<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Models\AcademicPeriod;
use App\Models\Assessment;
use App\Models\AssessmentScore;
use App\Models\Quiz;
use App\Models\QuizAnswer;
use App\Models\QuizAttempt;
use App\Models\Student;
use App\Models\TeachingAssignment;
use Illuminate\Http\Request;

class SiswaKuisController extends Controller
{
    private function resolve(Request $request): ?array
    {
        $user = $request->user();
        $student = Student::with('kelas', 'jurusan')
            ->where('user_id', $user->id)
            ->first();

        if (!$student) return null;

        $period = AcademicPeriod::where('is_active', true)->first();
        if (!$period) return null;

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

        $quizzes = Quiz::with('teachingAssignment.subject', 'teachingAssignment.customSubject', 'module')
            ->whereIn('teaching_assignment_id', $assignmentIds)
            ->published()
            ->latest()
            ->get();

        $attemptCounts = QuizAttempt::where('student_id', $student->id)
            ->whereIn('quiz_id', $quizzes->pluck('id'))
            ->selectRaw('quiz_id, COUNT(*) as count, MAX(total_score) as best_score')
            ->groupBy('quiz_id')
            ->get()
            ->keyBy('quiz_id');

        return view('siswa.kuis.index', [
            'student' => $student,
            'period' => $period,
            'initials' => $data['initials'],
            'quizzes' => $quizzes,
            'attemptCounts' => $attemptCounts,
        ]);
    }

    public function mulai(Request $request, Quiz $quiz)
    {
        $data = $this->resolve($request);
        if (!$data) return redirect()->route('login');

        $student = $data['student'];
        $period = $data['period'];

        $ta = $quiz->teachingAssignment;
        if ($ta->class_name !== $student->class_name || $ta->period_id !== $period->id || !$quiz->published_at) {
            abort(403);
        }

        if ($quiz->start_date && now()->lt($quiz->start_date)) {
            return back()->with('error', 'Kuis belum dimulai.');
        }
        if ($quiz->end_date && now()->gt($quiz->end_date)) {
            return back()->with('error', 'Waktu pengerjaan kuis sudah berakhir.');
        }

        $attemptCount = QuizAttempt::where('quiz_id', $quiz->id)
            ->where('student_id', $student->id)
            ->count();

        if ($attemptCount >= $quiz->max_attempts) {
            return back()->with('error', 'Batas percobaan sudah habis (' . $quiz->max_attempts . 'x).');
        }

        $inProgress = QuizAttempt::where('quiz_id', $quiz->id)
            ->where('student_id', $student->id)
            ->where('status', 'in_progress')
            ->first();

        if ($inProgress) {
            return redirect()->route('siswa.kuis.kerjakan', $inProgress->id);
        }

        $attempt = QuizAttempt::create([
            'quiz_id' => $quiz->id,
            'student_id' => $student->id,
            'attempt_number' => $attemptCount + 1,
            'started_at' => now(),
            'status' => 'in_progress',
        ]);

        return redirect()->route('siswa.kuis.kerjakan', $attempt->id);
    }

    public function kerjakan(Request $request, QuizAttempt $attempt)
    {
        $data = $this->resolve($request);
        if (!$data) return redirect()->route('login');

        $student = $data['student'];

        if ($attempt->student_id !== $student->id || $attempt->status !== 'in_progress') {
            abort(403);
        }

        $quiz = $attempt->quiz()->with(['questions' => fn($q) => $q->with('questionBank')])->first();

        $questions = collect($quiz->questions);
        if ($quiz->shuffle_questions) {
            $questions = $questions->shuffle();
        }

        $existingAnswers = QuizAnswer::where('quiz_attempt_id', $attempt->id)->get()->keyBy('quiz_question_id');

        $endTime = $quiz->time_limit ? $attempt->started_at->copy()->addMinutes($quiz->time_limit) : null;

        if ($endTime && now()->gt($endTime)) {
            $this->autoSubmit($attempt);
            return redirect()->route('siswa.kuis.hasil', $attempt->id);
        }

        $timeLimit = $quiz->time_limit;
        $startedAt = $attempt->started_at;

        return view('siswa.kuis.kerjakan', [
            'student' => $student,
            'initials' => $data['initials'],
            'attempt' => $attempt,
            'quiz' => $quiz,
            'questions' => $questions,
            'existingAnswers' => $existingAnswers,
            'endTime' => $endTime,
            'timeLimit' => $timeLimit,
            'startedAt' => $startedAt,
        ]);
    }

    public function submit(Request $request, QuizAttempt $attempt)
    {
        $data = $this->resolve($request);
        if (!$data) return redirect()->route('login');

        $student = $data['student'];

        if ($attempt->student_id !== $student->id || $attempt->status !== 'in_progress') {
            abort(403);
        }

        $quiz = $attempt->quiz()->with(['questions' => fn($q) => $q->with('questionBank')])->first();

        $totalPoints = 0;
        $earnedPoints = 0;
        $hasEssay = false;

        foreach ($quiz->questions as $question) {
            $bank = $question->questionBank;
            $answerKey = 'answers.' . $question->id;
            $totalPoints += $question->points;

            if ($bank->question_type === 'essay') {
                $hasEssay = true;
                $answerText = $request->input($answerKey . '.text', '');
                QuizAnswer::updateOrCreate(
                    ['quiz_attempt_id' => $attempt->id, 'quiz_question_id' => $question->id],
                    ['answer_text' => $answerText]
                );
            } else {
                $selected = $request->input($answerKey . '.option', '');
                $isCorrect = $selected === $bank->correct_answer;
                $score = $isCorrect ? $question->points : 0;
                $earnedPoints += $isCorrect ? $question->points : 0;

                QuizAnswer::updateOrCreate(
                    ['quiz_attempt_id' => $attempt->id, 'quiz_question_id' => $question->id],
                    [
                        'selected_option' => $selected,
                        'is_correct' => $isCorrect,
                        'score' => $score,
                    ]
                );
            }
        }

        $finalScore = $totalPoints > 0 ? round(($earnedPoints / $totalPoints) * 100, 1) : 0;

        $attempt->update([
            'total_score' => $finalScore,
            'submitted_at' => now(),
            'status' => $hasEssay ? 'submitted' : 'graded',
        ]);

        if (!$hasEssay && $quiz->show_result_immediately) {
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
                ['assessment_id' => $assessment->id, 'student_id' => $student->id],
                ['score' => $finalScore, 'graded_at' => now()]
            );
        }

        return redirect()->route('siswa.kuis.hasil', $attempt->id);
    }

    private function autoSubmit(QuizAttempt $attempt)
    {
        $quiz = $attempt->quiz()->with(['questions' => fn($q) => $q->with('questionBank')])->first();

        $totalPoints = 0;
        $earnedPoints = 0;
        $hasEssay = false;

        foreach ($quiz->questions as $question) {
            $bank = $question->questionBank;
            $totalPoints += $question->points;

            $answer = QuizAnswer::where('quiz_attempt_id', $attempt->id)
                ->where('quiz_question_id', $question->id)
                ->first();

            if ($answer) {
                if ($bank->question_type !== 'essay') {
                    $isCorrect = $answer->selected_option === $bank->correct_answer;
                    $score = $isCorrect ? $question->points : 0;
                    $earnedPoints += $score;
                    $answer->update(['is_correct' => $isCorrect, 'score' => $score]);
                } else {
                    $hasEssay = true;
                }
            } elseif ($bank->question_type === 'essay') {
                $hasEssay = true;
                QuizAnswer::create([
                    'quiz_attempt_id' => $attempt->id,
                    'quiz_question_id' => $question->id,
                    'answer_text' => '',
                ]);
            }
        }

        $finalScore = $totalPoints > 0 ? round(($earnedPoints / $totalPoints) * 100, 1) : 0;

        $attempt->update([
            'total_score' => $finalScore,
            'submitted_at' => now(),
            'status' => $hasEssay ? 'submitted' : 'graded',
        ]);
    }

    public function hasil(Request $request, QuizAttempt $attempt)
    {
        $data = $this->resolve($request);
        if (!$data) return redirect()->route('login');

        $student = $data['student'];

        if ($attempt->student_id !== $student->id) {
            abort(403);
        }

        $attempt->load(['quiz.questions.questionBank', 'answers.quizQuestion.questionBank']);

        $showResult = $attempt->quiz->show_result_immediately || $attempt->status === 'graded';

        return view('siswa.kuis.hasil', [
            'student' => $student,
            'initials' => $data['initials'],
            'attempt' => $attempt,
            'showResult' => $showResult,
        ]);
    }
}
