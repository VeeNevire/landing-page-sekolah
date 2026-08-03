<?php

namespace Tests\Feature;

use App\Models\AcademicPeriod;
use App\Models\Jurusan;
use App\Models\Kelas;
use App\Models\LeaveRequest;
use App\Models\Notification;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LeaveRequestTest extends TestCase
{
    use RefreshDatabase;

    private User $homeroom;
    private User $teacher;
    private User $parent;
    private Student $student;
    private Kelas $kelas;

    protected function setUp(): void
    {
        parent::setUp();

        $this->homeroom = User::factory()->create(['role' => 'homeroom']);
        $this->teacher = User::factory()->create(['role' => 'teacher']);
        $this->parent = User::factory()->create(['role' => 'parent']);

        AcademicPeriod::create([
            'academic_year' => '2026/2027',
            'semester' => 'ganjil',
            'start_date' => '2026-07-13',
            'end_date' => '2026-12-19',
            'is_active' => true,
        ]);

        $jurusan = Jurusan::create(['kode' => 'RPL', 'nama' => 'Rekayasa Perangkat Lunak']);

        $this->kelas = Kelas::create([
            'jurusan_id' => $jurusan->id,
            'tingkat' => 11,
            'nama' => 'RPL 1',
            'is_active' => true,
            'homeroom_teacher_id' => $this->homeroom->id,
        ]);

        $studentUser = User::factory()->create(['role' => 'student']);

        $this->student = Student::create([
            'user_id' => $studentUser->id,
            'full_name' => 'Andi Saputra',
            'nisn' => '1234567890',
            'class_name' => 'XI RPL 1',
            'program_name' => 'RPL',
            'homeroom_teacher_id' => $this->homeroom->id,
            'status' => 'active',
        ]);

        $this->parent->students()->attach($this->student->id, ['relationship' => 'Ayah', 'is_primary' => true]);
    }

    public function test_parent_can_access_izin_page(): void
    {
        $response = $this->actingAs($this->parent)->get(route('portal.izin', ['student_id' => $this->student->id]));
        $response->assertStatus(200);
        $response->assertSee('Ajukan Izin');
    }

    public function test_parent_can_submit_leave_request(): void
    {
        $response = $this->actingAs($this->parent)->post(route('portal.izin.store'), [
            'student_id' => $this->student->id,
            'type' => 'sick',
            'start_date' => now()->addDay()->toDateString(),
            'end_date' => now()->addDay()->toDateString(),
            'reason' => 'Demam tinggi, ke dokter.',
        ]);

        $response->assertSessionHas('success');
        $response->assertRedirect();

        $this->assertDatabaseHas('leave_requests', [
            'student_id' => $this->student->id,
            'type' => 'sick',
            'status' => 'pending',
        ]);
    }

    public function test_parent_cannot_submit_for_unlinked_student(): void
    {
        $otherUser = User::factory()->create(['role' => 'student']);
        $otherStudent = Student::create([
            'user_id' => $otherUser->id,
            'full_name' => 'Siswa Lain',
            'nisn' => '0987654321',
            'class_name' => 'X TKJ 1',
            'program_name' => 'TKJ',
            'status' => 'active',
        ]);

        $response = $this->actingAs($this->parent)->post(route('portal.izin.store'), [
            'student_id' => $otherStudent->id,
            'type' => 'sick',
            'start_date' => now()->addDay()->toDateString(),
            'end_date' => now()->addDay()->toDateString(),
            'reason' => 'Percobaan.',
        ]);

        $response->assertSessionHasErrors('student_id');
        $this->assertDatabaseMissing('leave_requests', ['student_id' => $otherStudent->id]);
    }

    public function test_homeroom_can_access_izin_page(): void
    {
        $response = $this->actingAs($this->homeroom)->get(route('guru.wali.izin'));
        $response->assertStatus(200);
    }

    public function test_teacher_cannot_access_wali_izin(): void
    {
        $response = $this->actingAs($this->teacher)->get(route('guru.wali.izin'));
        $response->assertRedirect(route('guru.dashboard'));
    }

    public function test_homeroom_cannot_act_on_student_outside_class(): void
    {
        $otherHomeroom = User::factory()->create(['role' => 'homeroom']);
        $otherStudent = Student::create([
            'full_name' => 'Siswa Kelas Lain',
            'nisn' => '1112223334',
            'class_name' => 'X TKJ 1',
            'program_name' => 'TKJ',
            'status' => 'active',
        ]);

        $request = LeaveRequest::create([
            'student_id' => $otherStudent->id,
            'requested_by' => $this->parent->id,
            'type' => 'sick',
            'start_date' => now()->addDay()->toDateString(),
            'end_date' => now()->addDay()->toDateString(),
            'reason' => 'Sakit.',
        ]);

        $response = $this->actingAs($otherHomeroom)->post(route('guru.wali.izin.approve', $request->id));
        $response->assertForbidden();
    }

    public function test_homeroom_approve_creates_attendance_and_notification(): void
    {
        $start = now()->addDays(2)->toDateString();
        $end = now()->addDays(3)->toDateString();

        $request = LeaveRequest::create([
            'student_id' => $this->student->id,
            'requested_by' => $this->parent->id,
            'type' => 'excused',
            'start_date' => $start,
            'end_date' => $end,
            'reason' => 'Acara keluarga.',
        ]);

        $response = $this->actingAs($this->homeroom)->post(route('guru.wali.izin.approve', $request->id), [
            'response_note' => 'Disetujui.',
        ]);

        $response->assertSessionHas('success');
        $response->assertRedirect();

        $this->assertDatabaseHas('leave_requests', ['id' => $request->id, 'status' => 'approved']);

        foreach ([$start, $end] as $date) {
            $this->assertDatabaseHas('attendance', [
                'student_id' => $this->student->id,
                'attendance_date' => $date,
                'status' => 'excused',
            ]);
        }

        $this->assertDatabaseHas('notifications', [
            'student_id' => $this->student->id,
            'type' => 'success',
        ]);
    }

    public function test_homeroom_reject_updates_status_and_notifies(): void
    {
        $request = LeaveRequest::create([
            'student_id' => $this->student->id,
            'requested_by' => $this->parent->id,
            'type' => 'sick',
            'start_date' => now()->addDay()->toDateString(),
            'end_date' => now()->addDay()->toDateString(),
            'reason' => 'Tidak jelas.',
        ]);

        $response = $this->actingAs($this->homeroom)->post(route('guru.wali.izin.reject', $request->id));

        $response->assertSessionHas('success');

        $this->assertDatabaseHas('leave_requests', ['id' => $request->id, 'status' => 'rejected']);
        $this->assertDatabaseHas('notifications', [
            'student_id' => $this->student->id,
            'type' => 'warning',
        ]);
        $this->assertDatabaseMissing('attendance', ['student_id' => $this->student->id]);
    }
}