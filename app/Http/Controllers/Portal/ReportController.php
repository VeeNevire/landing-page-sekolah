<?php

namespace App\Http\Controllers\Portal;

use App\Helpers\PortalHelper;
use App\Http\Controllers\Controller;
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

        $demoStudent = $this->findDemoStudent($selectedStudent->nisn ?? '');
        $subjects = PortalHelper::getStudentGrades($demoStudent['id'] ?? '');
        $average = PortalHelper::overallAverage($subjects);
        $attendanceRate = PortalHelper::attendanceRate($demoStudent['attendance'] ?? []);

        return view('portal.laporan', [
            'students' => $students,
            'selectedStudent' => $selectedStudent,
            'selectedStudentId' => $selectedStudent->id,
            'selectedStudentInitials' => strtoupper(substr($selectedStudent->full_name ?? 'S', 0, 1)),
            'demoStudent' => $demoStudent,
            'subjects' => $subjects,
            'average' => $average,
            'attendanceRate' => $attendanceRate,
        ]);
    }

    public function exportCsv(Request $request)
    {
        $user = $request->user();
        $studentId = $request->query('student_id');
        $student = $user->students()->where('status', 'active')->findOrFail($studentId);

        $demoStudent = $this->findDemoStudent($student->nisn ?? '');
        $subjects = PortalHelper::getStudentGrades($demoStudent['id'] ?? '');

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
                $scores = PortalHelper::componentScores($subject);
                $final = PortalHelper::finalScore($subject);
                $grade = PortalHelper::gradeLetter($final);
                $pass = $final >= 75;
                fputcsv($output, [
                    $subject['name'],
                    number_format($scores['quiz'], 1, ',', '.'),
                    number_format($scores['homework'], 1, ',', '.'),
                    number_format($scores['project'], 1, ',', '.'),
                    number_format($scores['uts'], 1, ',', '.'),
                    number_format($scores['uas'], 1, ',', '.'),
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

    private function findDemoStudent(?string $nisn): array
    {
        $all = PortalHelper::loadStudents();
        foreach ($all as $ds) {
            if (($ds['nisn'] ?? '') === $nisn) return $ds;
        }
        return $all[0] ?? [];
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
