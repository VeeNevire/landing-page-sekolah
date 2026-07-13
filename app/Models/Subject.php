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
}
