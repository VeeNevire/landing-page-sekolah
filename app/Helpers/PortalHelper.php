<?php

namespace App\Helpers;

class PortalHelper
{
    const WEIGHTS = [
        'quiz' => 0.15,
        'homework' => 0.20,
        'project' => 0.20,
        'uts' => 0.20,
        'uas' => 0.25,
    ];

    public static function loadStudents(): array
    {
        $path = database_path('demo-data/students.json');
        return is_file($path) ? json_decode(file_get_contents($path), true) : [];
    }

    public static function loadGrades(): array
    {
        $path = database_path('demo-data/grades.json');
        return is_file($path) ? json_decode(file_get_contents($path), true) : [];
    }

    public static function getStudent(?string $studentId = null): array
    {
        $all = static::loadStudents();
        if (!$all) return [];
        $studentId = $studentId ?: ($all[0]['id'] ?? '');
        foreach ($all as $s) {
            if (($s['id'] ?? '') === $studentId) return $s;
        }
        return $all[0];
    }

    public static function getStudentGrades(string $studentId): array
    {
        $all = static::loadGrades();
        return $all[$studentId] ?? [];
    }

    public static function average(array $values): float
    {
        $numeric = array_values(array_filter($values, fn($v) => is_numeric($v)));
        return $numeric ? array_sum($numeric) / count($numeric) : 0.0;
    }

    public static function componentScores(array $subject): array
    {
        return [
            'quiz' => static::average($subject['quiz'] ?? []),
            'homework' => static::average($subject['homework'] ?? []),
            'project' => static::average($subject['project'] ?? []),
            'uts' => (float)($subject['uts'] ?? 0),
            'uas' => (float)($subject['uas'] ?? 0),
        ];
    }

    public static function finalScore(array $subject): float
    {
        $scores = static::componentScores($subject);
        $total = 0.0;
        foreach (static::WEIGHTS as $component => $weight) {
            $total += ($scores[$component] ?? 0) * $weight;
        }
        return round($total, 1);
    }

    public static function overallAverage(array $subjects): float
    {
        if (!$subjects) return 0.0;
        $values = array_map(fn($s) => static::finalScore($s), $subjects);
        return round(array_sum($values) / count($values), 1);
    }

    public static function gradeLetter(float $score): string
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

    public static function gradeClass(float $score): string
    {
        return match (true) {
            $score >= 85 => 'grade-a',
            $score >= 75 => 'grade-b',
            $score >= 65 => 'grade-c',
            default => 'grade-d',
        };
    }

    public static function attendanceRate(array $attendance): float
    {
        $total = array_sum($attendance);
        return $total > 0 ? round((($attendance['present'] ?? 0) / $total) * 100, 1) : 0.0;
    }

    public static function assignmentCompletion(array $subjects): float
    {
        $total = 0;
        $completed = 0;
        foreach ($subjects as $subject) {
            $expected = max(1, count($subject['homework'] ?? []));
            $total += $expected;
            $completed += count(array_filter($subject['homework'] ?? [], fn($v) => is_numeric($v)));
        }
        return $total > 0 ? round(($completed / $total) * 100, 1) : 0.0;
    }
}
