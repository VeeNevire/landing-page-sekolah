<?php

namespace App\Http\Controllers\Portal;

use App\Helpers\PortalHelper;
use App\Http\Controllers\Controller;
use App\Models\AcademicPeriod;
use App\Models\Assessment;
use App\Services\RaporService;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function __construct(private RaporService $raporService)
    {
    }

    public function index(Request $request)
    {
        $user = $request->user();
        $students = $user->students()->where('status', 'active')->get();

        if ($students->isEmpty()) {
            $tgl = now()->startOfMonth();
            return view('portal.laporan', array_merge(
                $this->emptyData(),
                [
                    'students' => collect(), 'selectedStudent' => null, 'selectedStudentId' => null,
                    'selectedStudentInitials' => 'S',
                    'jadwalBulan' => collect(), 'calendarMonth' => $tgl,
                    'prevBulan' => $tgl->copy()->subMonth()->format('Y-m'),
                    'nextBulan' => $tgl->copy()->addMonth()->format('Y-m'),
                ]
            ));
        }

        $studentId = $request->query('student_id', $students->first()->id);
        $selectedStudent = $students->firstWhere('id', $studentId) ?? $students->first();

        $studentReport = $this->raporService->buildReport($selectedStudent);

        $bulan = $request->query('bulan', now()->format('Y-m'));
        $tgl = \Carbon\Carbon::parse($bulan . '-01');
        $jadwalBulan = Assessment::whereHas('teachingAssignment', fn($q) =>
            $q->where('class_name', $selectedStudent->class_name)
        )->whereMonth('assessment_date', $tgl->month)
         ->whereYear('assessment_date', $tgl->year)
         ->with('teachingAssignment.subject', 'teachingAssignment.customSubject', 'teachingAssignment.teacher')
         ->orderBy('assessment_date')
         ->get()
         ->groupBy(fn($a) => $a->assessment_date->format('Y-m-d'));

        return view('portal.laporan', [
            'students' => $students,
            'selectedStudent' => $selectedStudent,
            'selectedStudentId' => $selectedStudent->id,
            'selectedStudentInitials' => strtoupper(mb_substr($selectedStudent->full_name ?? 'S', 0, 1)),
            'demoStudent' => $studentReport,
            'subjects' => $studentReport['subjects'],
            'average' => $studentReport['average'],
            'attendanceRate' => $studentReport['attendanceRate'],
            'jadwalBulan' => $jadwalBulan,
            'calendarMonth' => $tgl,
            'prevBulan' => $tgl->copy()->subMonth()->format('Y-m'),
            'nextBulan' => $tgl->copy()->addMonth()->format('Y-m'),
        ]);
    }

    public function exportCsv(Request $request)
    {
        $user = $request->user();
        $studentId = $request->query('student_id');
        $student = $user->students()->where('status', 'active')->findOrFail($studentId);

        $studentReport = $this->raporService->buildReport($student);
        $subjects = $studentReport['subjects'];

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

            fputcsv($output, ['Mata Pelajaran', 'Kuis', 'PR', 'Tugas', 'Proyek', 'UTS', 'UAS', 'Nilai Akhir', 'Grade', 'KKM', 'Status']);
            foreach ($subjects as $subject) {
                $final = $subject['final'] ?? 0;
                $kkm = $subject['kkm'] ?? 75;
                $grade = $this->gradeLetter($final);
                $pass = $final >= $kkm;
                fputcsv($output, [
                    $subject['name'],
                    number_format($this->avg($subject['quiz']), 1, ',', '.'),
                    number_format($this->avg($subject['homework']), 1, ',', '.'),
                    number_format($this->avg($subject['assignment']), 1, ',', '.'),
                    number_format($this->avg($subject['project']), 1, ',', '.'),
                    number_format($subject['uts'], 1, ',', '.'),
                    number_format($subject['uas'], 1, ',', '.'),
                    number_format($final, 1, ',', '.'),
                    $grade,
                    $kkm,
                    $pass ? 'Tuntas' : 'Perlu Remedial',
                ]);
            }

            fclose($output);
        };

        return response()->stream($callback, 200, $headers);
    }

    private function avg(array $values): float
    {
        return $values ? array_sum($values) / count($values) : 0;
    }

    private function gradeLetter(float $score): string
    {
        return PortalHelper::gradeLetter($score);
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
