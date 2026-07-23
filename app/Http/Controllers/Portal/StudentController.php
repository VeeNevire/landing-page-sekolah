<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\AcademicPeriod;
use App\Models\Attendance;
use App\Models\Billing;
use App\Models\Notification;
use App\Models\TeachingAssignment;
use Illuminate\Http\Request;

class StudentController extends Controller
{
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
            'demoStudent' => $this->buildProfile($selectedStudent, $period),
        ];
    }

    public function kehadiran(Request $request)
    {
        $data = $this->resolve($request);
        if (!$data) {
            return view('portal.kehadiran', array_merge(
                $data ?? [],
                ['students' => collect(), 'selectedStudent' => null, 'selectedStudentId' => null, 'selectedStudentInitials' => 'S']
            ));
        }

        $student = $data['selectedStudent'];
        $breakdown = Attendance::where('student_id', $student->id)
            ->selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status')
            ->toArray();

        $attendance = [
            'present' => $breakdown['present'] ?? 0,
            'sick' => $breakdown['sick'] ?? 0,
            'excused' => $breakdown['excused'] ?? 0,
            'unexcused' => $breakdown['unexcused'] ?? 0,
            'late' => $breakdown['late'] ?? 0,
        ];

        $total = array_sum($attendance);
        $attendanceRate = $total > 0 ? round(($attendance['present'] ?? 0) / $total * 100, 1) : 0;

        return view('portal.kehadiran', array_merge($data, [
            'attendance' => $attendance,
            'attendanceRate' => $attendanceRate,
            'total' => $total,
        ]));
    }

    public function jadwal(Request $request)
    {
        $data = $this->resolve($request);
        if (!$data) {
            return view('portal.jadwal', array_merge(
                $data ?? [],
                ['students' => collect(), 'selectedStudent' => null, 'selectedStudentId' => null, 'selectedStudentInitials' => 'S', 'schedule' => []]
            ));
        }

        $student = $data['selectedStudent'];
        $period = AcademicPeriod::where('is_active', true)->first();

        $assignments = TeachingAssignment::where('class_name', $student->class_name)
            ->where('period_id', $period?->id)
            ->with('subject', 'customSubject', 'teacher')
            ->get();

        $days = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'];
        $times = ['07:30–09:00', '09:15–10:45', '11:00–12:30'];
        $schedule = [];

        $grouped = $assignments->groupBy(fn($ta, $i) => $i % count($days));

        foreach ($days as $dayIndex => $day) {
            $dayAssignments = $grouped[$dayIndex] ?? collect();
            foreach ($dayAssignments as $idx => $ta) {
                $schedule[] = [
                    'day' => $day,
                    'time' => $times[$idx] ?? '07:30–09:00',
                    'subject' => $ta->subject?->name ?? $ta->customSubject?->nama ?? '-',
                    'teacher' => $ta->teacher->full_name ?? '-',
                ];
            }
        }

        return view('portal.jadwal', array_merge($data, [
            'schedule' => $schedule,
        ]));
    }

    public function tagihan(Request $request)
    {
        $data = $this->resolve($request);
        if (!$data) {
            return view('portal.tagihan', array_merge(
                $data ?? [],
                ['students' => collect(), 'selectedStudent' => null, 'selectedStudentId' => null, 'selectedStudentInitials' => 'S', 'billing' => []]
            ));
        }

        $student = $data['selectedStudent'];
        $billings = Billing::where('student_id', $student->id)->orderBy('due_date')->get();

        $billing = $billings->map(fn($b) => [
            'id' => $b->id,
            'name' => $b->name,
            'amount' => (int) $b->amount,
            'date' => $b->due_date->format('Y-m-d'),
            'status' => $b->status,
        ])->toArray();

        $totalAmount = array_sum(array_column($billing, 'amount'));
        $paidAmount = array_sum(array_map(
            fn($b) => $b['amount'],
            array_filter($billing, fn($b) => $b['status'] === 'lunas')
        ));
        $unpaidAmount = $totalAmount - $paidAmount;

        return view('portal.tagihan', array_merge($data, [
            'billing' => $billing,
            'totalAmount' => $totalAmount,
            'paidAmount' => $paidAmount,
            'unpaidAmount' => $unpaidAmount,
        ]));
    }

    public function tagihanBayar(Request $request, Billing $billing)
    {
        $data = $this->resolve($request);
        if (!$data) {
            return response()->json(['success' => false, 'message' => 'Sesi tidak valid.'], 400);
        }

        $student = $data['selectedStudent'];
        if ($billing->student_id !== $student->id) {
            return response()->json(['success' => false, 'message' => 'Tagihan tidak ditemukan.'], 404);
        }

        if ($billing->status === 'lunas') {
            return response()->json(['success' => false, 'message' => 'Tagihan ini sudah lunas.'], 400);
        }

        $billing->update([
            'status' => 'lunas',
            'paid_date' => now(),
        ]);

        return response()->json(['success' => true, 'message' => 'Pembayaran berhasil.']);
    }

    public function profil(Request $request)
    {
        $data = $this->resolve($request);
        if (!$data) {
            return view('portal.profil', array_merge(
                $data ?? [],
                ['students' => collect(), 'selectedStudent' => null, 'selectedStudentId' => null, 'selectedStudentInitials' => 'S']
            ));
        }

        return view('portal.profil', $data);
    }

    public function notifikasi(Request $request)
    {
        $data = $this->resolve($request);
        if (!$data) {
            return view('portal.notifikasi', array_merge(
                $data ?? [],
                ['students' => collect(), 'selectedStudent' => null, 'selectedStudentId' => null, 'selectedStudentInitials' => 'S', 'notifications' => []]
            ));
        }

        $student = $data['selectedStudent'];
        $notifications = Notification::where('student_id', $student->id)
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(fn($n) => [
                'type' => $n->type,
                'title' => $n->title,
                'body' => $n->body,
                'date' => $n->created_at->format('Y-m-d'),
            ])
            ->toArray();

        return view('portal.notifikasi', array_merge($data, [
            'notifications' => $notifications,
        ]));
    }

    private function buildProfile($student, ?AcademicPeriod $period): array
    {
        $behavior = $student->behaviorScores
            ->where('period_id', $period?->id)
            ->pluck('grade', 'aspect')
            ->toArray();

        $extracurricular = $student->extracurriculars->map(fn($e) => [
            'name' => $e->name,
            'score' => $e->score,
            'note' => $e->note,
        ])->toArray();

        return [
            'id' => $student->id,
            'name' => $student->full_name,
            'initials' => strtoupper(mb_substr($student->full_name, 0, 2)),
            'nisn' => $student->nisn,
            'class' => $student->class_name,
            'program' => $student->program_name,
            'homeroom_teacher' => $student->homeroomTeacher?->full_name ?? '-',
            'academic_year' => $period?->academic_year ?? '-',
            'semester' => $period?->semester === 'ganjil' ? 'Ganjil' : 'Genap',
            'kkm' => 75,
            'behavior' => $behavior,
            'extracurricular' => $extracurricular,
        ];
    }
}
