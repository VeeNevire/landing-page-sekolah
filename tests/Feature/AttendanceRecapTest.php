<?php

namespace Tests\Feature;

use App\Models\Attendance;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use OpenSpout\Reader\XLSX\Reader;
use Tests\TestCase;

class AttendanceRecapTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    private Student $andi;

    private Student $budi;

    protected function setUp(): void
    {
        parent::setUp();
        $this->travelTo(now()->setDate(2026, 10, 13)->setTime(10, 0));
        $this->admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);
        $this->andi = Student::create(['nisn' => '1111111111', 'nis' => '1001', 'full_name' => 'Andi Rekap', 'class_name' => 'X RPL', 'program_name' => 'RPL', 'status' => 'active']);
        $this->budi = Student::create(['nisn' => '2222222222', 'nis' => '1002', 'full_name' => 'Budi Rekap', 'class_name' => 'XI TKJ', 'program_name' => 'TKJ', 'status' => 'active']);
        Student::create(['nisn' => '3333333333', 'full_name' => 'Siswa Nonaktif', 'class_name' => 'X RPL', 'program_name' => 'RPL', 'status' => 'inactive']);

        // Three effective days in October; Andi: present, late, sick. Budi: present on one day only.
        $this->mark($this->andi, '2026-10-01', 'present', 'rfid');
        $this->mark($this->andi, '2026-10-02', 'late', 'rfid');
        $this->mark($this->andi, '2026-10-05', 'sick');
        $this->mark($this->budi, '2026-10-01', 'present');
        // Outside the default period.
        $this->mark($this->budi, '2026-09-30', 'unexcused');
    }

    private function mark(Student $student, string $date, string $status, ?string $source = null): void
    {
        Attendance::create(['student_id' => $student->id, 'attendance_date' => $date, 'status' => $status,
            'source' => $source, 'recorded_by' => $this->admin->id]);
    }

    public function test_recap_counts_statuses_effective_days_and_percentage(): void
    {
        $this->actingAs($this->admin)->get('/admin/rekap-absensi')->assertOk()
            ->assertSee('Andi Rekap')->assertSee('Budi Rekap')->assertDontSee('Siswa Nonaktif')
            ->assertViewHas('filters', fn ($f) => $f['from'] === '2026-10-01' && $f['to'] === '2026-10-13')
            ->assertViewHas('summary', fn ($s) => $s['students'] === 2 && $s['days'] === 3 && $s['present'] === 2
                && $s['late'] === 1 && $s['sick'] === 1 && $s['unexcused'] === 0 && $s['unrecorded'] === 2 && $s['percentage'] === 50.0)
            ->assertViewHas('rows', function ($rows) {
                [$andi, $budi] = $rows->items();

                return $andi['name'] === 'Andi Rekap' && $andi['present'] === 1 && $andi['late'] === 1 && $andi['sick'] === 1
                    && $andi['unrecorded'] === 0 && $andi['percentage'] === 66.7
                    && $budi['present'] === 1 && $budi['unrecorded'] === 2 && $budi['percentage'] === 33.3;
            });
    }

    public function test_filters_by_class_and_date_range(): void
    {
        $this->actingAs($this->admin)->get('/admin/rekap-absensi?class=XI+TKJ&from=2026-09-01&to=2026-09-30')->assertOk()
            ->assertSee('Budi Rekap')->assertDontSee('Andi Rekap')
            ->assertViewHas('summary', fn ($s) => $s['days'] === 1 && $s['unexcused'] === 1 && $s['percentage'] === 0.0);
        $this->get('/admin/rekap-absensi?from=2026-10-10&to=2026-10-01')->assertSessionHasErrors('to');
    }

    public function test_pdf_export_downloads(): void
    {
        $response = $this->actingAs($this->admin)->get('/admin/rekap-absensi/pdf?class=X+RPL');
        $response->assertOk()->assertHeader('content-type', 'application/pdf');
        $this->assertStringContainsString('rekap-absensi_2026-10-01_2026-10-13_x-rpl.pdf', $response->headers->get('content-disposition'));
        $this->assertStringStartsWith('%PDF', $response->getContent());
    }

    public function test_excel_export_contains_recap_rows(): void
    {
        $response = $this->actingAs($this->admin)->get('/admin/rekap-absensi/excel');
        $response->assertOk()->assertHeader('content-type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $this->assertStringContainsString('.xlsx', $response->headers->get('content-disposition'));

        $path = tempnam(sys_get_temp_dir(), 'recap').'.xlsx';
        file_put_contents($path, $response->streamedContent());
        $reader = new Reader;
        $reader->open($path);
        $rows = [];
        foreach ($reader->getSheetIterator() as $sheet) {
            foreach ($sheet->getRowIterator() as $row) {
                $rows[] = $row->toArray();
            }
        }
        $reader->close();
        unlink($path);

        $andi = collect($rows)->first(fn ($row) => ($row[1] ?? null) === 'Andi Rekap');
        $this->assertSame([1, 'Andi Rekap', '1001', 'X RPL', 1, 1, 1, 0, 0, 0, 66.7], $andi);
        $this->assertSame('Total', collect($rows)->last()[1]);
    }

    public function test_only_admin_and_principal_can_access(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'principal']))->get('/admin/rekap-absensi')->assertOk();
        $teacher = User::factory()->create(['role' => 'teacher']);
        foreach (['', '/pdf', '/excel'] as $suffix) {
            $this->actingAs($teacher)->getJson('/admin/rekap-absensi'.$suffix)->assertForbidden();
        }
    }
}
