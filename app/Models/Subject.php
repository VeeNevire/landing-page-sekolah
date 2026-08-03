<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
    protected $fillable = [
        'code',
        'name',
        'kkm',
        'weight_quiz',
        'weight_homework',
        'weight_project',
        'weight_assignment',
        'weight_uts',
        'weight_uas',
    ];

    protected function casts(): array
    {
        return [
            'kkm' => 'decimal:2',
            'weight_quiz' => 'decimal:2',
            'weight_homework' => 'decimal:2',
            'weight_project' => 'decimal:2',
            'weight_assignment' => 'decimal:2',
            'weight_uts' => 'decimal:2',
            'weight_uas' => 'decimal:2',
        ];
    }

    public function hasWeights(): bool
    {
        return collect(['weight_quiz', 'weight_homework', 'weight_project', 'weight_assignment', 'weight_uts', 'weight_uas'])
            ->contains(fn($column) => $this->{$column} !== null);
    }

    public function teachingAssignments()
    {
        return $this->hasMany(TeachingAssignment::class);
    }

    public function kelas()
    {
        return $this->belongsToMany(Kelas::class, 'kelas_subject');
    }

    public function gurus()
    {
        $activePeriod = \App\Models\AcademicPeriod::where('is_active', true)->first();
        return $this->belongsToMany(User::class, 'guru_mapel', 'mapel_id', 'guru_id')
            ->withPivot('semester_id', 'class_name')
            ->wherePivot('semester_id', $activePeriod?->id);
    }

    public function allGurus()
    {
        return $this->belongsToMany(User::class, 'guru_mapel', 'mapel_id', 'guru_id')
            ->withPivot('semester_id', 'class_name');
    }
}
