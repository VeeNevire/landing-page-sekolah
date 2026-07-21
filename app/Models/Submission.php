<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Submission extends Model
{
    protected $fillable = [
        'assignment_id',
        'student_id',
        'file_path',
        'file_name',
        'file_size',
        'notes',
        'submitted_at',
        'is_late',
    ];

    protected function casts(): array
    {
        return [
            'file_size' => 'integer',
            'submitted_at' => 'datetime',
            'is_late' => 'boolean',
        ];
    }

    public function assignment()
    {
        return $this->belongsTo(Assignment::class);
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function grade()
    {
        return $this->hasOne(SubmissionGrade::class);
    }

    public function isGraded(): bool
    {
        return $this->grade()->exists();
    }
}
