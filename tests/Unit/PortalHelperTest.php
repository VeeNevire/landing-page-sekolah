<?php

namespace Tests\Unit;

use App\Helpers\PortalHelper;
use Tests\TestCase;

class PortalHelperTest extends TestCase
{
    public function test_component_scores_returns_averages()
    {
        $subject = [
            'assignment' => [],
            'quiz' => [80, 90, 100],
            'homework' => [70, 85],
            'project' => [95],
            'uts' => 88,
            'uas' => 92,
        ];

        $scores = PortalHelper::componentScores($subject);

        $this->assertEqualsWithDelta(90, $scores['quiz'], 0.01);
        $this->assertEqualsWithDelta(77.5, $scores['homework'], 0.01);
        $this->assertEqualsWithDelta(95, $scores['project'], 0.01);
        $this->assertEqualsWithDelta(88, $scores['uts'], 0.01);
        $this->assertEqualsWithDelta(92, $scores['uas'], 0.01);
    }

    public function test_component_scores_handles_empty_arrays()
    {
        $scores = PortalHelper::componentScores(['assignment' => [], 'quiz' => [], 'homework' => [], 'project' => [], 'uts' => 0, 'uas' => 0]);

        $this->assertEqualsWithDelta(0, $scores['quiz'], 0.01);
        $this->assertEqualsWithDelta(0, $scores['homework'], 0.01);
        $this->assertEqualsWithDelta(0, $scores['project'], 0.01);
    }

    public function test_final_score_with_all_components()
    {
        $subject = [
            'assignment' => [100],
            'quiz' => [100],
            'homework' => [100],
            'project' => [100],
            'uts' => 100,
            'uas' => 100,
        ];

        $this->assertEquals(100, PortalHelper::finalScore($subject));
    }

    public function test_final_score_with_all_zeros()
    {
        $subject = [
            'assignment' => [],
            'quiz' => [],
            'homework' => [],
            'project' => [],
            'uts' => 0,
            'uas' => 0,
        ];

        $this->assertEquals(0, PortalHelper::finalScore($subject));
    }

    public function test_final_score_with_weighted_mix()
    {
        $subject = [
            'assignment' => [],
            'quiz' => [100],
            'homework' => [100],
            'project' => [100],
            'uts' => 80,
            'uas' => 80,
        ];

        $expected = (100 * 0.15) + (100 * 0.10) + (100 * 0.20) + (80 * 0.20) + (80 * 0.25);

        $this->assertEqualsWithDelta($expected, PortalHelper::finalScore($subject), 0.01);
    }

    public function test_grade_letter_a()
    {
        $this->assertEquals('A', PortalHelper::gradeLetter(95));
        $this->assertEquals('A', PortalHelper::gradeLetter(90));
    }

    public function test_grade_letter_a_minus()
    {
        $this->assertEquals('A-', PortalHelper::gradeLetter(89));
        $this->assertEquals('A-', PortalHelper::gradeLetter(85));
    }

    public function test_grade_letter_b_plus()
    {
        $this->assertEquals('B+', PortalHelper::gradeLetter(84));
        $this->assertEquals('B+', PortalHelper::gradeLetter(80));
    }

    public function test_grade_letter_b()
    {
        $this->assertEquals('B', PortalHelper::gradeLetter(79));
        $this->assertEquals('B', PortalHelper::gradeLetter(75));
    }

    public function test_grade_letter_d()
    {
        $this->assertEquals('D', PortalHelper::gradeLetter(30));
        $this->assertEquals('D', PortalHelper::gradeLetter(64));
    }

    public function test_average_returns_zero_for_empty()
    {
        $this->assertEquals(0, PortalHelper::average([]));
    }

    public function test_average_returns_correct_value()
    {
        $this->assertEqualsWithDelta(85, PortalHelper::average([80, 85, 90]), 0.01);
    }

    public function test_average_filters_non_numeric()
    {
        $this->assertEqualsWithDelta(85, PortalHelper::average([80, null, 90, 'abc']), 0.01);
    }

    public function test_final_score_uses_passed_key()
    {
        $subject = ['final' => 88.5, 'assignment' => [], 'quiz' => [], 'homework' => [], 'project' => [], 'uts' => 0, 'uas' => 0];
        $this->assertEquals(88.5, PortalHelper::finalScore($subject));
    }

    public function test_grade_class_returns_correct_css_class()
    {
        $this->assertEquals('grade-a', PortalHelper::gradeClass(90));
        $this->assertEquals('grade-b', PortalHelper::gradeClass(80));
        $this->assertEquals('grade-c', PortalHelper::gradeClass(70));
        $this->assertEquals('grade-d', PortalHelper::gradeClass(60));
    }

    public function test_weights_from_array_returns_defaults_for_empty()
    {
        $this->assertEquals(PortalHelper::WEIGHTS, PortalHelper::weightsFromArray([]));
        $this->assertEquals(PortalHelper::WEIGHTS, PortalHelper::weightsFromArray(null));
    }

    public function test_weights_from_array_merges_partial_overrides()
    {
        $weights = PortalHelper::weightsFromArray(['quiz' => 20, 'uas' => 30]);
        $this->assertEqualsWithDelta(0.20, $weights['quiz'], 0.0001);
        $this->assertEqualsWithDelta(0.30, $weights['uas'], 0.0001);
        $this->assertEqualsWithDelta(0.10, $weights['homework'], 0.0001);
        $this->assertEqualsWithDelta(0.20, $weights['project'], 0.0001);
    }

    public function test_subject_weights_uses_defaults_when_no_columns()
    {
        $subject = new \App\Models\Subject();
        $this->assertEquals(PortalHelper::WEIGHTS, PortalHelper::subjectWeights($subject));
    }

    public function test_subject_weights_reads_columns()
    {
        $subject = new \App\Models\Subject([
            'weight_quiz' => 10,
            'weight_homework' => 5,
            'weight_project' => 30,
        ]);

        $weights = PortalHelper::subjectWeights($subject);

        $this->assertEqualsWithDelta(0.10, $weights['quiz'], 0.0001);
        $this->assertEqualsWithDelta(0.05, $weights['homework'], 0.0001);
        $this->assertEqualsWithDelta(0.30, $weights['project'], 0.0001);
        $this->assertEqualsWithDelta(0.20, $weights['uts'], 0.0001);
    }

    public function test_effective_weights_teaching_assignment_overrides_subject()
    {
        $subject = new \App\Models\Subject(['weight_quiz' => 10, 'weight_uas' => 30]);
        $ta = new \App\Models\TeachingAssignment(['weight_project' => 40]);

        $weights = PortalHelper::effectiveWeights($subject, $ta);

        $this->assertEqualsWithDelta(0.40, $weights['project'], 0.0001);
        $this->assertEqualsWithDelta(0.15, $weights['quiz'], 0.0001);
        $this->assertEqualsWithDelta(0.20, $weights['uts'], 0.0001);
    }

    public function test_effective_weights_uses_subject_when_ta_empty()
    {
        $subject = new \App\Models\Subject(['weight_quiz' => 10, 'weight_uas' => 30]);
        $ta = new \App\Models\TeachingAssignment();

        $weights = PortalHelper::effectiveWeights($subject, $ta);

        $this->assertEqualsWithDelta(0.10, $weights['quiz'], 0.0001);
        $this->assertEqualsWithDelta(0.30, $weights['uas'], 0.0001);
        $this->assertEqualsWithDelta(0.20, $weights['project'], 0.0001);
    }

    public function test_final_score_with_custom_weights()
    {
        $subject = [
            'quiz' => [100],
            'homework' => [100],
            'project' => [100],
            'assignment' => [100],
            'uts' => 100,
            'uas' => 100,
        ];
        $weights = ['quiz' => 0.1, 'homework' => 0.1, 'project' => 0.3, 'assignment' => 0.1, 'uts' => 0.2, 'uas' => 0.2];

        $this->assertEquals(100, PortalHelper::finalScore($subject, $weights));

        $partial = ['quiz' => [100], 'project' => [100], 'uts' => 0, 'uas' => 0];
        $this->assertEqualsWithDelta(40, PortalHelper::finalScore($partial, $weights), 0.01);
    }
}
