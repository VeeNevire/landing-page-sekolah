<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BehaviorScore extends Model
{
    protected $fillable = [
        'student_id',
        'period_id',
        'aspect',
        'grade',
        'note',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function period()
    {
        return $this->belongsTo(AcademicPeriod::class);
    }
}
