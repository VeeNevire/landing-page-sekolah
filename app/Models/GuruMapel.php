<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GuruMapel extends Model
{
    protected $table = 'guru_mapel';
    protected $fillable = ['semester_id', 'mapel_id', 'guru_id'];

    public function semester()
    {
        return $this->belongsTo(AcademicPeriod::class, 'semester_id');
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class, 'mapel_id');
    }

    public function teacher()
    {
        return $this->belongsTo(User::class, 'guru_id');
    }

    public function jadwals()
    {
        return $this->hasMany(Jadwal::class);
    }
}
