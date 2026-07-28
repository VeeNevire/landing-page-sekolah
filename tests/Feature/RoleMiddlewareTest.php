<?php

namespace Tests\Feature;

use App\Models\AcademicPeriod;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoleMiddlewareTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $teacher;
    private User $homeroom;
    private User $student;
    private User $parent;
    private User $alumni;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create(['role' => 'admin']);
        $this->teacher = User::factory()->create(['role' => 'teacher']);
        $this->homeroom = User::factory()->create(['role' => 'homeroom']);

        $studentUser = User::factory()->create(['role' => 'student']);
        $this->student = $studentUser;

        Student::create([
            'user_id' => $studentUser->id,
            'full_name' => 'Student',
            'nisn' => '1234567890',
            'class_name' => 'X RPL 1',
            'program_name' => 'RPL',
            'status' => 'active',
        ]);

        $alumniUser = User::factory()->create(['role' => 'alumni']);
        $this->alumni = $alumniUser;

        Student::create([
            'user_id' => $alumniUser->id,
            'full_name' => 'Alumni User',
            'nisn' => '9876543210',
            'class_name' => 'XII RPL 1',
            'program_name' => 'RPL',
            'status' => 'graduated',
            'graduation_year' => 2026,
        ]);

        $parentUser = User::factory()->create(['role' => 'parent']);
        $this->parent = $parentUser;

        AcademicPeriod::create([
            'academic_year' => '2026/2027',
            'semester' => 'ganjil',
            'start_date' => '2026-07-13',
            'end_date' => '2026-12-19',
            'is_active' => true,
        ]);
    }

    public function test_admin_can_access_admin_dashboard(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.dashboard'));
        $response->assertStatus(200);
    }

    public function test_principal_can_access_admin_dashboard(): void
    {
        $principal = User::factory()->create(['role' => 'principal']);
        $response = $this->actingAs($principal)->get(route('admin.dashboard'));
        $response->assertStatus(200);
    }

    public function test_teacher_cannot_access_admin_dashboard(): void
    {
        $response = $this->actingAs($this->teacher)->get(route('admin.dashboard'));
        $response->assertRedirect(route('guru.dashboard'));
    }

    public function test_student_cannot_access_admin_dashboard(): void
    {
        $response = $this->actingAs($this->student)->get(route('admin.dashboard'));
        $response->assertRedirect(route('siswa.dashboard'));
    }

    public function test_parent_cannot_access_admin_dashboard(): void
    {
        $response = $this->actingAs($this->parent)->get(route('admin.dashboard'));
        $response->assertRedirect(route('portal.dashboard'));
    }

    public function test_student_can_access_student_dashboard(): void
    {
        $response = $this->actingAs($this->student)->get(route('siswa.dashboard'));
        $response->assertStatus(200);
    }

    public function test_teacher_cannot_access_student_dashboard(): void
    {
        $response = $this->actingAs($this->teacher)->get(route('siswa.dashboard'));
        $response->assertRedirect(route('guru.dashboard'));
    }

    public function test_parent_cannot_access_student_dashboard(): void
    {
        $response = $this->actingAs($this->parent)->get(route('siswa.dashboard'));
        $response->assertRedirect(route('portal.dashboard'));
    }

    public function test_teacher_can_access_teacher_dashboard(): void
    {
        $response = $this->actingAs($this->teacher)->get(route('guru.dashboard'));
        $response->assertStatus(200);
    }

    public function test_homeroom_can_access_teacher_dashboard(): void
    {
        $response = $this->actingAs($this->homeroom)->get(route('guru.dashboard'));
        $response->assertStatus(200);
    }

    public function test_student_cannot_access_teacher_dashboard(): void
    {
        $response = $this->actingAs($this->student)->get(route('guru.dashboard'));
        $response->assertRedirect(route('siswa.dashboard'));
    }

    public function test_unauthenticated_user_is_redirected_to_login(): void
    {
        $response = $this->get(route('portal.dashboard'));
        $response->assertRedirect(route('login'));
    }

    public function test_guest_can_access_public_pages(): void
    {
        $response = $this->get(route('beranda'));
        $response->assertStatus(200);

        $response = $this->get(route('ppdb'));
        $response->assertStatus(200);

        $response = $this->get(route('profil'));
        $response->assertStatus(200);
    }

    public function test_alumni_can_access_alumni_dashboard(): void
    {
        $response = $this->actingAs($this->alumni)->get(route('alumni.dashboard'));
        $response->assertStatus(200);
    }

    public function test_student_cannot_access_alumni_dashboard(): void
    {
        $response = $this->actingAs($this->student)->get(route('alumni.dashboard'));
        $response->assertRedirect(route('siswa.dashboard'));
    }

    public function test_alumni_cannot_access_student_dashboard(): void
    {
        $response = $this->actingAs($this->alumni)->get(route('siswa.dashboard'));
        $response->assertRedirect(route('alumni.dashboard'));
    }
}
