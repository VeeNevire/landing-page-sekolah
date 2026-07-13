<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\AcademicPeriod;
use App\Models\Attendance;
use App\Models\TeacherNote;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $students = $user->students()->where('status', 'active')->get();

        if ($students->isEmpty()) {
            return view('portal.dashboard', array_merge(
                $this->emptyData(),
                ['students' => collect(), 'selectedStudent' => null, 'selectedStudentId' => null, 'selectedStudentInitials' => 'S']
            ));
        }

        $studentId = $request->query('student_id', $students->first()->id);
        $selectedStudent = $students->firstWhere('id', $studentId) ?? $students->first();

        $demoStudent = $this->buildDemoStudent($selectedStudent);
        $subjects = $demoStudent['subjects'];
        $average = $demoStudent['average'];
        $attendanceRate = $demoStudent['attendanceRate'];
        $completion = $demoStudent['completion'];

        $history = $demoStudent['history'];
        $chartValues = array_map(fn($r) => (float) ($r['score'] ?? 0), $history);
        $minValue = $chartValues ? min($chartValues) - 4 : 70;
        $maxValue = $chartValues ? max($chartValues) + 3 : 100;
        $range = max(1, $maxValue - $minValue);

        $points = [];
        $labels = [];
        $count = max(1, count($history) - 1);
        foreach ($history as $index => $row) {
            $x = 55 + (($index / $count) * 620);
            $y = 220 - (((float) $row['score'] - $minValue) / $range * 165);
            $points[] = round($x, 1) . ',' . round($y, 1);
            $labels[] = ['x' => $x, 'label' => $row['label'], 'score' => $row['score'], 'y' => $y];
        }

        return view('portal.dashboard', [
            'students' => $students,
            'selectedStudent' => $selectedStudent,
            'selectedStudentId' => $selectedStudent->id,
            'selectedStudentInitials' => strtoupper(mb_substr($selectedStudent->full_name ?? 'S', 0, 1)),
            'demoStudent' => $demoStudent,
            'subjects' => $subjects,
            'average' => $average,
            'attendanceRate' => $attendanceRate,
            'completion' => $completion,
            'history' => $history,
            'chartValues' => $chartValues,
            'minValue' => $minValue,
            'maxValue' => $maxValue,
            'range' => $range,
            'points' => $points,
            'labels' => $labels,
        ]);
    }

    private function buildDemoStudent($student): array
    {
        $period = AcademicPeriod::where('is_active', true)->first();

        $subjects = $this->computeSubjects($student, $period);
        $average = $this->computeAverage($subjects);
        $attendanceData = $this->computeAttendance($student);
        $attendanceRate = $attendanceData['rate'];
        $completion = $this->computeCompletion($subjects);
        $history = $this->computeHistory($student);

        $note = TeacherNote::where('student_id', $student->id)
            ->where('visible_to_parent', true)
            ->latest('created_at')
            ->first();

        $behavior = $student->behaviorScores
            ->where('period_id', $period?->id)
            ->pluck('grade', 'aspect')
            ->toArray();

        $extracurricular = $student->extracurriculars->map(fn($e) => [
            'name' => $e->name,
            'score' => $e->score,
            'note' => $e->note,
        ])->toArray();

        $recentScores = $student->assessmentScores()
            ->whereHas('assessment.teachingAssignment', fn($q) => $q->where('period_id', $period?->id))
            ->whereNotNull('graded_at')
            ->latest('graded_at')
            ->take(4)
            ->get()
            ->map(fn($s) => [
                'icon' => match ($s->assessment->component) {
                    'quiz' => 'flask',
                    'homework' => 'check',
                    'project' => 'trophy',
                    default => 'note',
                },
                'title' => $s->assessment->title . ' dinilai',
                'time' => $s->graded_at->format('j M Y') . ' • skor ' . $s->score,
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
            'subjects' => $subjects,
            'average' => $average,
            'attendanceRate' => $attendanceRate,
            'completion' => $completion,
            'attendance' => $attendanceData['breakdown'],
            'behavior' => $behavior,
            'extracurricular' => $extracurricular,
            'teacher_note' => $note?->note ?? '',
            'history' => $history,
            'activities' => $recentScores,
        ];
    }

    private function computeSubjects($student, ?AcademicPeriod $period): array
    {
        $assignments = $student->class_name
            ? \App\Models\TeachingAssignment::where('class_name', $student->class_name)
                ->where('period_id', $period?->id)
                ->with('subject', 'teacher')
                ->get()
            : collect();

        $result = [];
        foreach ($assignments as $ta) {
            $assessments = $ta->assessments()->with('scores')->get();

            $grouped = [];
            foreach ($assessments as $a) {
                $scores = $a->scores->where('student_id', $student->id)->pluck('score')->filter()->values()->toArray();
                $grouped[$a->component][] = [
                    'assessment' => $a,
                    'scores' => $scores,
                ];
            }

            $componentAvgs = [];
            foreach (['quiz', 'homework', 'project', 'uts', 'uas'] as $comp) {
                $items = $grouped[$comp] ?? [];
                $allScores = [];
                foreach ($items as $item) {
                    $allScores = array_merge($allScores, $item['scores']);
                }
                $componentAvgs[$comp] = $allScores ? array_sum($allScores) / count($allScores) : 0;
            }

            $weights = ['quiz' => 0.15, 'homework' => 0.20, 'project' => 0.20, 'uts' => 0.20, 'uas' => 0.25];
            $finalScore = 0;
            foreach ($weights as $comp => $w) {
                $finalScore += $componentAvgs[$comp] * $w;
            }

            $quizScores = [];
            $hwScores = [];
            $projScores = [];
            foreach ($grouped['quiz'] ?? [] as $item) {
                $quizScores = array_merge($quizScores, $item['scores']);
            }
            foreach ($grouped['homework'] ?? [] as $item) {
                $hwScores = array_merge($hwScores, $item['scores']);
            }
            foreach ($grouped['project'] ?? [] as $item) {
                $projScores = array_merge($projScores, $item['scores']);
            }

            $result[] = [
                'code' => $ta->subject->code,
                'name' => $ta->subject->name,
                'teacher' => $ta->teacher->full_name ?? '-',
                'quiz' => $quizScores,
                'homework' => $hwScores,
                'project' => $projScores,
                'uts' => $componentAvgs['uts'],
                'uas' => $componentAvgs['uas'],
                'final' => round($finalScore, 1),
                'mastery' => match (true) {
                    $finalScore >= 85 => 'Sangat Baik',
                    $finalScore >= 75 => 'Baik',
                    $finalScore >= 65 => 'Cukup',
                    default => 'Perlu Remedial',
                },
                'note' => '',
            ];
        }

        return $result;
    }

    private function computeAverage(array $subjects): float
    {
        if (!$subjects) return 0;
        $values = array_map(fn($s) => $s['final'] ?? 0, $subjects);
        return round(array_sum($values) / count($values), 1);
    }

    private function computeAttendance($student): array
    {
        $breakdown = Attendance::where('student_id', $student->id)
            ->selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status')
            ->toArray();

        $total = array_sum($breakdown);
        $present = $breakdown['present'] ?? 0;
        $rate = $total > 0 ? round($present / $total * 100, 1) : 0;

        return [
            'breakdown' => [
                'present' => $breakdown['present'] ?? 0,
                'sick' => $breakdown['sick'] ?? 0,
                'excused' => $breakdown['excused'] ?? 0,
                'unexcused' => $breakdown['unexcused'] ?? 0,
                'late' => $breakdown['late'] ?? 0,
            ],
            'rate' => $rate,
        ];
    }

    private function computeCompletion(array $subjects): float
    {
        $total = 0;
        $completed = 0;
        foreach ($subjects as $subject) {
            $expected = max(1, count($subject['homework'] ?? []));
            $total += $expected;
            $completed += count(array_filter($subject['homework'] ?? [], fn($v) => $v > 0));
        }
        return $total > 0 ? round(($completed / $total) * 100, 1) : 0;
    }

    private function computeHistory($student): array
    {
        $periods = AcademicPeriod::orderBy('academic_year')->orderBy('semester')->get();
        $history = [];

        foreach ($periods as $p) {
            $assignments = \App\Models\TeachingAssignment::where('class_name', $student->class_name)
                ->where('period_id', $p->id)
                ->with('assessments.scores')
                ->get();

            $allScores = [];
            foreach ($assignments as $ta) {
                foreach ($ta->assessments as $a) {
                    $s = $a->scores->where('student_id', $student->id)->pluck('score')->filter()->toArray();
                    $allScores = array_merge($allScores, $s);
                }
            }

            if ($allScores) {
                $avg = round(array_sum($allScores) / count($allScores), 1);
                $label = str_replace(['Ganjil', 'Genap'], ['G', 'Gn'], $p->academic_year) . ' ' . ucfirst($p->semester);
                $history[] = ['label' => $label, 'score' => $avg];
            }
        }

        return $history ?: [['label' => 'Semester ini', 'score' => 0]];
    }

    private function emptyData(): array
    {
        return [
            'demoStudent' => null,
            'subjects' => [],
            'average' => 0,
            'attendanceRate' => 0,
            'completion' => 0,
            'history' => [],
            'chartValues' => [],
            'minValue' => 70,
            'maxValue' => 100,
            'range' => 30,
            'points' => [],
            'labels' => [],
        ];
    }
}
