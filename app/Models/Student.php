<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    protected $fillable = [
        'nisn',
        'full_name',
        'birth_date',
        'class_name',
        'program_name',
        'homeroom_teacher_id',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'birth_date' => 'date',
        ];
    }

    public function homeroomTeacher()
    {
        return $this->belongsTo(User::class, 'homeroom_teacher_id');
    }

    public function parents()
    {
        return $this->belongsToMany(User::class, 'parent_student', 'student_id', 'parent_id')
            ->withPivot('relationship', 'is_primary');
    }

    public function assessmentScores()
    {
        return $this->hasMany(AssessmentScore::class);
    }

    public function attendance()
    {
        return $this->hasMany(Attendance::class);
    }

    public function teacherNotes()
    {
        return $this->hasMany(TeacherNote::class);
    }

    public function behaviorScores()
    {
        return $this->hasMany(BehaviorScore::class);
    }
}
