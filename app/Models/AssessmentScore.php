<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AssessmentScore extends Model
{
    public $incrementing = false;
    protected $primaryKey = null;

    protected $fillable = [
        'assessment_id',
        'student_id',
        'score',
        'feedback',
        'submitted_at',
        'graded_at',
    ];

    protected function casts(): array
    {
        return [
            'score' => 'decimal:2',
            'submitted_at' => 'datetime',
            'graded_at' => 'datetime',
        ];
    }

    public function assessment()
    {
        return $this->belongsTo(Assessment::class);
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}
