<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LeaveRequest extends Model
{
    protected $fillable = [
        'student_id',
        'requested_by',
        'type',
        'start_date',
        'end_date',
        'reason',
        'status',
        'response_note',
        'approved_by',
        'responded_at',
        'attachment_path',
        'attachment_name',
        'attachment_size',
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
            'responded_at' => 'datetime',
        ];
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function requester()
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeForClass($query, string $className)
    {
        return $query->whereHas('student', fn($q) => $q->where('class_name', $className));
    }

    public function scopePendingForHomeroom($query, int $userId)
    {
        $kelas = Kelas::where('homeroom_teacher_id', $userId)->first();

        if (!$kelas) {
            return $query->whereRaw('1 = 0');
        }

        return $query->pending()->forClass($kelas->nama_lengkap);
    }

    public function getTypeLabelAttribute(): string
    {
        return $this->type === 'sick' ? 'Sakit' : 'Izin';
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
