<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Kelas;
use App\Models\LeaveRequest;
use App\Models\Notification;
use App\Models\Student;
use App\Services\AuditService;
use Illuminate\Http\Request;

class WaliIzinController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $kelas = Kelas::where('homeroom_teacher_id', $user->id)->first();

        $requests = collect();
        $counts = ['pending' => 0, 'approved' => 0, 'rejected' => 0];

        if ($kelas) {
            $studentIds = Student::where('class_name', $kelas->nama_lengkap)
                ->where('status', 'active')
                ->pluck('id');

            $requests = LeaveRequest::whereIn('student_id', $studentIds)
                ->with('student', 'requester')
                ->orderByRaw("FIELD(status, 'pending', 'approved', 'rejected')")
                ->orderByDesc('created_at')
                ->get();

            $counts['pending'] = $requests->where('status', 'pending')->count();
            $counts['approved'] = $requests->where('status', 'approved')->count();
            $counts['rejected'] = $requests->where('status', 'rejected')->count();
        }

        return view('guru.wali.izin', [
            'kelas' => $kelas,
            'requests' => $requests,
            'counts' => $counts,
        ]);
    }

    public function approve(Request $request, LeaveRequest $leaveRequest)
    {
        $this->authorizeRequest($request->user(), $leaveRequest);

        if ($leaveRequest->status !== 'pending') {
            return back()->with('error', 'Pengajuan ini sudah diproses.');
        }

        $responseNote = $request->input('response_note');

        foreach ($this->dateRange($leaveRequest->start_date, $leaveRequest->end_date) as $date) {
            Attendance::firstOrCreate(
                ['student_id' => $leaveRequest->student_id, 'attendance_date' => $date],
                [
                    'status' => $leaveRequest->type,
                    'recorded_by' => $request->user()->id,
                    'note' => $leaveRequest->reason,
                ]
            );
        }

        $leaveRequest->update([
            'status' => 'approved',
            'response_note' => $responseNote,
            'approved_by' => $request->user()->id,
            'responded_at' => now(),
        ]);

        $this->notify($leaveRequest, 'success', 'disetujui', $responseNote);
        AuditService::log('leave-request.approve', 'LeaveRequest', $leaveRequest->id, $leaveRequest->student?->full_name, $request->user()->id);

        return back()->with('success', 'Pengajuan ' . $leaveRequest->type_label . ' ' . $leaveRequest->student?->full_name . ' disetujui dan absensi tercatat.');
    }

    public function reject(Request $request, LeaveRequest $leaveRequest)
    {
        $this->authorizeRequest($request->user(), $leaveRequest);

        if ($leaveRequest->status !== 'pending') {
            return back()->with('error', 'Pengajuan ini sudah diproses.');
        }

        $responseNote = $request->input('response_note');

        $leaveRequest->update([
            'status' => 'rejected',
            'response_note' => $responseNote,
            'approved_by' => $request->user()->id,
            'responded_at' => now(),
        ]);

        $this->notify($leaveRequest, 'warning', 'ditolak', $responseNote);
        AuditService::log('leave-request.reject', 'LeaveRequest', $leaveRequest->id, $leaveRequest->student?->full_name, $request->user()->id);

        return back()->with('success', 'Pengajuan ' . $leaveRequest->type_label . ' ' . $leaveRequest->student?->full_name . ' ditolak.');
    }

    private function authorizeRequest($user, LeaveRequest $leaveRequest): void
    {
        $kelas = Kelas::where('homeroom_teacher_id', $user->id)->first();
        abort_unless($kelas, 403, 'Anda belum ditetapkan sebagai wali kelas.');

        $belongsToClass = Student::where('class_name', $kelas->nama_lengkap)
            ->where('id', $leaveRequest->student_id)
            ->exists();

        abort_unless($belongsToClass, 403, 'Siswa ini bukan bagian dari kelas binaan Anda.');
    }

    private function dateRange(\Carbon\Carbon $start, \Carbon\Carbon $end): array
    {
        $dates = [];
        for ($date = $start->copy(); $date->lte($end); $date->addDay()) {
            $dates[] = $date->toDateString();
        }
        return $dates;
    }

    private function notify(LeaveRequest $leaveRequest, string $type, string $action, ?string $note): void
    {
        $range = $leaveRequest->start_date->format('d M') . ' - ' . $leaveRequest->end_date->format('d M Y');
        $body = "Pengajuan {$leaveRequest->type_label} ({$range}) telah {$action}.";
        if ($note) {
            $body .= " Catatan wali kelas: {$note}";
        }

        Notification::create([
            'student_id' => $leaveRequest->student_id,
            'type' => $type,
            'title' => "Izin {$leaveRequest->type_label} {$action}",
            'body' => $body,
        ]);
    }
}
