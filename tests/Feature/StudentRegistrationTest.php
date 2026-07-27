<?php

namespace Tests\Feature;

use App\Models\AcademicPeriod;
use App\Models\Applicant;
use App\Models\ApplicantDocument;
use App\Models\Student;
use App\Models\User;
use App\Services\StudentRegistrationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class StudentRegistrationTest extends TestCase
{
    use RefreshDatabase;

    private User $applicantUser;
    private Applicant $applicant;
    private StudentRegistrationService $service;

    protected function setUp(): void
    {
        parent::setUp();

        Mail::fake();

        $this->applicantUser = User::factory()->create([
            'role' => 'applicant',
            'email' => 'applicant@test.com',
        ]);

        $this->applicant = Applicant::create([
            'user_id' => $this->applicantUser->id,
            'full_name' => 'Budi Test',
            'nickname' => 'Budi',
            'birth_place' => 'Jakarta',
            'birth_date' => '2009-06-15',
            'gender' => 'L',
            'religion' => 'Islam',
            'address' => 'Jl. Test 123',
            'phone' => '08123456789',
            'asal_sekolah' => 'SMP Test',
            'nisn' => '0098765432',
            'jenjang' => 'SMK',
            'program_diminati' => 'RPL',
            'ayah_name' => 'Ayah Budi',
            'ayah_email' => 'ayah@test.com',
            'ayah_phone' => '0811111111',
            'ibu_name' => 'Ibu Budi',
            'ibu_email' => 'ibu@test.com',
            'ibu_phone' => '0822222222',
            'status' => 'verified',
        ]);

        $this->service = app(StudentRegistrationService::class);
    }

    public function test_creates_student_from_applicant(): void
    {
        $student = $this->service->createFromApplicant($this->applicant);

        $this->assertDatabaseHas('students', [
            'id' => $student->id,
            'full_name' => 'Budi Test',
            'nisn' => '0098765432',
            'status' => 'active',
        ]);

        $this->assertNotNull($student->nis);
    }

    public function test_updates_applicant_user_role_to_student(): void
    {
        $this->service->createFromApplicant($this->applicant);

        $this->applicantUser->refresh();
        $this->assertEquals('student', $this->applicantUser->role);
    }

    public function test_creates_parent_accounts_from_applicant(): void
    {
        $student = $this->service->createFromApplicant($this->applicant);

        $this->assertDatabaseHas('users', ['email' => 'ayah@test.com', 'role' => 'parent']);
        $this->assertDatabaseHas('users', ['email' => 'ibu@test.com', 'role' => 'parent']);

        $this->assertDatabaseHas('parent_student', [
            'student_id' => $student->id,
            'parent_id' => User::where('email', 'ayah@test.com')->first()->id,
        ]);

        $this->assertDatabaseHas('parent_student', [
            'student_id' => $student->id,
            'parent_id' => User::where('email', 'ibu@test.com')->first()->id,
        ]);
    }

    public function test_sends_parent_account_emails(): void
    {
        $this->service->createFromApplicant($this->applicant);

        Mail::assertSent(\App\Mail\ParentAccountMail::class, 2);
    }

    public function test_reuses_existing_parent_account(): void
    {
        User::factory()->create([
            'email' => 'ayah@test.com',
            'role' => 'parent',
        ]);

        $student = $this->service->createFromApplicant($this->applicant);

        $ayahCount = User::where('email', 'ayah@test.com')->count();
        $this->assertEquals(1, $ayahCount, 'Should not create duplicate parent');

        Mail::assertSent(\App\Mail\ParentAccountMail::class, 1);
    }

    public function test_skips_parent_creation_when_email_missing(): void
    {
        $applicantWithoutParents = Applicant::create([
            'user_id' => $this->applicantUser->id,
            'full_name' => 'Test No Parent',
            'nisn' => '0012345678',
            'status' => 'verified',
        ]);

        $this->applicantUser->update(['role' => 'applicant']);

        $student = $this->service->createFromApplicant($applicantWithoutParents);

        $this->assertDatabaseHas('students', ['id' => $student->id]);
        $parentCount = User::where('role', 'parent')->count();
        $this->assertEquals(0, $parentCount);
    }

    public function test_ppdb_flow_step_by_step(): void
    {
        $user = User::factory()->create(['role' => 'applicant']);

        Applicant::create([
            'user_id' => $user->id,
            'full_name' => $user->full_name ?? $user->name,
        ]);

        $this->actingAs($user);

        $response = $this->postJson(route('ppdb.form.step1'), [
            'full_name' => 'Test Lengkap',
            'nickname' => 'Test',
            'birth_place' => 'Jakarta',
            'birth_date' => '2009-01-15',
            'gender' => 'L',
            'religion' => 'Islam',
            'address' => 'Jl. Test',
            'asal_sekolah' => 'SMP Test',
            'nisn' => '1111111111',
            'jenjang' => 'SMK',
            'program_diminati' => 'RPL',
        ]);

        $response->assertJson(['success' => true]);

        $this->assertDatabaseHas('applicants', [
            'user_id' => $user->id,
            'full_name' => 'Test Lengkap',
        ]);
    }

    public function test_full_ppdb_journey_from_registration_to_student(): void
    {
        Mail::fake();

        $response = $this->post(route('ppdb.manual.register'), [
            'full_name' => 'New Applicant',
            'email' => 'new@applicant.test',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect();

        $this->assertEquals(2, User::where('role', 'applicant')->count());
        $this->assertDatabaseHas('users', ['email' => 'new@applicant.test', 'role' => 'applicant']);
    }
}
