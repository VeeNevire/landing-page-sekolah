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

    public static function weightsFromArray(?array $columns): array
    {
        $weights = static::WEIGHTS;
        foreach ($weights as $component => $default) {
            if (isset($columns[$component]) && is_numeric($columns[$component])) {
                $weights[$component] = (float) $columns[$component] / 100;
            }
        }
        return $weights;
    }

    public static function subjectWeights($subject): array
    {
        if (!$subject) {
            return static::WEIGHTS;
        }

        return static::weightsFromArray([
            'quiz' => $subject->weight_quiz,
            'homework' => $subject->weight_homework,
            'project' => $subject->weight_project,
            'assignment' => $subject->weight_assignment,
            'uts' => $subject->weight_uts,
            'uas' => $subject->weight_uas,
        ]);
    }

    public static function effectiveWeights($subject, $teachingAssignment = null): array
    {
        if ($teachingAssignment && $teachingAssignment->hasWeights()) {
            return static::subjectWeights($teachingAssignment);
        }

        if ($subject && $subject->hasWeights()) {
            return static::subjectWeights($subject);
        }

        return static::WEIGHTS;
    }

    public static function componentScores(array $subject): array
    {
        return [
            'quiz' => static::average($subject['quiz'] ?? []),
            'homework' => static::average($subject['homework'] ?? []),
            'project' => static::average($subject['project'] ?? []),
            'assignment' => static::average($subject['assignment'] ?? []),
            'uts' => (float) ($subject['uts'] ?? 0),
            'uas' => (float) ($subject['uas'] ?? 0),
        ];
    }

    public static function finalScore(array $subject, ?array $weights = null): float
    {
        if (isset($subject['final'])) {
            return (float) $subject['final'];
        }

        $weights = $weights ?? static::WEIGHTS;
        $scores = static::componentScores($subject);
        $total = 0.0;
        foreach ($weights as $component => $weight) {
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
