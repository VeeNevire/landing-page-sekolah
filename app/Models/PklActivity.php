<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PklActivity extends Model
{
    protected $fillable = [
        'placement_id',
        'tanggal',
        'aktivitas',
        'keterangan',
        'foto_path',
        'foto_name',
        'status',
        'catatan_pembimbing',
        'approved_by',
        'responded_at',
    ];

    protected function casts(): array
    {
        return [
            'tanggal' => 'date',
            'responded_at' => 'datetime',
        ];
    }

    public function placement()
    {
        return $this->belongsTo(PklPlacement::class, 'placement_id');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'approved' => 'Disetujui',
            'rejected' => 'Ditolak',
            default => 'Menunggu',
        };
    }
}
