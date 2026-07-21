<?php

namespace App\Helpers;

class PortalHelper
{
    const WEIGHTS = [
        'quiz' => 0.15,
        'homework' => 0.10,
        'project' => 0.20,
        'assignment' => 0.10,
        'uts' => 0.20,
        'uas' => 0.25,
    ];

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
            'uts' => (float) ($subject['uts'] ?? 0),
            'uas' => (float) ($subject['uas'] ?? 0),
        ];
    }

    public static function finalScore(array $subject): float
    {
        if (isset($subject['final'])) {
            return (float) $subject['final'];
        }

        $scores = static::componentScores($subject);
        $total = 0.0;
        foreach (static::WEIGHTS as $component => $weight) {
            $total += ($scores[$component] ?? 0) * $weight;
        }
        return round($total, 1);
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
}
