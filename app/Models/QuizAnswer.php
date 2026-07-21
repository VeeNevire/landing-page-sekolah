<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuizAnswer extends Model
{
    protected $fillable = [
        'quiz_attempt_id',
        'quiz_question_id',
        'answer_text',
        'selected_option',
        'is_correct',
        'score',
        'feedback',
    ];

    protected function casts(): array
    {
        return [
            'is_correct' => 'boolean',
            'score' => 'decimal:2',
        ];
    }

    public function quizAttempt()
    {
        return $this->belongsTo(QuizAttempt::class);
    }

    public function quizQuestion()
    {
        return $this->belongsTo(QuizQuestion::class);
    }
}
