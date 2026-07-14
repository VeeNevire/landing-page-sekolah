<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Applicant extends Model
{
    protected $fillable = [
        'user_id',
        'full_name', 'nickname', 'birth_place', 'birth_date',
        'gender', 'religion', 'address', 'phone',
        'asal_sekolah', 'nisn',
        'jenjang', 'program_diminati',
        'ayah_name', 'ayah_occupation', 'ayah_phone', 'ayah_email',
        'ibu_name', 'ibu_occupation', 'ibu_phone', 'ibu_email',
        'wali_name', 'wali_occupation', 'wali_phone', 'wali_email',
        'status', 'submitted_at', 'paid_at', 'admin_note', 'completion_step',
    ];

    protected function casts(): array
    {
        return [
            'birth_date' => 'date',
            'submitted_at' => 'datetime',
            'paid_at' => 'datetime',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function documents()
    {
        return $this->hasMany(ApplicantDocument::class);
    }
}
