<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JurusanCustomSubject extends Model
{
    protected $fillable = [
        'jurusan_id',
        'kode',
        'nama',
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

    public function jurusan()
    {
        return $this->belongsTo(Jurusan::class);
    }

    public function kelas()
    {
        return $this->belongsToMany(Kelas::class, 'kelas_custom_subject');
    }
}
