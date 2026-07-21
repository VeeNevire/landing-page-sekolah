<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubmissionGrade extends Model
{
    protected $fillable = [
        'submission_id',
        'score',
        'feedback',
        'graded_by',
        'graded_at',
    ];

    protected function casts(): array
    {
        return [
            'score' => 'decimal:2',
            'graded_at' => 'datetime',
        ];
    }

    public function submission()
    {
        return $this->belongsTo(Submission::class);
    }

    public function grader()
    {
        return $this->belongsTo(User::class, 'graded_by');
    }
}
