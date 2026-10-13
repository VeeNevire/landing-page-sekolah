<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Student;
use App\Services\AuditService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use OpenSpout\Common\Entity\Row;
use OpenSpout\Common\Entity\Style\Style;
use OpenSpout\Writer\XLSX\Options;
use OpenSpout\Writer\XLSX\Writer;

class AttendanceRecapController extends Controller
{
    public const STATUSES = ['present' => 'Hadir', 'late' => 'Terlambat', 'sick' => 'Sakit', 'excused' => 'Izin', 'unexcused' => 'Alpa'];

    public const HEADINGS = ['No', 'Nama', 'NIS', 'Kelas', 'Hadir', 'Terlambat', 'Sakit', 'Izin', 'Alpa', 'Belum Tercatat', '% Kehadiran'];

    public function index(Request $request)
    {
        $filters = $this->filters($request);
        $recap = $this->recap($filters);
        $page = LengthAwarePaginator::resolveCurrentPage();
        $rows = new LengthAwarePaginator($recap['rows']->forPage($page, 50)->values(), $recap['rows']->count(), 50, $page,
            ['path' => $request->url(), 'query' => $request->query()]);
        $classes = Student::where('status', 'active')->distinct()->orderBy('class_name')->pluck('class_name');

        return view('admin.attendance-recap', ['filters' => $filters, 'summary' => $recap['summary'],
            'rows' => $rows, 'offset' => ($page - 1) * 50, 'classes' => $classes]);
    }

    public function pdf(Request $request)
    {
        $filters = $this->filters($request);
        $recap = $this->recap($filters);
        AuditService::log('attendance.recap_export', null, null, 'PDF '.$this->periodLabel($filters), $request->user()->id);

        return Pdf::loadView('admin.attendance-recap-pdf', ['filters' => $filters, 'summary' => $recap['summary'],
            'rows' => $recap['rows'], 'period' => $this->periodLabel($filters)])
            ->setPaper('a4', 'landscape')->download($this->fileName($filters, 'pdf'));
    }

    public function excel(Request $request)
    {
        $filters = $this->filters($request);
        $recap = $this->recap($filters);
        AuditService::log('attendance.recap_export', null, null, 'Excel '.$this->periodLabel($filters), $request->user()->id);
        $period = $this->periodLabel($filters);

        return response()->streamDownload(function () use ($recap, $filters, $period) {
            $options = new Options;
            foreach ([6, 32, 16, 14, 9, 11, 9, 9, 9, 15, 13] as $i => $width) {
                $options->setColumnWidth($width, $i + 1);
            }
            $writer = new Writer($options);
            $writer->openToFile('php://output');
            $bold = (new Style)->setFontBold();
            $title = (new Style)->setFontBold()->setFontSize(14);
            $header = (new Style)->setFontBold()->setBackgroundColor('DCE8F2');

            $writer->addRow(Row::fromValues(['Rekap Absensi '.config('school.name', config('app.name'))], $title));
            $writer->addRow(Row::fromValues(['Periode', $period]));
            $writer->addRow(Row::fromValues(['Kelas', $filters['class'] ?: 'Semua kelas']));
            $writer->addRow(Row::fromValues(['Hari efektif', $recap['summary']['days']]));
            $writer->addRow(Row::fromValues([]));
            $writer->addRow(Row::fromValues(self::HEADINGS, $header));
            foreach ($recap['rows'] as $i => $row) {
                $writer->addRow(Row::fromValues([$i + 1, $row['name'], $row['nis'], $row['class'],
                    ...array_map(fn ($key) => $row[$key], array_keys(self::STATUSES)),
                    $row['unrecorded'], $row['percentage'] ?? '-']));
            }
            $summary = $recap['summary'];
            $writer->addRow(Row::fromValues(['', 'Total', '', '',
                ...array_map(fn ($key) => $summary[$key], array_keys(self::STATUSES)),
                $summary['unrecorded'], $summary['percentage'] ?? '-'], $bold));
            $writer->close();
        }, $this->fileName($filters, 'xlsx'), [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    private function filters(Request $request): array
    {
        $data = $request->validate([
            'from' => 'nullable|date_format:Y-m-d',
            'to' => 'nullable|date_format:Y-m-d|after_or_equal:from',
            'class' => 'nullable|string|max:100',
            'search' => 'nullable|string|max:100',
        ]);
        $to = Carbon::parse($data['to'] ?? today())->min(today());
        $from = Carbon::parse($data['from'] ?? $to->copy()->startOfMonth())->min($to);
        // Keep exports bounded so a single request cannot scan years of data.
        $from = $from->max($to->copy()->subDays(366));

        return ['from' => $from->toDateString(), 'to' => $to->toDateString(),
            'class' => $data['class'] ?? null, 'search' => $data['search'] ?? null];
    }

    /**
     * @return array{rows: Collection, summary: array}
     */
    private function recap(array $filters): array
    {
        $students = Student::where('status', 'active')
            ->when($filters['class'], fn ($q, $v) => $q->where('class_name', $v))
            ->when($filters['search'], fn ($q, $v) => $q->where(fn ($q) => $q
                ->where('full_name', 'like', '%'.$v.'%')->orWhere('nis', 'like', '%'.$v.'%')->orWhere('nisn', 'like', '%'.$v.'%')))
            ->orderBy('class_name')->orderBy('full_name')->get(['id', 'full_name', 'nis', 'nisn', 'class_name']);

        $range = [$filters['from'], $filters['to']];
        // Days on which the school recorded any attendance; this skips weekends and holidays without configuration.
        $days = Attendance::whereBetween('attendance_date', $range)->distinct()->count('attendance_date');

        $select = collect(self::STATUSES)->keys()
            ->map(fn ($status) => "SUM(CASE WHEN status = '{$status}' THEN 1 ELSE 0 END) as {$status}")
            ->implode(', ');
        $counts = Attendance::whereBetween('attendance_date', $range)->whereIn('student_id', $students->pluck('id'))
            ->groupBy('student_id')->selectRaw("student_id, COUNT(*) as total, {$select}")->get()->keyBy('student_id');

        $rows = $students->map(function (Student $student) use ($counts, $days) {
            $count = $counts->get($student->id);
            $row = ['name' => $student->full_name, 'nis' => $student->nis ?: $student->nisn, 'class' => $student->class_name];
            foreach (array_keys(self::STATUSES) as $status) {
                $row[$status] = (int) ($count?->{$status} ?? 0);
            }
            $row['unrecorded'] = max(0, $days - (int) ($count?->total ?? 0));
            $row['percentage'] = $this->percentage($row['present'] + $row['late'], $days);

            return $row;
        });

        $summary = ['students' => $rows->count(), 'days' => $days, 'unrecorded' => $rows->sum('unrecorded')];
        foreach (array_keys(self::STATUSES) as $status) {
            $summary[$status] = $rows->sum($status);
        }
        $summary['percentage'] = $this->percentage($summary['present'] + $summary['late'], $days * $rows->count());

        return ['rows' => $rows->values(), 'summary' => $summary];
    }

    private function percentage(int $attended, int $expected): ?float
    {
        return $expected > 0 ? round($attended / $expected * 100, 1) : null;
    }

    private function periodLabel(array $filters): string
    {
        return Carbon::parse($filters['from'])->format('d/m/Y').' - '.Carbon::parse($filters['to'])->format('d/m/Y');
    }

    private function fileName(array $filters, string $extension): string
    {
        $class = $filters['class'] ? '_'.Str::slug($filters['class']) : '';

        return "rekap-absensi_{$filters['from']}_{$filters['to']}{$class}.{$extension}";
    }
}
