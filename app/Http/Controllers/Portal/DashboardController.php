<?php

namespace App\Http\Controllers\Portal;

use App\Helpers\PortalHelper;
use App\Http\Controllers\Controller;
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

        $demoStudents = PortalHelper::loadStudents();
        $demoStudent = [];
        foreach ($demoStudents as $ds) {
            if (($ds['nisn'] ?? '') === ($selectedStudent->nisn ?? '')) {
                $demoStudent = $ds;
                break;
            }
        }
        if (!$demoStudent && isset($demoStudents[0])) {
            $demoStudent = $demoStudents[0];
        }

        $subjects = PortalHelper::getStudentGrades($demoStudent['id'] ?? '');
        $average = PortalHelper::overallAverage($subjects);
        $attendanceRate = PortalHelper::attendanceRate($demoStudent['attendance'] ?? []);
        $completion = PortalHelper::assignmentCompletion($subjects);

        $history = $demoStudent['history'] ?? [];
        $chartValues = array_map(fn($r) => (float)($r['score'] ?? 0), $history);
        $minValue = $chartValues ? min($chartValues) - 4 : 70;
        $maxValue = $chartValues ? max($chartValues) + 3 : 100;
        $range = max(1, $maxValue - $minValue);

        $points = [];
        $labels = [];
        $count = max(1, count($history) - 1);
        foreach ($history as $index => $row) {
            $x = 55 + (($index / $count) * 620);
            $y = 220 - (((float)$row['score'] - $minValue) / $range * 165);
            $points[] = round($x, 1) . ',' . round($y, 1);
            $labels[] = ['x' => $x, 'label' => $row['label'], 'score' => $row['score'], 'y' => $y];
        }

        return view('portal.dashboard', [
            'students' => $students,
            'selectedStudent' => $selectedStudent,
            'selectedStudentId' => $selectedStudent->id,
            'selectedStudentInitials' => strtoupper(substr($selectedStudent->full_name ?? 'S', 0, 1)),
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
