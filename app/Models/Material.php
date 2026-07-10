<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Material extends Model
{
    protected $fillable = [
        'teaching_assignment_id',
        'title',
        'description',
        'url',
    ];

    public function teachingAssignment()
    {
        return $this->belongsTo(TeachingAssignment::class);
    }
}
