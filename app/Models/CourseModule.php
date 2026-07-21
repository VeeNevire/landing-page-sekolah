<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CourseModule extends Model
{
    protected $fillable = [
        'teaching_assignment_id',
        'title',
        'description',
        'order',
    ];

    public function teachingAssignment()
    {
        return $this->belongsTo(TeachingAssignment::class);
    }

    public function materials()
    {
        return $this->hasMany(Material::class, 'module_id')->orderBy('order');
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('order');
    }
}
