<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Helpers\PortalHelper;
use App\Models\AcademicPeriod;
use App\Models\Assessment;
use App\Models\AssessmentScore;
use App\Models\Attendance;
use App\Models\BehaviorScore;
use App\Models\CourseModule;
use App\Models\Extracurricular;
use App\Models\Material;
use App\Models\Notification;
use App\Models\TeachingAssignment;
use Illuminate\Http\Request;

class SiswaController extends Controller
{
    private function resolve(Request $request): ?array
    {
        $user = $request->user();
        $student = $user->studentProfile;

        if (!$student || $student->status !== 'active') {
            return null;
        }

        $period = AcademicPeriod::where('is_active', true)->first();

        return [
            'student' => $student,
            'period' => $period,
            'initials' => strtoupper(mb_substr($student->full_name ?? 'S', 0, 1)),
        ];
    }

    private function computeGrades($student, $period): array
    {
        if (!$period) return [];

        $assignments = TeachingAssignment::where('period_id', $period->id)
            ->where('class_name', $student->class_name)
            ->with('subject', 'customSubject')
            ->get();

        $grades = [];

        foreach ($assignments as $assignment) {
            $assessments = Assessment::where('teaching_assignment_id', $assignment->id)
                ->whereNotNull('published_at')
                ->orderBy('assessment_date')
                ->get();

            $raw = ['quiz' => [], 'homework' => [], 'project' => [], 'uts' => 0, 'uas' => 0];

            foreach ($assessments as $assessment) {
                $scoreRecord = AssessmentScore::where('assessment_id', $assessment->id)
                    ->where('student_id', $student->id)
                    ->first();

                $score = $scoreRecord?->score;

                if ($score === null) continue;

                if ($assessment->component === 'uts' || $assessment->component === 'uas') {
                    $raw[$assessment->component] = max($raw[$assessment->component], (float) $score);
                } else {
                    $raw[$assessment->component][] = (float) $score;
                }
            }

            $componentScores = PortalHelper::componentScores($raw);
            $finalScore = PortalHelper::finalScore($raw);

            $subjectName = $assignment->subject?->name ?? $assignment->customSubject?->nama ?? '-';
            $subjectCode = $assignment->subject?->code ?? $assignment->customSubject?->kode ?? '-';
            $kkm = (float) ($assignment->subject?->kkm ?? $assignment->customSubject?->kkm ?? 0);

            $grades[] = [
                'subject' => $subjectName,
                'subject_code' => $subjectCode,
                'kkm' => $kkm,
                'components' => $componentScores,
                'final_score' => $finalScore,
                'letter' => PortalHelper::gradeLetter($finalScore),
                'passed' => $finalScore >= $kkm,
            ];
        }

        return $grades;
    }

    public function dashboard(Request $request)
    {
        $data = $this->resolve($request);
        if (!$data) return redirect()->route('login');

        $student = $data['student'];
        $period = $data['period'];

        $grades = $this->computeGrades($student, $period);
        $avgScore = $grades ? PortalHelper::average(array_column($grades, 'final_score')) : 0;

        $attendanceBreakdown = Attendance::where('student_id', $student->id)
            ->selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status')
            ->toArray();

        $totalAttendance = array_sum($attendanceBreakdown);
        $presentDays = $attendanceBreakdown['present'] ?? 0;
        $attendanceRate = $totalAttendance > 0 ? round($presentDays / $totalAttendance * 100, 1) : 0;

        $todaySchedule = collect();
        if ($period) {
            $assignments = TeachingAssignment::where('period_id', $period->id)
                ->where('class_name', $student->class_name)
                ->with('subject', 'customSubject')
                ->get();

            $dayMap = ['Monday' => 0, 'Tuesday' => 1, 'Wednesday' => 2, 'Thursday' => 3, 'Friday' => 4];
            $dayIndex = $dayMap[now()->format('l')] ?? null;
            $times = ['07:30–09:00', '09:15–10:45', '11:00–12:30'];

            if ($dayIndex !== null) {
                $dayAssignments = $assignments->filter(fn($ta, $i) => ($i % 5) === $dayIndex);
                $todaySchedule = $dayAssignments->values()->map(fn($ta, $i) => [
                    'subject' => $ta->subject?->name ?? $ta->customSubject?->nama ?? '-',
                    'slot' => $times[$i] ?? '-'
                ]);
            }
        }

        $notifications = Notification::where('student_id', $student->id)
            ->orderByDesc('created_at')
            ->limit(3)
            ->get();

        return view('siswa.dashboard', [
            'student' => $student,
            'period' => $period,
            'initials' => $data['initials'],
            'grades' => $grades,
            'avgScore' => round($avgScore, 1),
            'avgLetter' => PortalHelper::gradeLetter($avgScore),
            'attendanceBreakdown' => $attendanceBreakdown,
            'attendanceRate' => $attendanceRate,
            'totalAttendance' => $totalAttendance,
            'todaySchedule' => $todaySchedule,
            'notifications' => $notifications,
            'subjectCount' => count($grades),
        ]);
    }

    public function nilai(Request $request)
    {
        $data = $this->resolve($request);
        if (!$data) return redirect()->route('login');

        $grades = $this->computeGrades($data['student'], $data['period']);
        $avgScore = $grades ? PortalHelper::average(array_column($grades, 'final_score')) : 0;

        return view('siswa.nilai', [
            'student' => $data['student'],
            'period' => $data['period'],
            'initials' => $data['initials'],
            'grades' => $grades,
            'avgScore' => round($avgScore, 1),
            'avgLetter' => PortalHelper::gradeLetter($avgScore),
        ]);
    }

    public function kehadiran(Request $request)
    {
        $data = $this->resolve($request);
        if (!$data) return redirect()->route('login');

        $student = $data['student'];
        $breakdown = Attendance::where('student_id', $student->id)
            ->selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status')
            ->toArray();

        $total = array_sum($breakdown);
        $presentDays = $breakdown['present'] ?? 0;
        $rate = $total > 0 ? round($presentDays / $total * 100, 1) : 0;

        $recentAttendance = Attendance::where('student_id', $student->id)
            ->orderByDesc('attendance_date')
            ->limit(20)
            ->get();

        return view('siswa.kehadiran', [
            'student' => $student,
            'period' => $data['period'],
            'initials' => $data['initials'],
            'breakdown' => $breakdown,
            'total' => $total,
            'rate' => $rate,
            'recentAttendance' => $recentAttendance,
        ]);
    }

    public function jadwal(Request $request)
    {
        $data = $this->resolve($request);
        if (!$data) return redirect()->route('login');

        $student = $data['student'];
        $period = $data['period'];
        $days = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'];
        $times = ['07:30–09:00', '09:15–10:45', '11:00–12:30'];

        $assignments = TeachingAssignment::where('period_id', $period->id)
            ->where('class_name', $student->class_name)
            ->with('subject', 'customSubject')
            ->get();

        $grouped = $assignments->groupBy(fn($ta, $i) => $i % count($days));

        $jadwal = [];
        foreach ($days as $dayIndex => $day) {
            $group = $grouped[$dayIndex] ?? collect();
            $jadwal[$day] = $group->values()->map(fn($ta, $i) => [
                'subject' => $ta->subject?->name ?? $ta->customSubject?->nama ?? '-',
                'slot' => $i + 1,
            ]);
        }

        return view('siswa.jadwal', [
            'student' => $student,
            'period' => $period,
            'initials' => $data['initials'],
            'jadwal' => $jadwal,
            'days' => $days,
        ]);
    }

    public function materi(Request $request)
    {
        $data = $this->resolve($request);
        if (!$data) return redirect()->route('login');

        $student = $data['student'];
        $period = $data['period'];

        $assignmentIds = TeachingAssignment::where('period_id', $period->id)
            ->where('class_name', $student->class_name)
            ->pluck('id');

        $materials = Material::with('teachingAssignment.subject', 'teachingAssignment.customSubject', 'module')
            ->whereIn('teaching_assignment_id', $assignmentIds)
            ->orderBy('order')
            ->latest('id')
            ->get();

        $modules = CourseModule::with(['materials' => fn($q) => $q->orderBy('order')])
            ->whereIn('teaching_assignment_id', $assignmentIds)
            ->ordered()
            ->get();

        $grouped = $modules->mapWithKeys(fn($m) => [
            $m->id => [
                'module' => $m,
                'materials' => $m->materials,
            ]
        ]);

        $ungrouped = $materials->whereNull('module_id');

        return view('siswa.materi', [
            'student' => $student,
            'period' => $period,
            'initials' => $data['initials'],
            'materials' => $materials,
            'grouped' => $grouped,
            'ungrouped' => $ungrouped,
        ]);
    }

    public function profil(Request $request)
    {
        $data = $this->resolve($request);
        if (!$data) return redirect()->route('login');

        $student = $data['student'];
        $period = $data['period'];

        $behavior = BehaviorScore::where('student_id', $student->id)
            ->where('period_id', $period?->id)
            ->get();

        $extracurriculars = Extracurricular::where('student_id', $student->id)->get();

        return view('siswa.profil', [
            'student' => $student,
            'period' => $period,
            'initials' => $data['initials'],
            'behavior' => $behavior,
            'extracurriculars' => $extracurriculars,
        ]);
    }
}
