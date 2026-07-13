<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Jadwal extends Model
{
    protected $table = 'jadwal';
    protected $fillable = ['guru_mapel_id', 'day', 'time_slot'];

    public function guruMapel()
    {
        return $this->belongsTo(GuruMapel::class);
    }
}
