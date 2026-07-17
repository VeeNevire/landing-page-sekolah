<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kelas extends Model
{
    protected $table = 'kelas';

    protected $fillable = [
        'jurusan_id',
        'tingkat',
        'nama',
        'is_active',
        'homeroom_teacher_id',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'tingkat' => 'integer',
        ];
    }

    public function jurusan()
    {
        return $this->belongsTo(Jurusan::class);
    }

    public function homeroomTeacher()
    {
        return $this->belongsTo(User::class, 'homeroom_teacher_id');
    }

    public function subjects()
    {
        return $this->belongsToMany(Subject::class, 'kelas_subject');
    }

    public function customSubjects()
    {
        return $this->belongsToMany(JurusanCustomSubject::class, 'kelas_custom_subject');
    }

    public function students()
    {
        return $this->hasMany(Student::class, 'kelas_id');
    }

    public function getNamaLengkapAttribute()
    {
        $romawi = match ($this->tingkat) {
            10 => 'X',
            11 => 'XI',
            12 => 'XII',
            default => (string) $this->tingkat,
        };

        return $romawi . ' ' . $this->nama;
    }
}
