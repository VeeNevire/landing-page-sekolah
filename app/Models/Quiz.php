<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Quiz extends Model
{
    protected $fillable = [
        'teaching_assignment_id',
        'module_id',
        'title',
        'instructions',
        'time_limit',
        'max_attempts',
        'shuffle_questions',
        'shuffle_options',
        'show_result_immediately',
        'published_at',
        'start_date',
        'end_date',
    ];

    protected function casts(): array
    {
        return [
            'time_limit' => 'integer',
            'max_attempts' => 'integer',
            'shuffle_questions' => 'boolean',
            'shuffle_options' => 'boolean',
            'show_result_immediately' => 'boolean',
            'published_at' => 'datetime',
            'start_date' => 'datetime',
            'end_date' => 'datetime',
        ];
    }

    public function teachingAssignment()
    {
        return $this->belongsTo(TeachingAssignment::class);
    }

    public function module()
    {
        return $this->belongsTo(CourseModule::class);
    }

    public function questions()
    {
        return $this->hasMany(QuizQuestion::class)->orderBy('order');
    }

    public function attempts()
    {
        return $this->hasMany(QuizAttempt::class);
    }

    public function scopePublished($query)
    {
        return $query->whereNotNull('published_at');
    }
}
