<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TeacherNote extends Model
{
    protected $fillable = [
        'student_id',
        'period_id',
        'author_id',
        'category',
        'note',
        'follow_up',
        'visible_to_parent',
    ];

    protected function casts(): array
    {
        return [
            'visible_to_parent' => 'boolean',
        ];
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function period()
    {
        return $this->belongsTo(AcademicPeriod::class);
    }

    public function author()
    {
        return $this->belongsTo(User::class, 'author_id');
    }
}
