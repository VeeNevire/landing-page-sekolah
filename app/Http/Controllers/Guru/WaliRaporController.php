<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\Kelas;
use App\Models\Student;
use App\Services\RaporService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use ZipArchive;

class WaliRaporController extends Controller
{
    public function __construct(private RaporService $raporService)
    {
    }

    public function index(Request $request)
    {
        $user = $request->user();
        $kelas = Kelas::where('homeroom_teacher_id', $user->id)->first();

        $students = collect();
        $selectedStudent = null;
        $report = null;

        if ($kelas) {
            $students = Student::where('class_name', $kelas->nama_lengkap)
                ->where('status', 'active')
                ->orderBy('full_name')
                ->get();

            $studentId = $request->query('student_id');
            $selectedStudent = $students->firstWhere('id', $studentId) ?? $students->first();

            if ($selectedStudent) {
                $report = $this->raporService->buildReport($selectedStudent);
            }
        }

        return view('guru.wali.rapor', [
            'kelas' => $kelas,
            'students' => $students,
            'selectedStudent' => $selectedStudent,
            'report' => $report,
            'school' => $this->school(),
        ]);
    }

    public function preview(Request $request, Student $student)
    {
        $this->authorizeStudent($request->user(), $student);

        $report = $this->raporService->buildReport($student);

        return view('guru.wali.rapor-preview', [
            'report' => $report,
            'school' => $this->school(),
        ]);
    }

    public function pdf(Request $request, Student $student)
    {
        $this->authorizeStudent($request->user(), $student);

        $report = $this->raporService->buildReport($student);

        $pdf = Pdf::loadView('guru.wali.rapor-pdf', [
            'report' => $report,
            'school' => $this->school(true),
        ])->setPaper('a4', 'portrait');

        $fileName = 'Rapor_' . $student->nisn . '_' . str_replace(' ', '_', $student->full_name) . '.pdf';

        return $pdf->download($fileName);
    }

    public function pdfSemua(Request $request)
    {
        $user = $request->user();
        $kelas = Kelas::where('homeroom_teacher_id', $user->id)->first();
        abort_unless($kelas, 403, 'Anda belum ditetapkan sebagai wali kelas.');

        $students = Student::where('class_name', $kelas->nama_lengkap)
            ->where('status', 'active')
            ->orderBy('full_name')
            ->get();

        if ($students->isEmpty()) {
            return back()->with('error', 'Tidak ada siswa aktif di kelas binaan.');
        }

        $zipPath = storage_path('app/private/rapor-' . $kelas->nama_lengkap . '-' . now()->format('Ymd-His') . '.zip');
        $zip = new ZipArchive();
        $zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE);

        foreach ($students as $student) {
            $report = $this->raporService->buildReport($student);

            $pdf = Pdf::loadView('guru.wali.rapor-pdf', [
                'report' => $report,
                'school' => $this->school(true),
            ])->setPaper('a4', 'portrait');

            $safeName = preg_replace('/[^A-Za-z0-9_.-]/', '_', $student->full_name);
            $fileName = 'Rapor_' . $student->nisn . '_' . $safeName . '.pdf';

            $zip->addFromString($fileName, $pdf->output());
        }

        $zip->close();

        return response()->download($zipPath)->deleteFileAfterSend(true);
    }

    private function authorizeStudent($user, Student $student): void
    {
        $kelas = Kelas::where('homeroom_teacher_id', $user->id)->first();
        abort_unless($kelas, 403, 'Anda belum ditetapkan sebagai wali kelas.');

        $belongsToClass = Student::where('class_name', $kelas->nama_lengkap)
            ->where('id', $student->id)
            ->exists();

        abort_unless($belongsToClass, 403, 'Siswa ini bukan bagian dari kelas binaan Anda.');
    }

    private function school(bool $forPdf = false): array
    {
        return [
            'name' => config('school.name'),
            'tagline' => config('school.tagline'),
            'address' => config('school.address'),
            'phone' => config('school.phone'),
            'email' => config('school.email'),
            'website' => config('school.website'),
            'npsn' => config('school.npsn'),
            'city' => config('school.city'),
            'logo' => $forPdf ? null : asset(config('school.logo')),
            'is_pdf' => $forPdf,
        ];
    }
}
