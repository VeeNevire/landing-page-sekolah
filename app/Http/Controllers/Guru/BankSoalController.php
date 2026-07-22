<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\QuestionBank;
use App\Models\TeachingAssignment;
use App\Services\AuditService;
use Illuminate\Http\Request;

class BankSoalController extends Controller
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
        $filterType = $request->query('type');
        $filterTopic = $request->query('topic');

        $questions = QuestionBank::with('teachingAssignment.subject', 'teachingAssignment.customSubject')
            ->whereIn('teaching_assignment_id', $teachingAssignments->pluck('id'))
            ->when($selectedTaId, fn($q) => $q->where('teaching_assignment_id', $selectedTaId))
            ->when($filterType, fn($q) => $q->where('question_type', $filterType))
            ->when($filterTopic, fn($q) => $q->where('topic', $filterTopic))
            ->latest()
            ->get();

        $topics = QuestionBank::whereIn('teaching_assignment_id', $teachingAssignments->pluck('id'))
            ->whereNotNull('topic')
            ->distinct()
            ->pluck('topic');

        $pairs = $teachingAssignments->map(fn($a) => [
            'assignment_id' => $a->id,
            'class_name' => $a->class_name,
            'subject_name' => $this->subjectName($a),
        ])->unique('assignment_id')->values();

        $selectedTa = $selectedTaId ? $teachingAssignments->firstWhere('id', $selectedTaId) : $teachingAssignments->first();

        return view('guru.bank-soal.index', [
            'questions' => $questions,
            'topics' => $topics,
            'pairs' => $pairs,
            'selectedTa' => $selectedTa,
            'filterType' => $filterType,
            'filterTopic' => $filterTopic,
        ]);
    }

    public function create(Request $request)
    {
        $user = $request->user();
        $teachingAssignments = $this->getAssignments($user);

        $pairs = $teachingAssignments->map(fn($a) => [
            'assignment_id' => $a->id,
            'class_name' => $a->class_name,
            'subject_name' => $this->subjectName($a),
        ])->unique('assignment_id')->values();

        $presetTopic = $request->query('topic', '');

        return view('guru.bank-soal.buat', [
            'pairs' => $pairs,
            'presetTopic' => $presetTopic,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'teaching_assignment_id' => 'required|exists:teaching_assignments,id',
            'topic' => 'required|string|max:255',
            'questions' => 'required|array|min:1',
            'questions.*.type' => 'required|in:multiple_choice,essay,true_false',
            'questions.*.question_text' => 'required|string',
            'questions.*.options' => 'nullable|array',
            'questions.*.correct_answer' => 'nullable|string|max:500',
            'questions.*.points' => 'required|numeric|min:0.5|max:999',
            'questions.*.explanation' => 'nullable|string',
        ]);

        $user = $request->user();
        if (!$user->teachingAssignments()->where('id', $validated['teaching_assignment_id'])->exists()) {
            abort(403);
        }

        $created = 0;

        foreach ($validated['questions'] as $data) {
            $options = null;
            if ($data['type'] === 'multiple_choice' && isset($data['options'])) {
                $labels = ['A', 'B', 'C', 'D'];
                $options = [];
                foreach ($labels as $label) {
                    if (isset($data['options'][$label]) && $data['options'][$label]) {
                        $options[] = ['label' => $label, 'text' => $data['options'][$label]];
                    }
                }
            }

            QuestionBank::create([
                'teaching_assignment_id' => $validated['teaching_assignment_id'],
                'topic' => $validated['topic'],
                'question_type' => $data['type'],
                'question_text' => $data['question_text'],
                'options' => $options,
                'correct_answer' => $data['correct_answer'] ?? null,
                'points' => $data['points'],
                'explanation' => $data['explanation'] ?? null,
            ]);

            $created++;
        }

        AuditService::log('question_bank.bulk_create', 'QuestionBank', $validated['teaching_assignment_id'], null);

        return redirect()->route('guru.bank-soal.index', ['ta_id' => $validated['teaching_assignment_id']])
            ->with('success', "{$created} soal berhasil ditambahkan ke bank soal.");
    }

    public function update(Request $request, QuestionBank $questionBank)
    {
        $user = $request->user();
        if (!$user->teachingAssignments()->where('id', $questionBank->teaching_assignment_id)->exists()) {
            abort(403);
        }

        $validated = $request->validate([
            'question_type' => 'required|in:multiple_choice,essay,true_false',
            'question_text' => 'required|string',
            'options' => 'nullable|array',
            'options.*.label' => 'required|string|max:10',
            'options.*.text' => 'required|string|max:500',
            'correct_answer' => 'nullable|string|max:500',
            'points' => 'required|numeric|min:0.5|max:999',
            'explanation' => 'nullable|string',
        ]);

        $questionBank->update([
            'question_type' => $validated['question_type'],
            'question_text' => $validated['question_text'],
            'options' => $validated['question_type'] === 'multiple_choice' ? $validated['options'] : null,
            'correct_answer' => $validated['correct_answer'],
            'points' => $validated['points'],
            'explanation' => $validated['explanation'] ?? null,
        ]);

        AuditService::log('question_bank.update', 'QuestionBank', $questionBank->id, null);

        return redirect()->route('guru.bank-soal.index', ['ta_id' => $questionBank->teaching_assignment_id])
            ->with('success', 'Soal berhasil diperbarui.');
    }

    public function edit(Request $request, QuestionBank $questionBank)
    {
        $user = $request->user();
        if (!$user->teachingAssignments()->where('id', $questionBank->teaching_assignment_id)->exists()) {
            abort(403);
        }

        return response()->json($questionBank);
    }

    public function destroy(Request $request, QuestionBank $questionBank)
    {
        $user = $request->user();
        if (!$user->teachingAssignments()->where('id', $questionBank->teaching_assignment_id)->exists()) {
            abort(403);
        }

        AuditService::log('question_bank.delete', 'QuestionBank', $questionBank->id, null);
        $questionBank->delete();

        return back()->with('success', 'Soal berhasil dihapus dari bank soal.');
    }
}
