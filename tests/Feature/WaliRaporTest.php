<?php

namespace Tests\Feature;

use App\Models\AcademicPeriod;
use App\Models\Assessment;
use App\Models\AssessmentScore;
use App\Models\Jurusan;
use App\Models\Kelas;
use App\Models\Student;
use App\Models\Subject;
use App\Models\TeachingAssignment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WaliRaporTest extends TestCase
{
    use RefreshDatabase;

    private User $homeroom;
    private User $teacher;
    private User $studentUser;
    private Kelas $kelas;
    private Student $student;

    protected function setUp(): void
    {
        parent::setUp();

        $this->homeroom = User::factory()->create(['role' => 'homeroom']);
        $this->teacher = User::factory()->create(['role' => 'teacher']);

        $period = AcademicPeriod::create([
            'academic_year' => '2026/2027',
            'semester' => 'ganjil',
            'start_date' => '2026-07-13',
            'end_date' => '2026-12-19',
            'is_active' => true,
        ]);

        $jurusan = Jurusan::create([
            'kode' => 'RPL',
            'nama' => 'Rekayasa Perangkat Lunak',
        ]);

        $this->kelas = Kelas::create([
            'jurusan_id' => $jurusan->id,
            'tingkat' => 11,
            'nama' => 'RPL 1',
            'is_active' => true,
            'homeroom_teacher_id' => $this->homeroom->id,
        ]);

        $this->studentUser = User::factory()->create(['role' => 'student']);

        $this->student = Student::create([
            'user_id' => $this->studentUser->id,
            'full_name' => 'Andi Saputra',
            'nisn' => '1234567890',
            'class_name' => 'XI RPL 1',
            'program_name' => 'RPL',
            'homeroom_teacher_id' => $this->homeroom->id,
            'status' => 'active',
        ]);

        $teacherUser = User::factory()->create(['role' => 'teacher']);

        $subject = Subject::create([
            'code' => 'MAT',
            'name' => 'Matematika',
            'kkm' => 75,
        ]);

        $assignment = TeachingAssignment::create([
            'period_id' => $period->id,
            'subject_id' => $subject->id,
            'teacher_id' => $teacherUser->id,
            'class_name' => 'XI RPL 1',
        ]);

        $assessment = Assessment::create([
            'teaching_assignment_id' => $assignment->id,
            'title' => 'Kuis 1',
            'component' => 'quiz',
            'assessment_date' => now()->toDateString(),
            'max_score' => 100,
            'published_at' => now(),
        ]);

        AssessmentScore::create([
            'assessment_id' => $assessment->id,
            'student_id' => $this->student->id,
            'score' => 85,
        ]);
    }

    public function test_homeroom_can_access_rapor_page(): void
    {
        $response = $this->actingAs($this->homeroom)->get(route('guru.wali.rapor'));
        $response->assertStatus(200);
        $response->assertSee('XI RPL 1');
    }

    public function test_teacher_cannot_access_wali_rapor(): void
    {
        $response = $this->actingAs($this->teacher)->get(route('guru.wali.rapor'));
        $response->assertRedirect(route('guru.dashboard'));
    }

    public function test_student_cannot_access_wali_rapor(): void
    {
        $response = $this->actingAs($this->studentUser)->get(route('guru.wali.rapor'));
        $response->assertRedirect(route('siswa.dashboard'));
    }

    public function test_homeroom_can_download_student_pdf(): void
    {
        $response = $this->actingAs($this->homeroom)->get(route('guru.wali.rapor.pdf', $this->student->id));
        $response->assertStatus(200);
        $response->assertHeader('content-type', 'application/pdf');
    }

    public function test_homeroom_cannot_download_student_outside_class(): void
    {
        $otherUser = User::factory()->create(['role' => 'student']);
        $otherStudent = Student::create([
            'user_id' => $otherUser->id,
            'full_name' => 'Siswa Kelas Lain',
            'nisn' => '0987654321',
            'class_name' => 'X TKJ 1',
            'program_name' => 'TKJ',
            'status' => 'active',
        ]);

        $response = $this->actingAs($this->homeroom)->get(route('guru.wali.rapor.pdf', $otherStudent->id));
        $response->assertForbidden();
    }

    public function test_homeroom_can_download_all_zip(): void
    {
        $response = $this->actingAs($this->homeroom)->get(route('guru.wali.rapor.pdf-semua'));
        $response->assertStatus(200);
        $this->assertTrue(str_contains($response->headers->get('content-type', ''), 'zip') !== false || $response->headers->get('content-type') === 'application/octet-stream');
    }
}
