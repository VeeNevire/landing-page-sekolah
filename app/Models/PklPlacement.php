<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PklPlacement extends Model
{
    protected $fillable = [
        'student_id',
        'company_id',
        'guru_pembimbing_id',
        'start_date',
        'end_date',
        'status',
        'catatan',
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
        ];
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function company()
    {
        return $this->belongsTo(PklCompany::class, 'company_id');
    }

    public function guruPembimbing()
    {
        return $this->belongsTo(User::class, 'guru_pembimbing_id');
    }

    public function activities()
    {
        return $this->hasMany(PklActivity::class, 'placement_id');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'selesai' => 'Selesai',
            'batal' => 'Batal',
            default => 'Aktif',
        };
    }
}
