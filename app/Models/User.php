<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'full_name', 'email', 'password', 'role', 'is_active', 'last_login_at', 'google_id', 'avatar'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
            'last_login_at' => 'datetime',
        ];
    }

    public function students()
    {
        return $this->belongsToMany(Student::class, 'parent_student', 'parent_id', 'student_id')
            ->withPivot('relationship', 'is_primary');
    }

    public function teachingAssignments()
    {
        return $this->hasMany(TeachingAssignment::class, 'teacher_id');
    }

    public function homeroomStudents()
    {
        return $this->hasMany(Student::class, 'homeroom_teacher_id');
    }

    public function auditLogs()
    {
        return $this->hasMany(AuditLog::class);
    }

    public function mapels()
    {
        return $this->hasMany(GuruMapel::class, 'guru_id');
    }

    public function applicant()
    {
        return $this->hasOne(Applicant::class);
    }
}
