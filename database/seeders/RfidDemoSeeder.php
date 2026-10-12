<?php

namespace Database\Seeders;

use App\Models\Attendance;
use App\Models\Jurusan;
use App\Models\Kelas;
use App\Models\Student;
use App\Models\User;
use App\Services\RfidNotificationService;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

/**
 * Data demo untuk mencoba Absensi RFID: wali kelas, siswa (sebagian sudah punya kartu),
 * orang tua, riwayat absensi 10 hari sekolah, dan absensi RFID hari ini.
 * Aman dijalankan ulang: tidak menghapus data lain. Semua akun memakai password Demo123!
 *
 * php artisan db:seed --class=RfidDemoSeeder
 */
class RfidDemoSeeder extends Seeder
{
    private const PASSWORD = 'Demo123!';

    // Kelas => [wali kelas, daftar siswa [nama, jenis kelamin L/P, UID kartu atau null]]
    private const CLASSES = [
        ['RPL', 10, 'RPL 1', ['Siti Nurhaliza, S.Kom.', 'walas.xrpl1@demo.sch.id'], [
            ['Andi Saputra', '04:A1:B2:C3'], ['Bunga Lestari', '04:A1:B2:C4'], ['Cahyo Wibowo', '04:A1:B2:C5'],
            ['Dewi Anggraini', '04:A1:B2:C6'], ['Eko Prasetyo', null], ['Fitri Handayani', null],
        ]],
        ['RPL', 11, 'RPL 1', ['Agus Hermawan, S.T.', 'walas.xirpl1@demo.sch.id'], [
            ['Gilang Ramadhan', '04:D1:E2:F3'], ['Hana Safitri', '04:D1:E2:F4'], ['Irfan Maulana', '04:D1:E2:F5'],
            ['Jihan Aulia', '04:D1:E2:F6'], ['Kevin Pratama', null], ['Laras Wulandari', null],
        ]],
        ['DKV', 10, 'DKV 1', ['Maya Puspita, S.Ds.', 'walas.xdkv1@demo.sch.id'], [
            ['Muhammad Rizki', '05:11:22:33'], ['Nadia Putri', '05:11:22:34'], ['Oki Setiawan', '05:11:22:35'],
            ['Putri Ayu', '05:11:22:36'], ['Raka Aditya', null], ['Salsabila Zahra', null],
        ]],
        ['AKL', 11, 'AKL 1', ['Hendra Gunawan, S.E.', 'walas.xiakl1@demo.sch.id'], [
            ['Taufik Hidayat', '06:AA:BB:01'], ['Umi Kalsum', '06:AA:BB:02'], ['Vino Bastian', '06:AA:BB:03'],
            ['Wulan Sari', '06:AA:BB:04'], ['Yoga Pratama', null], ['Zahra Amelia', null],
        ]],
    ];

    public function run(): void
    {
        mt_srand(20261012); // hasil acak sama setiap kali dijalankan
        if (Jurusan::count() === 0) {
            $this->call(JurusanSeeder::class);
        }
        $password = Hash::make(self::PASSWORD);
        $admin = User::where('role', 'admin')->orderBy('id')->first();

        $students = [];
        $seq = 1;
        foreach (self::CLASSES as $ci => [$kode, $tingkat, $namaKelas, [$walasName, $walasEmail], $rows]) {
            $walas = User::updateOrCreate(['email' => $walasEmail], [
                'name' => $walasName, 'full_name' => $walasName, 'password' => $password,
                'role' => 'homeroom', 'is_active' => true, 'email_verified_at' => now(),
            ]);
            $jurusan = Jurusan::where('kode', $kode)->first();
            $kelas = Kelas::firstOrCreate(
                ['jurusan_id' => $jurusan?->id, 'tingkat' => $tingkat, 'nama' => $namaKelas],
                ['is_active' => true],
            );
            $kelas->update(['homeroom_teacher_id' => $walas->id]);

            foreach ($rows as $si => [$name, $uid]) {
                $nis = '2026'.str_pad((string) $seq, 4, '0', STR_PAD_LEFT);
                $slug = strtolower(str_replace(' ', '.', $name));
                $user = User::updateOrCreate(['email' => $slug.'@siswa.demo.sch.id'], [
                    'name' => $name, 'full_name' => $name, 'password' => $password,
                    'role' => 'student', 'is_active' => true, 'email_verified_at' => now(),
                ]);
                $student = Student::updateOrCreate(['nis' => $nis], [
                    'user_id' => $user->id,
                    'nisn' => '00'.(81000000 + $seq * 137),
                    'rfid_uid' => $uid,
                    'full_name' => $name,
                    'birth_date' => Carbon::create(2026 - $tingkat - 6, 1 + ($seq % 12), 1 + ($seq * 3 % 27)),
                    'class_name' => $kelas->nama_lengkap,
                    'program_name' => $jurusan?->nama ?? $kode,
                    'jurusan_id' => $jurusan?->id,
                    'kelas_id' => $kelas->id,
                    'homeroom_teacher_id' => $walas->id,
                    'status' => 'active',
                ]);
                $students[] = $student;
                $seq++;
            }
        }

        $this->seedParents($students, $password);
        $this->seedAttendance($students, $admin);

        $this->command?->info('Data demo RFID siap: '.count($students).' siswa, password semua akun: '.self::PASSWORD);
    }

    /** Satu orang tua per siswa, kecuali dua pasang kakak-adik yang berbagi orang tua. */
    private function seedParents(array $students, string $password): void
    {
        $siblings = [6 => 0, 18 => 12]; // index siswa => index saudara yang orang tuanya dipakai
        $parents = [];
        foreach ($students as $i => $student) {
            if (isset($siblings[$i])) {
                $parent = $parents[$siblings[$i]];
            } else {
                $last = last(explode(' ', $student->full_name));
                $isAyah = $i % 2 === 0;
                $name = ($isAyah ? 'Bapak ' : 'Ibu ').$last;
                $parent = User::updateOrCreate(['email' => 'ortu.'.strtolower($last).'@demo.sch.id'], [
                    'name' => $name, 'full_name' => $name, 'password' => $password,
                    'role' => 'parent', 'is_active' => true, 'email_verified_at' => now(),
                ]);
            }
            $parents[$i] = $parent;
            DB::table('parent_student')->updateOrInsert(
                ['parent_id' => $parent->id, 'student_id' => $student->id],
                ['relationship' => str_starts_with($parent->name, 'Bapak') ? 'Ayah' : 'Ibu', 'is_primary' => true],
            );
        }
    }

    /** Riwayat 10 hari sekolah terakhir + sebagian siswa berkartu sudah tap hari ini. */
    private function seedAttendance(array $students, ?User $recorder): void
    {
        if (! $recorder) {
            $this->command?->warn('Tidak ada akun admin; absensi dilewati.');

            return;
        }
        $notifier = app(RfidNotificationService::class);
        $days = [];
        for ($d = today()->subDay(); count($days) < 10; $d = $d->copy()->subDay()) {
            if ($d->isWeekday()) {
                $days[] = $d->copy();
            }
        }

        foreach ($students as $i => $student) {
            foreach ($days as $day) {
                $roll = mt_rand(1, 100);
                $status = match (true) {
                    $roll <= 85 => 'present',
                    $roll <= 90 => 'late',
                    $roll <= 95 => 'sick',
                    default => 'excused',
                };
                $viaRfid = $student->rfid_uid && in_array($status, ['present', 'late'], true);
                $checkIn = $viaRfid
                    ? $day->copy()->setTime($status === 'late' ? 7 : 6, $status === 'late' ? mt_rand(16, 45) : mt_rand(20, 59), mt_rand(0, 59))
                    : null;
                $this->record($student, $day, $status, $checkIn, $viaRfid, $recorder, $notifier);
            }

            // Hari ini: 2 siswa berkartu pertama di tiap kelas sudah tap; sisanya bisa dicoba di reader.
            if ($student->rfid_uid && $i % 6 < 2) {
                $checkIn = now()->copy()->setTime(6, 30 + $i % 6 * 7, mt_rand(0, 59));
                if ($checkIn->isFuture()) {
                    $checkIn = now()->subMinutes(5 - $i % 6);
                }
                $this->record($student, today(), 'present', $checkIn, true, $recorder, $notifier);
            }
        }
        // Contoh izin manual hari ini (tidak lewat RFID).
        $this->record($students[5], today(), 'sick', null, false, $recorder, $notifier, 'Demam, surat dari orang tua.');
    }

    private function record(Student $student, Carbon $day, string $status, ?Carbon $checkIn, bool $viaRfid,
        User $recorder, RfidNotificationService $notifier, ?string $note = null): void
    {
        $attendance = Attendance::firstOrCreate(
            ['student_id' => $student->id, 'attendance_date' => $day->toDateString()],
            ['status' => $status, 'check_in_at' => $checkIn, 'source' => $viaRfid ? 'rfid' : null,
                'note' => $note, 'recorded_by' => $recorder->id],
        );
        // Notifikasi orang tua dibuat sama seperti saat kartu benar-benar ditap.
        if ($attendance->wasRecentlyCreated && $viaRfid) {
            $notifier->record($attendance->load('student', 'recorder'));
        }
    }
}
