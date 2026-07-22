<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\AcademicPeriod;
use App\Models\Assessment;
use App\Models\AssessmentScore;
use App\Models\QuestionBank;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Models\QuizAnswer;
use App\Models\QuizQuestion;
use App\Models\Student;
use App\Services\AuditService;
use Illuminate\Http\Request;

class KuisController extends Controller
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

        $quizzes = Quiz::with('teachingAssignment.subject', 'teachingAssignment.customSubject', 'module')
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

        return view('guru.kuis.index', [
            'quizzes' => $quizzes,
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
            return redirect()->route('guru.kuis.index')->with('error', 'Pilih kelas & mapel terlebih dahulu.');
        }

        $modules = $selectedTa->modules()->get();
        $bankSoal = QuestionBank::where('teaching_assignment_id', $selectedTa->id)->latest()->get();

        return view('guru.kuis.form', [
            'teachingAssignments' => $teachingAssignments,
            'selectedTa' => $selectedTa,
            'modules' => $modules,
            'bankSoal' => $bankSoal,
            'quiz' => null,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'teaching_assignment_id' => 'required|exists:teaching_assignments,id',
            'module_id' => 'nullable|exists:course_modules,id',
            'title' => 'required|string|max:255',
            'instructions' => 'nullable|string',
            'time_limit' => 'nullable|integer|min:1|max:999',
            'max_attempts' => 'required|integer|min:1|max:10',
            'shuffle_questions' => 'boolean',
            'shuffle_options' => 'boolean',
            'show_result_immediately' => 'boolean',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'questions' => 'required|array|min:1',
            'questions.*.id' => 'required|exists:question_banks,id',
            'questions.*.points' => 'required|numeric|min:0.5|max:999',
        ]);

        $user = $request->user();
        if (!$user->teachingAssignments()->where('id', $validated['teaching_assignment_id'])->exists()) {
            abort(403);
        }

        $quiz = Quiz::create([
            'teaching_assignment_id' => $validated['teaching_assignment_id'],
            'module_id' => $validated['module_id'] ?? null,
            'title' => $validated['title'],
            'instructions' => $validated['instructions'] ?? null,
            'time_limit' => $validated['time_limit'] ?? null,
            'max_attempts' => $validated['max_attempts'],
            'shuffle_questions' => $request->boolean('shuffle_questions'),
            'shuffle_options' => $request->boolean('shuffle_options'),
            'show_result_immediately' => $request->boolean('show_result_immediately'),
            'start_date' => $validated['start_date'] ?? null,
            'end_date' => $validated['end_date'] ?? null,
        ]);

        foreach ($validated['questions'] as $order => $q) {
            QuizQuestion::create([
                'quiz_id' => $quiz->id,
                'question_bank_id' => $q['id'],
                'order' => $order + 1,
                'points' => $q['points'],
            ]);
        }

        AuditService::log('quiz.create', 'Quiz', $quiz->id, $quiz->title);

        return redirect()->route('guru.kuis.index', ['ta_id' => $validated['teaching_assignment_id']])
            ->with('success', 'Kuis berhasil dibuat.');
    }

    public function edit(Request $request, Quiz $quiz)
    {
        $user = $request->user();
        if (!$user->teachingAssignments()->where('id', $quiz->teaching_assignment_id)->exists()) {
            abort(403);
        }

        $teachingAssignments = $this->getAssignments($user);
        $selectedTa = $quiz->teachingAssignment;
        $modules = $selectedTa->modules()->get();
        $bankSoal = QuestionBank::where('teaching_assignment_id', $selectedTa->id)->latest()->get();
        $quiz->load('questions.questionBank');

        return view('guru.kuis.form', [
            'teachingAssignments' => $teachingAssignments,
            'selectedTa' => $selectedTa,
            'modules' => $modules,
            'bankSoal' => $bankSoal,
            'quiz' => $quiz,
        ]);
    }

    public function update(Request $request, Quiz $quiz)
    {
        $user = $request->user();
        if (!$user->teachingAssignments()->where('id', $quiz->teaching_assignment_id)->exists()) {
            abort(403);
        }

        $validated = $request->validate([
            'module_id' => 'nullable|exists:course_modules,id',
            'title' => 'required|string|max:255',
            'instructions' => 'nullable|string',
            'time_limit' => 'nullable|integer|min:1|max:999',
            'max_attempts' => 'required|integer|min:1|max:10',
            'shuffle_questions' => 'boolean',
            'shuffle_options' => 'boolean',
            'show_result_immediately' => 'boolean',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'questions' => 'required|array|min:1',
            'questions.*.id' => 'required|exists:question_banks,id',
            'questions.*.points' => 'required|numeric|min:0.5|max:999',
        ]);

        $quiz->update([
            'module_id' => $validated['module_id'] ?? null,
            'title' => $validated['title'],
            'instructions' => $validated['instructions'] ?? null,
            'time_limit' => $validated['time_limit'] ?? null,
            'max_attempts' => $validated['max_attempts'],
            'shuffle_questions' => $request->boolean('shuffle_questions'),
            'shuffle_options' => $request->boolean('shuffle_options'),
            'show_result_immediately' => $request->boolean('show_result_immediately'),
            'start_date' => $validated['start_date'] ?? null,
            'end_date' => $validated['end_date'] ?? null,
        ]);

        $quiz->questions()->delete();
        foreach ($validated['questions'] as $order => $q) {
            QuizQuestion::create([
                'quiz_id' => $quiz->id,
                'question_bank_id' => $q['id'],
                'order' => $order + 1,
                'points' => $q['points'],
            ]);
        }

        AuditService::log('quiz.update', 'Quiz', $quiz->id, $quiz->title);

        return redirect()->route('guru.kuis.index', ['ta_id' => $quiz->teaching_assignment_id])
            ->with('success', 'Kuis berhasil diperbarui.');
    }

    public function destroy(Request $request, Quiz $quiz)
    {
        $user = $request->user();
        if (!$user->teachingAssignments()->where('id', $quiz->teaching_assignment_id)->exists()) {
            abort(403);
        }

        AuditService::log('quiz.delete', 'Quiz', $quiz->id, $quiz->title);
        $quiz->delete();

        return back()->with('success', 'Kuis berhasil dihapus.');
    }

    public function publish(Request $request, Quiz $quiz)
    {
        $user = $request->user();
        if (!$user->teachingAssignments()->where('id', $quiz->teaching_assignment_id)->exists()) {
            abort(403);
        }

        $quiz->update([
            'published_at' => $quiz->published_at ? null : now(),
        ]);

        $status = $quiz->published_at ? 'dipublikasikan' : 'ditarik';
        AuditService::log('quiz.publish', 'Quiz', $quiz->id, $quiz->title);

        return back()->with('success', "Kuis berhasil {$status}.");
    }

    public function hasil(Request $request, Quiz $quiz)
    {
        $user = $request->user();
        if (!$user->teachingAssignments()->where('id', $quiz->teaching_assignment_id)->exists()) {
            abort(403);
        }

        $ta = $quiz->teachingAssignment;
        $quiz->load('questions.questionBank');

        $students = Student::with(['quizAttempts' => fn($q) => $q->where('quiz_id', $quiz->id)->with('answers')])
            ->where('class_name', $ta->class_name)
            ->where('status', 'active')
            ->orderBy('full_name')
            ->get();

        return view('guru.kuis.hasil', [
            'quiz' => $quiz,
            'students' => $students,
            'ta' => $ta,
        ]);
    }

    public function nilaiEssay(Request $request, QuizAttempt $attempt)
    {
        $user = $request->user();
        $quiz = $attempt->quiz;

        if (!$user->teachingAssignments()->where('id', $quiz->teaching_assignment_id)->exists()) {
            abort(403);
        }

        $validated = $request->validate([
            'answers' => 'required|array',
            'answers.*.id' => 'required|exists:quiz_answers,id',
            'answers.*.score' => 'required|numeric|min:0',
            'answers.*.feedback' => 'nullable|string|max:1000',
        ]);

        $totalScore = 0;
        $maxPoints = 0;

        foreach ($validated['answers'] as $data) {
            $answer = QuizAnswer::findOrFail($data['id']);
            $answer->update([
                'score' => $data['score'],
                'feedback' => $data['feedback'] ?? null,
                'is_correct' => $data['score'] > 0,
            ]);
            $totalScore += $data['score'];
            $maxPoints += $answer->quizQuestion->points;
        }

        $finalScore = $maxPoints > 0 ? ($totalScore / $maxPoints) * 100 : 0;
        $attempt->update([
            'total_score' => $finalScore,
            'status' => 'graded',
        ]);

        $assessment = Assessment::firstOrCreate(
            [
                'teaching_assignment_id' => $quiz->teaching_assignment_id,
                'title' => $quiz->title,
                'component' => 'quiz',
            ],
            [
                'assessment_date' => now(),
                'max_score' => 100,
                'published_at' => now(),
            ]
        );

        AssessmentScore::updateOrCreate(
            [
                'assessment_id' => $assessment->id,
                'student_id' => $attempt->student_id,
            ],
            [
                'score' => $finalScore,
                'graded_at' => now(),
            ]
        );

        AuditService::log('quiz.grade_essay', 'QuizAttempt', $attempt->id, null);

        return back()->with('success', 'Nilai essay berhasil disimpan.');
    }

    public function nilaiEssayData(Request $request, QuizAttempt $attempt)
    {
        $user = $request->user();
        $quiz = $attempt->quiz;

        if (!$user->teachingAssignments()->where('id', $quiz->teaching_assignment_id)->exists()) {
            abort(403);
        }

        $attempt->load(['answers.quizQuestion.questionBank', 'student']);

        $answers = $attempt->answers->where('quizQuestion.questionBank.question_type', 'essay')->values()->map(fn($a) => [
            'id' => $a->id,
            'question_text' => $a->quizQuestion->questionBank->question_text,
            'answer_text' => $a->answer_text,
            'max_points' => (float) $a->quizQuestion->points,
            'score' => $a->score,
            'feedback' => $a->feedback,
        ]);

        return response()->json([
            'student_name' => $attempt->student->full_name,
            'answers' => $answers,
        ]);
    }
}
