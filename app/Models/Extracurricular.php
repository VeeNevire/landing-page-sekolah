<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Extracurricular extends Model
{
    protected $fillable = [
        'student_id',
        'name',
        'score',
        'note',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}
