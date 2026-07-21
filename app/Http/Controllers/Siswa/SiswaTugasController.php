<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Models\Assignment;
use App\Models\Student;
use App\Models\Submission;
use App\Models\TeachingAssignment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SiswaTugasController extends Controller
{
    private function resolve(Request $request): ?array
    {
        $user = $request->user();
        $student = Student::with('kelas', 'jurusan')
            ->where('user_id', $user->id)
            ->first();

        if (!$student) {
            return null;
        }

        $period = \App\Models\AcademicPeriod::where('is_active', true)->first();
        if (!$period) {
            return null;
        }

        $initials = strtoupper(substr($user->name, 0, 1));

        return compact('user', 'student', 'period', 'initials');
    }

    public function index(Request $request)
    {
        $data = $this->resolve($request);
        if (!$data) return redirect()->route('login');

        $student = $data['student'];
        $period = $data['period'];

        $assignmentIds = TeachingAssignment::where('period_id', $period->id)
            ->where('class_name', $student->class_name)
            ->pluck('id');

        $assignments = Assignment::with('teachingAssignment.subject', 'teachingAssignment.customSubject', 'module')
            ->whereIn('teaching_assignment_id', $assignmentIds)
            ->published()
            ->latest()
            ->get();

        $submittedIds = Submission::where('student_id', $student->id)
            ->whereIn('assignment_id', $assignments->pluck('id'))
            ->pluck('assignment_id', 'assignment_id');

        return view('siswa.tugas.index', [
            'student' => $student,
            'period' => $period,
            'initials' => $data['initials'],
            'assignments' => $assignments,
            'submittedIds' => $submittedIds,
        ]);
    }

    public function show(Request $request, Assignment $assignment)
    {
        $data = $this->resolve($request);
        if (!$data) return redirect()->route('login');

        $student = $data['student'];
        $period = $data['period'];

        $ta = $assignment->teachingAssignment;
        if ($ta->class_name !== $student->class_name || $ta->period_id !== $period->id || !$assignment->published_at) {
            abort(403);
        }

        $submission = Submission::with('grade')
            ->where('assignment_id', $assignment->id)
            ->where('student_id', $student->id)
            ->first();

        return view('siswa.tugas.show', [
            'student' => $student,
            'period' => $period,
            'initials' => $data['initials'],
            'assignment' => $assignment,
            'submission' => $submission,
        ]);
    }

    public function submit(Request $request, Assignment $assignment)
    {
        $data = $this->resolve($request);
        if (!$data) return redirect()->route('login');

        $student = $data['student'];
        $period = $data['period'];

        $ta = $assignment->teachingAssignment;
        if ($ta->class_name !== $student->class_name || $ta->period_id !== $period->id || !$assignment->published_at) {
            abort(403);
        }

        $validated = $request->validate([
            'file' => 'required|file|mimes:pdf,doc,docx,ppt,pptx,xls,xlsx,jpg,jpeg,png,gif,zip,rar|max:51200',
            'notes' => 'nullable|string|max:500',
        ]);

        if ($assignment->due_date && now()->gt($assignment->due_date) && !$assignment->allow_late_submission) {
            return back()->with('error', 'Batas pengumpulan tugas sudah lewat.');
        }

        $existing = Submission::where('assignment_id', $assignment->id)
            ->where('student_id', $student->id)
            ->first();

        if ($existing) {
            Storage::disk('public')->delete($existing->file_path);
            $existing->delete();
        }

        $file = $request->file('file');
        $path = $file->store('lms/submissions/' . $assignment->id, 'public');

        $isLate = $assignment->due_date && now()->gt($assignment->due_date);

        Submission::create([
            'assignment_id' => $assignment->id,
            'student_id' => $student->id,
            'file_path' => $path,
            'file_name' => $file->getClientOriginalName(),
            'file_size' => $file->getSize(),
            'notes' => $validated['notes'] ?? null,
            'submitted_at' => now(),
            'is_late' => $isLate,
        ]);

        return redirect()->route('siswa.tugas.show', $assignment->id)
            ->with('success', 'Tugas berhasil dikumpulkan.');
    }
}
