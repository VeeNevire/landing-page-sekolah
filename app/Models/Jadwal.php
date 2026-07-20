<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Jadwal extends Model
{
    protected $table = 'jadwal';
    protected $fillable = ['teaching_assignment_id', 'day', 'time_slot'];

    public function teachingAssignment()
    {
        return $this->belongsTo(TeachingAssignment::class);
    }
}
