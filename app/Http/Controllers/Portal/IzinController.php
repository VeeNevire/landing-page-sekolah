<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\AcademicPeriod;
use App\Models\LeaveRequest;
use App\Services\AuditService;
use Illuminate\Http\Request;

class IzinController extends Controller
{
    public function index(Request $request)
    {
        $data = $this->resolve($request);

        if (!$data) {
            return view('portal.izin', [
                'students' => collect(),
                'selectedStudent' => null,
                'selectedStudentId' => null,
                'selectedStudentInitials' => 'S',
                'demoStudent' => null,
                'requests' => collect(),
                'requestCounts' => ['pending' => 0, 'approved' => 0, 'rejected' => 0],
            ]);
        }

        $student = $data['selectedStudent'];

        $requests = LeaveRequest::where('student_id', $student->id)
            ->with('requester')
            ->orderByDesc('created_at')
            ->get();

        $requestCounts = [
            'pending' => $requests->where('status', 'pending')->count(),
            'approved' => $requests->where('status', 'approved')->count(),
            'rejected' => $requests->where('status', 'rejected')->count(),
        ];

        return view('portal.izin', array_merge($data, [
            'requests' => $requests,
            'requestCounts' => $requestCounts,
        ]));
    }

    public function store(Request $request)
    {
        $data = $this->resolve($request);

        if (!$data) {
            return back()->with('error', 'Sesi tidak valid.');
        }

        $user = $request->user();
        $studentIds = $user->students()->where('status', 'active')->pluck('id');

        $validated = $request->validate([
            'student_id' => 'required|integer|in:' . $studentIds->implode(','),
            'type' => 'required|in:sick,excused',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after_or_equal:start_date',
            'reason' => 'required|string|max:1000',
            'attachment' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ], [
            'student_id.in' => 'Siswa tidak terhubung dengan akun Anda.',
            'start_date.after_or_equal' => 'Tanggal mulai tidak boleh di masa lalu.',
            'end_date.after_or_equal' => 'Tanggal selesai harus sama atau setelah tanggal mulai.',
        ]);

        $student = $user->students()->where('status', 'active')->find($validated['student_id']);

        if (!$student) {
            return back()->with('error', 'Siswa tidak terhubung dengan akun Anda.');
        }

        $overlap = LeaveRequest::where('student_id', $student->id)
            ->whereIn('status', ['pending', 'approved'])
            ->where(function ($q) use ($validated) {
                $q->whereBetween('start_date', [$validated['start_date'], $validated['end_date']])
                    ->orWhereBetween('end_date', [$validated['start_date'], $validated['end_date']])
                    ->orWhere(function ($q2) use ($validated) {
                        $q2->where('start_date', '<=', $validated['start_date'])
                            ->where('end_date', '>=', $validated['end_date']);
                    });
            })
            ->exists();

        if ($overlap) {
            return back()->withInput()->with('error', 'Sudah ada pengajuan izin/sakit pada rentang tanggal tersebut.');
        }

        $attachmentPath = null;
        $attachmentName = null;
        $attachmentSize = null;

        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $attachmentPath = $file->store('izin/' . $student->id, 'public');
            $attachmentName = $file->getClientOriginalName();
            $attachmentSize = $file->getSize();
        }

        $leaveRequest = LeaveRequest::create([
            'student_id' => $student->id,
            'requested_by' => $user->id,
            'type' => $validated['type'],
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
            'reason' => $validated['reason'],
            'status' => 'pending',
            'attachment_path' => $attachmentPath,
            'attachment_name' => $attachmentName,
            'attachment_size' => $attachmentSize,
        ]);

        AuditService::log('leave-request.create', 'LeaveRequest', $leaveRequest->id, $student->full_name, $user->id);

        return back()->with('success', 'Pengajuan ' . $leaveRequest->type_label . ' berhasil dikirim dan menunggu persetujuan wali kelas.');
    }

    private function resolve(Request $request): ?array
    {
        $user = $request->user();
        $students = $user->students()->where('status', 'active')->get();

        if ($students->isEmpty()) {
            return null;
        }

        $studentId = $request->query('student_id', $students->first()->id);
        $selectedStudent = $students->firstWhere('id', $studentId) ?? $students->first();

        $period = AcademicPeriod::where('is_active', true)->first();

        return [
            'students' => $students,
            'selectedStudent' => $selectedStudent,
            'selectedStudentId' => $selectedStudent->id,
            'selectedStudentInitials' => strtoupper(mb_substr($selectedStudent->full_name ?? 'S', 0, 1)),
            'demoStudent' => [
                'id' => $selectedStudent->id,
                'name' => $selectedStudent->full_name,
                'initials' => strtoupper(mb_substr($selectedStudent->full_name, 0, 2)),
                'nisn' => $selectedStudent->nisn,
                'class' => $selectedStudent->class_name,
                'program' => $selectedStudent->program_name,
                'homeroom_teacher' => $selectedStudent->homeroomTeacher?->full_name ?? '-',
                'semester' => $period?->semester === 'ganjil' ? 'Ganjil' : 'Genap',
                'academic_year' => $period?->academic_year ?? '-',
            ],
        ];
    }
}
