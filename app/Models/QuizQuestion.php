<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuizQuestion extends Model
{
    protected $fillable = [
        'quiz_id',
        'question_bank_id',
        'order',
        'points',
    ];

    protected function casts(): array
    {
        return [
            'points' => 'decimal:2',
        ];
    }

    public function quiz()
    {
        return $this->belongsTo(Quiz::class);
    }

    public function questionBank()
    {
        return $this->belongsTo(QuestionBank::class);
    }

    public function answers()
    {
        return $this->hasMany(QuizAnswer::class);
    }
}
