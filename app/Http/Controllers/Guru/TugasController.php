<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\AcademicPeriod;
use App\Models\Assessment;
use App\Models\AssessmentScore;
use App\Models\Assignment;
use App\Models\Student;
use App\Models\Submission;
use App\Models\TeachingAssignment;
use App\Services\AuditService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TugasController extends Controller
{
    private function getAssignments($user)
    {
        return $user->teachingAssignments()
            ->with('subject', 'customSubject', 'period')
            ->whereHas('period', fn($q) => $q->where('is_active', true))
            ->get();
    }

    private function subjectName($ta): string
    {
        if ($ta->subject) return $ta->subject->name;
        if ($ta->customSubject) return $ta->customSubject->nama;
        return '-';
    }

    public function index(Request $request)
    {
        $user = $request->user();
        $teachingAssignments = $this->getAssignments($user);
        $selectedTaId = $request->query('ta_id');

        $assignments = Assignment::with('teachingAssignment.subject', 'teachingAssignment.customSubject', 'module')
            ->whereIn('teaching_assignment_id', $teachingAssignments->pluck('id'))
            ->when($selectedTaId, fn($q) => $q->where('teaching_assignment_id', $selectedTaId))
            ->latest()
            ->get();

        $pairs = $teachingAssignments->map(fn($a) => [
            'assignment_id' => $a->id,
            'class_name' => $a->class_name,
            'subject_name' => $this->subjectName($a),
        ])->unique('assignment_id')->values();

        $selectedTa = $selectedTaId ? $teachingAssignments->firstWhere('id', $selectedTaId) : $teachingAssignments->first();

        return view('guru.tugas.index', [
            'assignments' => $assignments,
            'pairs' => $pairs,
            'selectedTa' => $selectedTa,
            'teachingAssignments' => $teachingAssignments,
        ]);
    }

    public function create(Request $request)
    {
        $user = $request->user();
        $teachingAssignments = $this->getAssignments($user);
        $taId = $request->query('ta_id');

        $selectedTa = $taId ? $teachingAssignments->firstWhere('id', $taId) : $teachingAssignments->first();

        if (!$selectedTa) {
            return redirect()->route('guru.tugas.index')->with('error', 'Pilih kelas & mapel terlebih dahulu.');
        }

        $modules = $selectedTa->modules()->get();

        return view('guru.tugas.form', [
            'teachingAssignments' => $teachingAssignments,
            'selectedTa' => $selectedTa,
            'modules' => $modules,
            'assignment' => null,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'teaching_assignment_id' => 'required|exists:teaching_assignments,id',
            'module_id' => 'nullable|exists:course_modules,id',
            'title' => 'required|string|max:255',
            'instructions' => 'required|string',
            'due_date' => 'nullable|date',
            'max_score' => 'required|numeric|min:1|max:999',
            'allow_late_submission' => 'boolean',
            'attachment' => 'nullable|file|max:51200',
        ]);

        $user = $request->user();
        if (!$user->teachingAssignments()->where('id', $validated['teaching_assignment_id'])->exists()) {
            abort(403);
        }

        $data = [
            'teaching_assignment_id' => $validated['teaching_assignment_id'],
            'module_id' => $validated['module_id'] ?? null,
            'title' => $validated['title'],
            'instructions' => $validated['instructions'],
            'due_date' => $validated['due_date'] ?? null,
            'max_score' => $validated['max_score'],
            'allow_late_submission' => $request->boolean('allow_late_submission'),
        ];

        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $path = $file->store('lms/assignments/' . $validated['teaching_assignment_id'], 'public');
            $data['attachment_path'] = $path;
            $data['attachment_name'] = $file->getClientOriginalName();
        }

        $assignment = Assignment::create($data);
        AuditService::log('assignment.create', 'Assignment', $assignment->id, $assignment->title);

        return redirect()->route('guru.tugas.index', ['ta_id' => $validated['teaching_assignment_id']])
            ->with('success', 'Tugas berhasil dibuat.');
    }

    public function edit(Request $request, Assignment $assignment)
    {
        $user = $request->user();
        if (!$user->teachingAssignments()->where('id', $assignment->teaching_assignment_id)->exists()) {
            abort(403);
        }

        $teachingAssignments = $this->getAssignments($user);
        $selectedTa = $assignment->teachingAssignment;
        $modules = $selectedTa->modules()->get();

        return view('guru.tugas.form', [
            'teachingAssignments' => $teachingAssignments,
            'selectedTa' => $selectedTa,
            'modules' => $modules,
            'assignment' => $assignment,
        ]);
    }

    public function update(Request $request, Assignment $assignment)
    {
        $user = $request->user();
        if (!$user->teachingAssignments()->where('id', $assignment->teaching_assignment_id)->exists()) {
            abort(403);
        }

        $validated = $request->validate([
            'module_id' => 'nullable|exists:course_modules,id',
            'title' => 'required|string|max:255',
            'instructions' => 'required|string',
            'due_date' => 'nullable|date',
            'max_score' => 'required|numeric|min:1|max:999',
            'allow_late_submission' => 'boolean',
            'attachment' => 'nullable|file|max:51200',
        ]);

        $data = [
            'module_id' => $validated['module_id'] ?? null,
            'title' => $validated['title'],
            'instructions' => $validated['instructions'],
            'due_date' => $validated['due_date'] ?? null,
            'max_score' => $validated['max_score'],
            'allow_late_submission' => $request->boolean('allow_late_submission'),
        ];

        if ($request->hasFile('attachment')) {
            if ($assignment->attachment_path) {
                Storage::disk('public')->delete($assignment->attachment_path);
            }
            $file = $request->file('attachment');
            $path = $file->store('lms/assignments/' . $assignment->teaching_assignment_id, 'public');
            $data['attachment_path'] = $path;
            $data['attachment_name'] = $file->getClientOriginalName();
        }

        $assignment->update($data);
        AuditService::log('assignment.update', 'Assignment', $assignment->id, $assignment->title);

        return redirect()->route('guru.tugas.index', ['ta_id' => $assignment->teaching_assignment_id])
            ->with('success', 'Tugas berhasil diperbarui.');
    }

    public function destroy(Request $request, Assignment $assignment)
    {
        $user = $request->user();
        if (!$user->teachingAssignments()->where('id', $assignment->teaching_assignment_id)->exists()) {
            abort(403);
        }

        if ($assignment->attachment_path) {
            Storage::disk('public')->delete($assignment->attachment_path);
        }

        AuditService::log('assignment.delete', 'Assignment', $assignment->id, $assignment->title);
        $assignment->delete();

        return back()->with('success', 'Tugas berhasil dihapus.');
    }

    public function publish(Request $request, Assignment $assignment)
    {
        $user = $request->user();
        if (!$user->teachingAssignments()->where('id', $assignment->teaching_assignment_id)->exists()) {
            abort(403);
        }

        $assignment->update([
            'published_at' => $assignment->published_at ? null : now(),
        ]);

        $status = $assignment->published_at ? 'dipublikasikan' : 'ditarik';
        AuditService::log('assignment.publish', 'Assignment', $assignment->id, $assignment->title);

        return back()->with('success', "Tugas berhasil {$status}.");
    }

    public function submissions(Request $request, Assignment $assignment)
    {
        $user = $request->user();
        if (!$user->teachingAssignments()->where('id', $assignment->teaching_assignment_id)->exists()) {
            abort(403);
        }

        $ta = $assignment->teachingAssignment;
        $students = Student::with(['submissions' => fn($q) => $q->where('assignment_id', $assignment->id)->with('grade')])
            ->where('class_name', $ta->class_name)
            ->where('status', 'active')
            ->orderBy('full_name')
            ->get();

        return view('guru.tugas.submissions', [
            'assignment' => $assignment,
            'students' => $students,
            'ta' => $ta,
        ]);
    }

    public function grade(Request $request, Submission $submission)
    {
        $user = $request->user();
        $assignment = $submission->assignment;

        if (!$user->teachingAssignments()->where('id', $assignment->teaching_assignment_id)->exists()) {
            abort(403);
        }

        $validated = $request->validate([
            'score' => 'required|numeric|min:0|max:' . $assignment->max_score,
            'feedback' => 'nullable|string|max:1000',
        ]);

        if ($submission->isGraded()) {
            $submission->grade->update([
                'score' => $validated['score'],
                'feedback' => $validated['feedback'] ?? null,
                'graded_by' => $user->id,
                'graded_at' => now(),
            ]);
        } else {
            $submission->grade()->create([
                'score' => $validated['score'],
                'feedback' => $validated['feedback'] ?? null,
                'graded_by' => $user->id,
                'graded_at' => now(),
            ]);
        }

        $assessment = Assessment::firstOrCreate(
            [
                'teaching_assignment_id' => $assignment->teaching_assignment_id,
                'title' => $assignment->title,
                'component' => 'assignment',
            ],
            [
                'assessment_date' => now(),
                'max_score' => $assignment->max_score,
                'published_at' => now(),
            ]
        );

        AssessmentScore::updateOrCreate(
            [
                'assessment_id' => $assessment->id,
                'student_id' => $submission->student_id,
            ],
            [
                'score' => $validated['score'],
                'feedback' => $validated['feedback'] ?? null,
                'graded_at' => now(),
            ]
        );

        AuditService::log('submission.grade', 'Submission', $submission->id, null);

        return back()->with('success', 'Nilai berhasil disimpan untuk ' . $submission->student->full_name);
    }
}
