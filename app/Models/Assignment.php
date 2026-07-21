<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Assignment extends Model
{
    protected $fillable = [
        'teaching_assignment_id',
        'module_id',
        'title',
        'instructions',
        'attachment_path',
        'attachment_name',
        'due_date',
        'max_score',
        'allow_late_submission',
        'published_at',
    ];

    protected function casts(): array
    {
        return [
            'due_date' => 'datetime',
            'max_score' => 'decimal:2',
            'allow_late_submission' => 'boolean',
            'published_at' => 'datetime',
        ];
    }

    public function teachingAssignment()
    {
        return $this->belongsTo(TeachingAssignment::class);
    }

    public function module()
    {
        return $this->belongsTo(CourseModule::class);
    }

    public function submissions()
    {
        return $this->hasMany(Submission::class);
    }

    public function scopePublished($query)
    {
        return $query->whereNotNull('published_at');
    }

    public function scopeDraft($query)
    {
        return $query->whereNull('published_at');
    }
}
