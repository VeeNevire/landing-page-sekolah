<?php

namespace Tests\Feature;

use App\Models\AcademicPeriod;
use App\Models\Assessment;
use App\Models\AssessmentScore;
use App\Models\Student;
use App\Models\Subject;
use App\Models\TeachingAssignment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GradebookTest extends TestCase
{
    use RefreshDatabase;

    private User $teacher;
    private User $homeroom;
    private Student $student;
    private Subject $subject;
    private AcademicPeriod $period;
    private TeachingAssignment $assignment;

    protected function setUp(): void
    {
        parent::setUp();

        $this->teacher = User::factory()->create(['role' => 'teacher']);
        $this->homeroom = User::factory()->create(['role' => 'homeroom']);

        $this->period = AcademicPeriod::create([
            'academic_year' => '2026/2027',
            'semester' => 'ganjil',
            'start_date' => '2026-07-13',
            'end_date' => '2026-12-19',
            'is_active' => true,
        ]);

        $this->subject = Subject::create([
            'code' => 'TEST01',
            'name' => 'Test Subject',
            'kkm' => 75,
        ]);

        $this->student = Student::create([
            'full_name' => 'Test Student',
            'nisn' => '1234567890',
            'class_name' => 'X RPL 1',
            'program_name' => 'RPL',
            'homeroom_teacher_id' => $this->homeroom->id,
            'status' => 'active',
        ]);

        $this->assignment = TeachingAssignment::create([
            'period_id' => $this->period->id,
            'subject_id' => $this->subject->id,
            'teacher_id' => $this->teacher->id,
            'class_name' => 'X RPL 1',
        ]);
    }

    public function test_teacher_can_create_assessment_and_scores(): void
    {
        $this->actingAs($this->teacher);

        $response = $this->post(route('guru.nilai.store', ['class' => 'X RPL 1', 'subject' => $this->subject->id]), [
            'title' => 'Kuis 1',
            'component' => 'quiz',
            'scores' => [$this->student->id => 85],
        ]);

        $response->assertSessionHas('success');

        $this->assertDatabaseHas('assessments', [
            'teaching_assignment_id' => $this->assignment->id,
            'title' => 'Kuis 1',
            'component' => 'quiz',
        ]);

        $assessment = Assessment::where('title', 'Kuis 1')->first();
        $this->assertDatabaseHas('assessment_scores', [
            'assessment_id' => $assessment->id,
            'student_id' => $this->student->id,
            'score' => 85,
        ]);
    }

    public function test_teacher_can_publish_assessments(): void
    {
        $assessment = Assessment::create([
            'teaching_assignment_id' => $this->assignment->id,
            'title' => 'UTS',
            'component' => 'uts',
            'assessment_date' => '2026-09-20',
            'max_score' => 100,
        ]);

        AssessmentScore::create([
            'assessment_id' => $assessment->id,
            'student_id' => $this->student->id,
            'score' => 88,
            'graded_at' => now(),
        ]);

        $this->actingAs($this->teacher);

        $response = $this->post(route('guru.publikasi.store', ['class' => 'X RPL 1']));

        $response->assertSessionHas('success');

        $published = Assessment::find($assessment->id);
        $this->assertNotNull($published->published_at);
    }

    public function test_teacher_can_record_attendance(): void
    {
        $this->actingAs($this->teacher);

        $response = $this->post(route('guru.absensi.store'), [
            'class_name' => 'X RPL 1',
            'date' => '2026-07-20',
            'status' => [$this->student->id => 'present'],
        ]);

        $response->assertSessionHas('success');

        $this->assertDatabaseHas('attendance', [
            'student_id' => $this->student->id,
            'attendance_date' => '2026-07-20',
            'status' => 'present',
        ]);
    }

    public function test_final_score_weighted_calculation(): void
    {
        $this->actingAs($this->teacher);

        Assessment::create([
            'teaching_assignment_id' => $this->assignment->id,
            'title' => 'Kuis',
            'component' => 'quiz',
            'assessment_date' => '2026-08-01',
            'max_score' => 100,
            'published_at' => now(),
        ]);

        Assessment::create([
            'teaching_assignment_id' => $this->assignment->id,
            'title' => 'Homework',
            'component' => 'homework',
            'assessment_date' => '2026-08-10',
            'max_score' => 100,
            'published_at' => now(),
        ]);

        Assessment::create([
            'teaching_assignment_id' => $this->assignment->id,
            'title' => 'UTS',
            'component' => 'uts',
            'assessment_date' => '2026-09-20',
            'max_score' => 100,
            'published_at' => now(),
        ]);

        Assessment::create([
            'teaching_assignment_id' => $this->assignment->id,
            'title' => 'UAS',
            'component' => 'uas',
            'assessment_date' => '2026-12-10',
            'max_score' => 100,
            'published_at' => now(),
        ]);

        $allAssessments = Assessment::all();
        $scores = [
            90, // quiz
            85, // homework
            80, // uts
            88, // uas
        ];

        foreach ($allAssessments as $i => $a) {
            AssessmentScore::create([
                'assessment_id' => $a->id,
                'student_id' => $this->student->id,
                'score' => $scores[$i] ?? 80,
                'graded_at' => now(),
            ]);
        }

        $assignments = TeachingAssignment::with(['assessments' => fn($q) => $q->whereNotNull('published_at')])->get();
        $studentScores = AssessmentScore::where('student_id', $this->student->id)->get()->keyBy('assessment_id');

        $raw = ['quiz' => [], 'homework' => [], 'project' => [], 'uts' => 0, 'uas' => 0];
        foreach ($assignments->first()->assessments as $a) {
            $score = $studentScores->get($a->id)?->score;
            if ($score === null) continue;
            if ($a->component === 'uts' || $a->component === 'uas') {
                $raw[$a->component] = max($raw[$a->component], (float) $score);
            } else {
                $raw[$a->component][] = (float) $score;
            }
        }

        $final = \App\Helpers\PortalHelper::finalScore($raw);
        $expected = (90 * 0.15) + (85 * 0.10) + (0 * 0.20) + (80 * 0.20) + (88 * 0.25);
        $this->assertEqualsWithDelta($expected, $final, 0.01);
    }
}
