<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PklCompany extends Model
{
    protected $fillable = [
        'nama',
        'bidang',
        'alamat',
        'kota',
        'kontak_person',
        'kontak_telepon',
        'catatan',
    ];

    public function placements()
    {
        return $this->hasMany(PklPlacement::class, 'company_id');
    }
}
