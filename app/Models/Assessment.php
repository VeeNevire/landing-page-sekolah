<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Assessment extends Model
{
    protected $fillable = [
        'teaching_assignment_id',
        'title',
        'component',
        'assessment_date',
        'max_score',
        'weight_percent',
        'published_at',
    ];

    protected function casts(): array
    {
        return [
            'assessment_date' => 'date',
            'max_score' => 'decimal:2',
            'weight_percent' => 'decimal:2',
            'published_at' => 'datetime',
        ];
    }

    public function teachingAssignment()
    {
        return $this->belongsTo(TeachingAssignment::class);
    }

    public function scores()
    {
        return $this->hasMany(AssessmentScore::class);
    }
}
