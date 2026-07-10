<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TeachingAssignment extends Model
{
    protected $fillable = [
        'period_id',
        'subject_id',
        'teacher_id',
        'class_name',
    ];

    public function period()
    {
        return $this->belongsTo(AcademicPeriod::class);
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function teacher()
    {
        return $this->belongsTo(User::class);
    }

    public function assessments()
    {
        return $this->hasMany(Assessment::class);
    }
}
