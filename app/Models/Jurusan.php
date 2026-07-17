<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Jurusan extends Model
{
    protected $fillable = [
        'kode',
        'nama',
        'deskripsi',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function kelas()
    {
        return $this->hasMany(Kelas::class);
    }

    public function subjects()
    {
        return $this->belongsToMany(Subject::class, 'jurusan_subject');
    }

    public function customSubjects()
    {
        return $this->hasMany(JurusanCustomSubject::class);
    }

    public function students()
    {
        return $this->hasMany(Student::class, 'jurusan_id');
    }
}
