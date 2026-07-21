<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuestionBank extends Model
{
    protected $fillable = [
        'teaching_assignment_id',
        'topic',
        'question_type',
        'question_text',
        'options',
        'correct_answer',
        'points',
        'explanation',
    ];

    protected function casts(): array
    {
        return [
            'options' => 'array',
            'points' => 'decimal:2',
        ];
    }

    public function teachingAssignment()
    {
        return $this->belongsTo(TeachingAssignment::class);
    }

    public function quizQuestions()
    {
        return $this->hasMany(QuizQuestion::class);
    }
}
