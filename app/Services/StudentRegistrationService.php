<?php

namespace App\Services;

use App\Mail\ParentAccountMail;
use App\Models\Applicant;
use App\Models\Student;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class StudentRegistrationService
{
    public function createFromApplicant(Applicant $applicant, ?string $classOverride = null): Student
    {
        $student = Student::create([
            'user_id' => $applicant->user_id,
            'nisn' => $applicant->nisn ?? ('PPDB-' . str_pad((string) $applicant->id, 5, '0', STR_PAD_LEFT)),
            'full_name' => $applicant->full_name,
            'birth_date' => $applicant->birth_date,
            'class_name' => $classOverride ?? ('X ' . ($applicant->program_diminati ?? 'Baru')),
            'program_name' => $applicant->program_diminati ?? ($applicant->jenjang ?? ''),
            'status' => 'active',
        ]);

        if ($applicant->user_id) {
            $applicant->user->update(['role' => 'student']);
        }

        $nis = $this->generateNis();
        $student->update(['nis' => $nis]);

        $this->createParentAccountsFromApplicant($applicant, $student);

        AuditService::log('student.create-from-applicant', 'Student', $student->id, $student->full_name);

        return $student;
    }

    public function createParentAccountsFromApplicant(Applicant $applicant, Student $student): void
    {
        foreach (['ayah', 'ibu'] as $parentType) {
            $email = $applicant->{$parentType . '_email'};
            $name = $applicant->{$parentType . '_name'};
            if ($email && $name) {
                $parent = $this->findOrCreateParent($name, $email, $applicant->full_name);
                $student->parents()->syncWithoutDetaching([$parent->id => [
                    'relationship' => $parentType === 'ayah' ? 'Ayah' : 'Ibu',
                    'is_primary' => $parentType === 'ayah',
                ]]);
            }
        }
    }

    public function findOrCreateParent(string $name, string $email, string $studentName): User
    {
        $parent = User::where('email', $email)->first();

        if (!$parent) {
            $password = (string) random_int(10000000, 99999999);
            $parent = User::create([
                'name' => $name,
                'full_name' => $name,
                'email' => $email,
                'password' => Hash::make($password),
                'role' => 'parent',
            ]);

            try {
                Mail::to($email)->send(new ParentAccountMail(
                    parentName: $name,
                    parentEmail: $email,
                    password: $password,
                    studentName: $studentName,
                ));
            } catch (\Exception $e) {
                Log::error('Gagal kirim email parent: ' . $e->getMessage());
            }
        }

        return $parent;
    }

    public function generateNis(): string
    {
        $year = now()->format('Y');
        $lastNis = Student::where('nis', 'like', $year . '%')->max('nis');
        $nextNumber = $lastNis ? intval(substr((string) $lastNis, -4)) + 1 : 1;
        return $year . str_pad((string) $nextNumber, 4, '0', STR_PAD_LEFT);
    }
}
