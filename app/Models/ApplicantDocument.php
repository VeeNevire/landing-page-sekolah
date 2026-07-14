<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ApplicantDocument extends Model
{
    protected $fillable = [
        'applicant_id',
        'document_type',
        'file_path',
        'file_name',
        'file_size',
        'uploaded_at',
    ];

    protected function casts(): array
    {
        return [
            'uploaded_at' => 'datetime',
        ];
    }

    public function applicant()
    {
        return $this->belongsTo(Applicant::class);
    }
}
