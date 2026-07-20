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
    ];

    protected function casts(): array
    {
        return [
            'kkm' => 'decimal:2',
        ];
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
