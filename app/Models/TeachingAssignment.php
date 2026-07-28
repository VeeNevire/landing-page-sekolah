<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TeachingAssignment extends Model
{
    protected $fillable = [
        'period_id',
        'subject_id',
        'custom_subject_id',
        'teacher_id',
        'class_name',
    ];

    public function subject()
    {
        return $this->belongsTo(Subject::class, 'subject_id');
    }

    public function customSubject()
    {
        return $this->belongsTo(JurusanCustomSubject::class, 'custom_subject_id');
    }

    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    public function period()
    {
        return $this->belongsTo(AcademicPeriod::class, 'period_id');
    }

    public function assessments()
    {
        return $this->hasMany(Assessment::class, 'teaching_assignment_id');
    }

    public function jadwals()
    {
        return $this->hasMany(Jadwal::class, 'teaching_assignment_id');
    }

    public function modules()
    {
        return $this->hasMany(CourseModule::class)->ordered();
    }

    public function materials()
    {
        return $this->hasMany(Material::class)->ordered();
    }
}
