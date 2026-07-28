<?php

namespace App\Http\Controllers\Alumni;

use App\Http\Controllers\Controller;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class AlumniController extends Controller
{
    public function dashboard()
    {
        $user = Auth::user();
        $student = $user->studentProfile;

        if (!$student) {
            return redirect()->route('beranda')->with('error', 'Data alumni tidak ditemukan.');
        }

        $certificates = $student->certificates ?? collect();
        $projects = $student->projects ?? collect();

        $stats = [
            'certificates' => $certificates->count(),
            'projects' => $projects->count(),
            'graduation_year' => $student->graduation_year,
            'program' => $student->program_name,
            'alumni_status' => $student->alumni_status,
        ];

        return view('alumni.dashboard', compact('student', 'stats', 'certificates', 'projects'));
    }

    public function profil()
    {
        $user = Auth::user();
        $student = $user->studentProfile;

        if (!$student) {
            return redirect()->route('beranda')->with('error', 'Data alumni tidak ditemukan.');
        }

        $scores = \App\Models\AssessmentScore::where('student_id', $student->id)
            ->with('assessment:id,title,component,teaching_assignment_id',
                'assessment.teachingAssignment:id,subject_id,custom_subject_id',
                'assessment.teachingAssignment.subject:id,name,code',
                'assessment.teachingAssignment.customSubject:id,nama,kode')
            ->get()
            ->groupBy(fn($s) => $s->assessment->teachingAssignment?->subject?->name
                ?? $s->assessment->teachingAssignment?->customSubject?->nama
                ?? 'Unknown');

        $subjects = [];
        foreach ($scores as $subjectName => $items) {
            $vals = $items->pluck('score')->filter();
            $subjects[] = [
                'name' => $subjectName,
                'avg' => $vals->isNotEmpty() ? round($vals->avg(), 1) : '-',
                'count' => $items->count(),
            ];
        }

        return view('alumni.profil', compact('student', 'subjects'));
    }

    public function profilUpdate(Request $request)
    {
        $user = Auth::user();
        $student = $user->studentProfile;

        if (!$student) {
            return redirect()->route('beranda')->with('error', 'Data alumni tidak ditemukan.');
        }

        $validated = $request->validate([
            'alumni_status' => 'required|in:working,studying,looking_for_job',
            'graduation_year' => 'required|integer|min:2000|max:2099',
        ]);

        $student->update($validated);

        // Update User's own email/name if they choose to change it
        $request->validate([
            'email' => 'required|email|unique:users,email,' . $user->id,
            'name' => 'required|string|max:255',
        ]);

        $user->update([
            'email' => $request->email,
            'name' => $request->name,
        ]);

        return redirect()->route('alumni.profil')->with('success', 'Profil berhasil diperbarui.');
    }

    public function cvUpload(Request $request)
    {
        $user = Auth::user();
        $student = $user->studentProfile;

        if (!$student) {
            return redirect()->route('beranda')->with('error', 'Data alumni tidak ditemukan.');
        }

        $request->validate([
            'cv' => 'required|file|mimes:pdf,doc,docx|max:2048',
        ]);

        if ($student->cv_path) {
            Storage::disk('public')->delete($student->cv_path);
        }

        $path = $request->file('cv')->store('alumni-cv', 'public');
        $student->update(['cv_path' => $path]);

        return redirect()->route('alumni.profil')->with('success', 'CV berhasil diunggah.');
    }

    public function sertifikat()
    {
        $user = Auth::user();
        $student = $user->studentProfile;

        if (!$student) {
            return redirect()->route('beranda')->with('error', 'Data alumni tidak ditemukan.');
        }

        $certificates = $student->certificates()->latest()->get();

        return view('alumni.sertifikat', compact('student', 'certificates'));
    }

    public function sertifikatStore(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'issuer' => 'required|string|max:255',
            'issue_date' => 'required|date',
            'certificate_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'certificate_url' => 'nullable|url',
        ]);

        $user = Auth::user();
        $student = $user->studentProfile;

        $path = null;
        if ($request->hasFile('certificate_file')) {
            $path = $request->file('certificate_file')->store('alumni-certificates', 'public');
        }

        $student->certificates()->create([
            'name' => $request->name,
            'issuer' => $request->issuer,
            'issue_date' => $request->issue_date,
            'certificate_file' => $path,
            'certificate_url' => $request->certificate_url,
        ]);

        return redirect()->route('alumni.sertifikat')->with('success', 'Sertifikat berhasil ditambahkan.');
    }

    public function sertifikatUpdate(Request $request, \App\Models\Certificate $certificate)
    {
        $user = Auth::user();
        $student = $user->studentProfile;

        if ($certificate->student_id !== $student->id) {
            abort(403);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'issuer' => 'required|string|max:255',
            'issue_date' => 'required|date',
            'certificate_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'certificate_url' => 'nullable|url',
        ]);

        if ($request->hasFile('certificate_file')) {
            if ($certificate->certificate_file) {
                Storage::disk('public')->delete($certificate->certificate_file);
            }
            $certificate->certificate_file = $request->file('certificate_file')->store('alumni-certificates', 'public');
        }

        $certificate->update([
            'name' => $request->name,
            'issuer' => $request->issuer,
            'issue_date' => $request->issue_date,
            'certificate_url' => $request->certificate_url,
        ]);

        return redirect()->route('alumni.sertifikat')->with('success', 'Sertifikat berhasil diperbarui.');
    }

    public function sertifikatDestroy(\App\Models\Certificate $certificate)
    {
        $user = Auth::user();
        $student = $user->studentProfile;

        if ($certificate->student_id !== $student->id) {
            abort(403);
        }

        if ($certificate->certificate_file) {
            Storage::disk('public')->delete($certificate->certificate_file);
        }

        $certificate->delete();

        return redirect()->route('alumni.sertifikat')->with('success', 'Sertifikat berhasil dihapus.');
    }

    public function proyek()
    {
        $user = Auth::user();
        $student = $user->studentProfile;

        if (!$student) {
            return redirect()->route('beranda')->with('error', 'Data alumni tidak ditemukan.');
        }

        $projects = $student->projects()->latest()->get();

        return view('alumni.proyek', compact('student', 'projects'));
    }

    public function proyekStore(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'project_url' => 'nullable|url',
            'github_url' => 'nullable|url',
            'project_file' => 'nullable|file|mimes:pdf,zip,rar|max:10240',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
        ]);

        $user = Auth::user();
        $student = $user->studentProfile;

        $path = null;
        if ($request->hasFile('project_file')) {
            $path = $request->file('project_file')->store('alumni-projects', 'public');
        }

        $student->projects()->create([
            'title' => $request->title,
            'description' => $request->description,
            'project_url' => $request->project_url,
            'github_url' => $request->github_url,
            'project_file' => $path,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
        ]);

        return redirect()->route('alumni.proyek')->with('success', 'Proyek berhasil ditambahkan.');
    }

    public function proyekUpdate(Request $request, \App\Models\Project $project)
    {
        $user = Auth::user();
        $student = $user->studentProfile;

        if ($project->student_id !== $student->id) {
            abort(403);
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'project_url' => 'nullable|url',
            'github_url' => 'nullable|url',
            'project_file' => 'nullable|file|mimes:pdf,zip,rar|max:10240',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
        ]);

        if ($request->hasFile('project_file')) {
            if ($project->project_file) {
                Storage::disk('public')->delete($project->project_file);
            }
            $project->project_file = $request->file('project_file')->store('alumni-projects', 'public');
        }

        $project->update([
            'title' => $request->title,
            'description' => $request->description,
            'project_url' => $request->project_url,
            'github_url' => $request->github_url,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
        ]);

        return redirect()->route('alumni.proyek')->with('success', 'Proyek berhasil diperbarui.');
    }

    public function proyekDestroy(\App\Models\Project $project)
    {
        $user = Auth::user();
        $student = $user->studentProfile;

        if ($project->student_id !== $student->id) {
            abort(403);
        }

        if ($project->project_file) {
            Storage::disk('public')->delete($project->project_file);
        }

        $project->delete();

        return redirect()->route('alumni.proyek')->with('success', 'Proyek berhasil dihapus.');
    }
}