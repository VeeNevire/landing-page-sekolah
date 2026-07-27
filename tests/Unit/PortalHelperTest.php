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
}
