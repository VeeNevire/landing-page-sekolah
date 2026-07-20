<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AcademicPeriod extends Model
{
    protected $fillable = [
        'academic_year',
        'semester',
        'start_date',
        'end_date',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
            'is_active' => 'boolean',
        ];
    }

    public function teachingAssignments()
    {
        return $this->hasMany(TeachingAssignment::class, 'period_id');
    }

    public function teacherNotes()
    {
        return $this->hasMany(TeacherNote::class, 'period_id');
    }

    public function behaviorScores()
    {
        return $this->hasMany(BehaviorScore::class, 'period_id');
    }

}
