<?php

namespace App\Services;

use App\Models\Attendance;
use App\Models\Student;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class RfidService
{
    public function locked(callable $operation): mixed
    {
        return DB::transaction(function () use ($operation) {
            $state = DB::table('rfid_reader_state')->where('id', 1)->lockForUpdate()->first();
            abort_unless($state, 503, 'Migrasi RFID belum dijalankan.');
            return $operation($state);
        }, 3);
    }

    public function active(object $state): bool
    {
        return $state->session_id && $state->student_id && $state->owner_id
            && $state->expires_at && now()->lt($state->expires_at);
    }

    public function clear(): void
    {
        DB::table('rfid_reader_state')->where('id', 1)->update([
            'session_id' => null, 'student_id' => null, 'owner_id' => null,
            'uid' => null, 'expires_at' => null,
        ]);
    }

    public function start(int $studentId, int $ownerId): string
    {
        return $this->locked(function ($state) use ($studentId, $ownerId) {
            abort_if($this->active($state), 409, 'Pembaca sedang digunakan untuk registrasi. Batalkan sesi atau tunggu hingga berakhir.');
            Student::where('status', 'active')->findOrFail($studentId);
            $id = (string) Str::uuid();
            DB::table('rfid_reader_state')->where('id', 1)->update([
                'session_id' => $id, 'student_id' => $studentId, 'owner_id' => $ownerId,
                'uid' => null, 'expires_at' => now()->addMinutes(2),
            ]);
            return $id;
        });
    }

    public function finish(string $session, int $owner, bool $confirm, ?string $expectedUid): ?Student
    {
        return $this->locked(function ($state) use ($session, $owner, $confirm, $expectedUid) {
            abort_unless($state->session_id === $session && (int) $state->owner_id === $owner, 409, 'Sesi registrasi sudah berubah.');
            $student = null;
            if ($confirm) {
                abort_unless($this->active($state) && $state->uid && $state->uid === $expectedUid, 409, 'Sesi berakhir atau UID belum sesuai. Mulai scan kembali.');
                $student = Student::where('status', 'active')->lockForUpdate()->findOrFail($state->student_id);
                abort_if(Student::where('rfid_uid', $state->uid)->where('id', '!=', $student->id)->exists(), 422, 'Kartu sudah terdaftar ke siswa lain. Lepaskan kartu tersebut dahulu.');
                $student->update(['rfid_uid' => $state->uid]);
                AuditService::log('rfid.card_register', 'Student', $student->id, $student->full_name, $owner);
            }
            $this->clear();
            return $student;
        });
    }

    public function scan(string $uid): array
    {
        return $this->locked(function ($state) use ($uid) {
            if ($this->active($state)) {
                if (! $state->uid) {
                    DB::table('rfid_reader_state')->where('id', 1)->update(['uid' => $uid]);
                }
                return $this->result(true, 'registration', 'Kartu ditangkap. Konfirmasi registrasi di Admin.');
            }
            $this->clear();
            $student = Student::where('rfid_uid', $uid)->where('status', 'active')->lockForUpdate()->first();
            if (! $student) {
                return $this->result(false, 'unknown_card', 'Kartu belum terdaftar atau siswa tidak aktif.', 404);
            }
            $date = today()->toDateString();
            if (Attendance::where('student_id', $student->id)->where('attendance_date', $date)->exists()) {
                return $this->result(true, 'already_recorded', 'Kehadiran hari ini sudah tercatat.');
            }
            $recorder = User::where('is_active', true)->whereIn('role', ['admin', 'principal'])
                ->when(config('rfid.recorded_by_user_id'), fn ($q, $id) => $q->whereKey($id))->orderBy('id')->first();
            if (! $recorder) {
                return $this->result(false, 'configuration_error', 'Petugas RFID belum dikonfigurasi.', 503);
            }
            $attendance = Attendance::firstOrCreate(['student_id' => $student->id, 'attendance_date' => $date], [
                'status' => 'present', 'source' => 'rfid', 'check_in_at' => now(), 'recorded_by' => $recorder->id,
            ]);
            if ($attendance->wasRecentlyCreated) {
                AuditService::log('attendance.rfid_scan', 'Attendance', $attendance->id, $student->full_name, $recorder->id);
            }
            return $this->result(true, $attendance->wasRecentlyCreated ? 'attendance' : 'already_recorded',
                $attendance->wasRecentlyCreated ? 'Kehadiran berhasil dicatat: '.$student->full_name : 'Kehadiran hari ini sudah tercatat.');
        });
    }

    private function result(bool $success, string $mode, string $message, int $status = 200): array
    {
        DB::table('rfid_reader_state')->where('id', 1)->update(['last_message' => $message, 'last_seen_at' => now()]);
        return compact('success', 'mode', 'message', 'status');
    }
}
