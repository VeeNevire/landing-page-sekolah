<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\AcademicPeriod;
use App\Models\Attendance;
use App\Models\TeacherNote;
use App\Models\TeachingAssignment;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $students = $user->students()->where('status', 'active')->get();

        if ($students->isEmpty()) {
            return view('portal.laporan', array_merge(
                $this->emptyData(),
                ['students' => collect(), 'selectedStudent' => null, 'selectedStudentId' => null, 'selectedStudentInitials' => 'S']
            ));
        }

        $studentId = $request->query('student_id', $students->first()->id);
        $selectedStudent = $students->firstWhere('id', $studentId) ?? $students->first();

        $demoStudent = $this->buildDemoStudent($selectedStudent);

        return view('portal.laporan', [
            'students' => $students,
            'selectedStudent' => $selectedStudent,
            'selectedStudentId' => $selectedStudent->id,
            'selectedStudentInitials' => strtoupper(mb_substr($selectedStudent->full_name ?? 'S', 0, 1)),
            'demoStudent' => $demoStudent,
            'subjects' => $demoStudent['subjects'],
            'average' => $demoStudent['average'],
            'attendanceRate' => $demoStudent['attendanceRate'],
        ]);
    }

    public function exportCsv(Request $request)
    {
        $user = $request->user();
        $studentId = $request->query('student_id');
        $student = $user->students()->where('status', 'active')->findOrFail($studentId);

        $demoStudent = $this->buildDemoStudent($student);
        $subjects = $demoStudent['subjects'];

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="rapor_' . $student->nisn . '.csv"',
        ];

        $callback = function () use ($student, $subjects) {
            $output = fopen('php://output', 'w');
            fprintf($output, chr(0xEF) . chr(0xBB) . chr(0xBF));

            fputcsv($output, ['Laporan Nilai - ' . $student->full_name]);
            fputcsv($output, ['Kelas: ' . $student->class_name, 'Program: ' . $student->program_name]);
            fputcsv($output, []);

            fputcsv($output, ['Mata Pelajaran', 'Kuis', 'PR/Tugas', 'Proyek', 'UTS', 'UAS', 'Nilai Akhir', 'Grade', 'KKM', 'Status']);
            foreach ($subjects as $subject) {
                $final = $subject['final'] ?? 0;
                $grade = $this->gradeLetter($final);
                $pass = $final >= 75;
                fputcsv($output, [
                    $subject['name'],
                    number_format($this->avg($subject['quiz']), 1, ',', '.'),
                    number_format($this->avg($subject['homework']), 1, ',', '.'),
                    number_format($this->avg($subject['project']), 1, ',', '.'),
                    number_format($subject['uts'], 1, ',', '.'),
                    number_format($subject['uas'], 1, ',', '.'),
                    number_format($final, 1, ',', '.'),
                    $grade,
                    75,
                    $pass ? 'Tuntas' : 'Perlu Remedial',
                ]);
            }

            fclose($output);
        };

        return response()->stream($callback, 200, $headers);
    }

    private function buildDemoStudent($student): array
    {
        $period = AcademicPeriod::where('is_active', true)->first();

        $subjects = $this->computeSubjects($student, $period);
        $average = $this->computeAverage($subjects);

        $attendanceData = Attendance::where('student_id', $student->id)
            ->selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status')
            ->toArray();

        $total = array_sum($attendanceData);
        $present = $attendanceData['present'] ?? 0;
        $attendanceRate = $total > 0 ? round($present / $total * 100, 1) : 0;

        $behavior = $student->behaviorScores
            ->where('period_id', $period?->id)
            ->pluck('grade', 'aspect')
            ->toArray();

        $extracurricular = $student->extracurriculars->map(fn($e) => [
            'name' => $e->name,
            'score' => $e->score,
            'note' => $e->note,
        ])->toArray();

        $note = TeacherNote::where('student_id', $student->id)
            ->where('visible_to_parent', true)
            ->latest('created_at')
            ->first();

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
            'attendance' => [
                'present' => $attendanceData['present'] ?? 0,
                'sick' => $attendanceData['sick'] ?? 0,
                'excused' => $attendanceData['excused'] ?? 0,
                'unexcused' => $attendanceData['unexcused'] ?? 0,
            ],
            'behavior' => $behavior,
            'extracurricular' => $extracurricular,
            'teacher_note' => $note?->note ?? '',
        ];
    }

    private function computeSubjects($student, ?AcademicPeriod $period): array
    {
        $assignments = TeachingAssignment::where('class_name', $student->class_name)
            ->where('period_id', $period?->id)
            ->with('subject', 'customSubject', 'teacher')
            ->get();

        $result = [];
        foreach ($assignments as $ta) {
            $assessments = $ta->assessments()->with('scores')->get();

            $grouped = [];
            foreach ($assessments as $a) {
                $scores = $a->scores->where('student_id', $student->id)->pluck('score')->filter()->values()->toArray();
                $grouped[$a->component][] = $scores;
            }

            $componentAvgs = [];
            foreach (['quiz', 'homework', 'project', 'uts', 'uas'] as $comp) {
                $all = [];
                foreach ($grouped[$comp] ?? [] as $scores) {
                    $all = array_merge($all, $scores);
                }
                $componentAvgs[$comp] = $all ? array_sum($all) / count($all) : 0;
            }

            $weights = ['quiz' => 0.15, 'homework' => 0.20, 'project' => 0.20, 'uts' => 0.20, 'uas' => 0.25];
            $finalScore = 0;
            foreach ($weights as $comp => $w) {
                $finalScore += $componentAvgs[$comp] * $w;
            }

            $quizScores = [];
            $hwScores = [];
            $projScores = [];
            foreach ($grouped['quiz'] ?? [] as $s) { $quizScores = array_merge($quizScores, $s); }
            foreach ($grouped['homework'] ?? [] as $s) { $hwScores = array_merge($hwScores, $s); }
            foreach ($grouped['project'] ?? [] as $s) { $projScores = array_merge($projScores, $s); }

            $result[] = [
                'code' => $ta->subject?->code ?? $ta->customSubject?->kode ?? '-',
                'name' => $ta->subject?->name ?? $ta->customSubject?->nama ?? '-',
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

    private function avg(array $values): float
    {
        return $values ? array_sum($values) / count($values) : 0;
    }

    private function gradeLetter(float $score): string
    {
        return match (true) {
            $score >= 90 => 'A',
            $score >= 85 => 'A-',
            $score >= 80 => 'B+',
            $score >= 75 => 'B',
            $score >= 70 => 'C+',
            $score >= 65 => 'C',
            default => 'D',
        };
    }

    private function emptyData(): array
    {
        return [
            'demoStudent' => [],
            'subjects' => [],
            'average' => 0,
            'attendanceRate' => 0,
        ];
    }
}
