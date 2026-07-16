<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
    protected $fillable = [
        'code',
        'name',
        'kkm',
    ];

    protected function casts(): array
    {
        return [
            'kkm' => 'decimal:2',
        ];
    }

    public function teachingAssignments()
    {
        return $this->hasMany(TeachingAssignment::class);
    }

    public function guruMapels()
    {
        return $this->hasMany(GuruMapel::class, 'mapel_id');
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
