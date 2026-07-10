<?php

namespace Database\Seeders;

use App\Models\AcademicPeriod;
use App\Models\Student;
use App\Models\Subject;
use App\Models\TeachingAssignment;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $parent = User::factory()->create([
            'name' => 'Bapak/Ibu Pratama',
            'full_name' => 'Bapak/Ibu Pratama',
            'email' => 'orangtua@demo.sch.id',
            'password' => bcrypt('Demo123!'),
            'role' => 'parent',
        ]);

        $walasRpl = User::factory()->create([
            'name' => 'Rina Kusumawati',
            'full_name' => 'Ibu Rina Kusumawati, S.Kom.',
            'email' => 'rina@cakrawala.sch.id',
            'password' => bcrypt('Demo123!'),
            'role' => 'homeroom',
        ]);

        $walasSains = User::factory()->create([
            'name' => 'Dimas Arya',
            'full_name' => 'Bapak Dimas Arya, S.Pd.',
            'email' => 'dimas@cakrawala.sch.id',
            'password' => bcrypt('Demo123!'),
            'role' => 'homeroom',
        ]);

        $guru = User::factory()->create([
            'name' => 'Budi Santoso',
            'full_name' => 'Bapak Budi Santoso, S.Pd.',
            'email' => 'guru@demo.sch.id',
            'password' => bcrypt('Demo123!'),
            'role' => 'teacher',
        ]);

        $student1 = Student::create([
            'nisn' => '0098765432',
            'full_name' => 'Alif Pratama',
            'birth_date' => '2009-05-15',
            'class_name' => 'XI RPL 1',
            'program_name' => 'Rekayasa Perangkat Lunak',
            'homeroom_teacher_id' => $walasRpl->id,
            'status' => 'active',
        ]);

        $student2 = Student::create([
            'nisn' => '0101234567',
            'full_name' => 'Alya Pratama',
            'birth_date' => '2009-08-20',
            'class_name' => 'X SMA 2',
            'program_name' => 'Sains & Teknologi',
            'homeroom_teacher_id' => $walasSains->id,
            'status' => 'active',
        ]);

        $parent->students()->attach([
            $student1->id => ['relationship' => 'Ayah', 'is_primary' => true],
            $student2->id => ['relationship' => 'Ayah', 'is_primary' => true],
        ]);

        $period = AcademicPeriod::create([
            'academic_year' => '2026/2027',
            'semester' => 'ganjil',
            'start_date' => '2026-07-13',
            'end_date' => '2026-12-19',
            'is_active' => true,
        ]);

        $subjects = [
            ['code' => 'RPL001', 'name' => 'Pemrograman', 'kkm' => 75],
            ['code' => 'BINDO', 'name' => 'Bahasa Indonesia', 'kkm' => 75],
            ['code' => 'MTK', 'name' => 'Matematika', 'kkm' => 75],
            ['code' => 'BING', 'name' => 'Bahasa Inggris', 'kkm' => 75],
        ];

        $createdSubjects = [];
        foreach ($subjects as $s) {
            $createdSubjects[] = Subject::create($s);
        }

        TeachingAssignment::create([
            'period_id' => $period->id,
            'subject_id' => $createdSubjects[0]->id,
            'teacher_id' => $guru->id,
            'class_name' => 'XI RPL 1',
        ]);

        TeachingAssignment::create([
            'period_id' => $period->id,
            'subject_id' => $createdSubjects[1]->id,
            'teacher_id' => $guru->id,
            'class_name' => 'X SMA 2',
        ]);

        TeachingAssignment::create([
            'period_id' => $period->id,
            'subject_id' => $createdSubjects[3]->id,
            'teacher_id' => $walasRpl->id,
            'class_name' => 'XI RPL 1',
        ]);
    }
}
