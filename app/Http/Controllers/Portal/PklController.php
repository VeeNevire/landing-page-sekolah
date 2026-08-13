<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\AcademicPeriod;
use App\Models\PklActivity;
use App\Models\PklPlacement;
use Illuminate\Http\Request;

class PklController extends Controller
{
    public function index(Request $request)
    {
        $data = $this->resolve($request);

        if (! $data) {
            return view('portal.pkl', [
                'students' => collect(),
                'selectedStudent' => null,
                'selectedStudentId' => null,
                'selectedStudentInitials' => 'S',
                'demoStudent' => null,
                'placements' => collect(),
                'activities' => collect(),
                'stats' => ['total' => 0, 'approved' => 0, 'pending' => 0, 'rejected' => 0],
                'activePlacement' => null,
            ]);
        }

        $student = $data['selectedStudent'];

        $placements = PklPlacement::where('student_id', $student->id)
            ->with('company', 'guruPembimbing')
            ->withCount([
                'activities',
                'activities as approved_count' => fn ($q) => $q->where('status', 'approved'),
            ])
            ->orderByDesc('start_date')
            ->get();

        $activePlacement = $placements->firstWhere('status', 'active') ?? $placements->first();

        $activities = collect();
        if ($activePlacement) {
            $activities = PklActivity::where('placement_id', $activePlacement->id)
                ->with('approver')
                ->orderByDesc('tanggal')
                ->orderByDesc('id')
                ->get();
        }

        $stats = [
            'total' => $activePlacement?->activities_count ?? 0,
            'approved' => $activePlacement?->approved_count ?? 0,
            'pending' => $activities->where('status', 'pending')->count(),
            'rejected' => $activities->where('status', 'rejected')->count(),
        ];

        return view('portal.pkl', array_merge($data, [
            'placements' => $placements,
            'activePlacement' => $activePlacement,
            'activities' => $activities,
            'stats' => $stats,
        ]));
    }

    public function exportCsv(Request $request)
    {
        $data = $this->resolve($request);

        if (! $data) {
            return back()->with('error', 'Sesi tidak valid.');
        }

        $student = $data['selectedStudent'];
        $placement = PklPlacement::where('student_id', $student->id)
            ->with('company')
            ->orderByDesc('start_date')
            ->first();

        $activities = $placement
            ? PklActivity::where('placement_id', $placement->id)->orderBy('tanggal')->get()
            : collect();

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="pkl_'.$student->nisn.'.csv"',
        ];

        $callback = function () use ($student, $placement, $activities) {
            $output = fopen('php://output', 'w');
            fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

            fputcsv($output, ['Rekap Aktivitas PKL - '.$student->full_name]);
            fputcsv($output, ['NISN: '.$student->nisn, 'Kelas: '.$student->class_name]);
            fputcsv($output, ['Perusahaan: '.($placement?->company?->nama ?? '-'), 'Pembimbing: '.($placement?->guruPembimbing?->full_name ?? $placement?->guruPembimbing?->name ?? '-')]);
            if ($placement) {
                fputcsv($output, ['Rentang PKL: '.$placement->start_date->format('d M Y').' - '.$placement->end_date->format('d M Y'), 'Status: '.$placement->status_label]);
            }
            fputcsv($output, []);

            fputcsv($output, ['Tanggal', 'Kegiatan', 'Hasil', 'Status', 'Catatan Pembimbing']);
            foreach ($activities as $a) {
                fputcsv($output, [
                    $a->tanggal->format('d M Y'),
                    $a->aktivitas,
                    $a->keterangan ?? '',
                    $a->status_label,
                    $a->catatan_pembimbing ?? '',
                ]);
            }

            fclose($output);
        };

        return response()->stream($callback, 200, $headers);
    }

    private function resolve(Request $request): ?array
    {
        $user = $request->user();
        $students = $user->students()->where('status', 'active')->get();

        if ($students->isEmpty()) {
            return null;
        }

        $studentId = $request->query('student_id', $students->first()->id);
        $selectedStudent = $students->firstWhere('id', $studentId) ?? $students->first();

        $period = AcademicPeriod::where('is_active', true)->first();

        return [
            'students' => $students,
            'selectedStudent' => $selectedStudent,
            'selectedStudentId' => $selectedStudent->id,
            'selectedStudentInitials' => strtoupper(mb_substr($selectedStudent->full_name ?? 'S', 0, 1)),
            'demoStudent' => [
                'id' => $selectedStudent->id,
                'name' => $selectedStudent->full_name,
                'initials' => strtoupper(mb_substr($selectedStudent->full_name, 0, 2)),
                'nisn' => $selectedStudent->nisn,
                'class' => $selectedStudent->class_name,
                'program' => $selectedStudent->program_name,
                'homeroom_teacher' => $selectedStudent->homeroomTeacher?->full_name ?? '-',
                'semester' => $period?->semester === 'ganjil' ? 'Ganjil' : 'Genap',
                'academic_year' => $period?->academic_year ?? '-',
            ],
        ];
    }
}
