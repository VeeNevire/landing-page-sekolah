<?php

namespace Database\Seeders;

use App\Models\AcademicPeriod;
use App\Models\Assessment;
use App\Models\AssessmentScore;
use App\Models\Attendance;
use App\Models\BehaviorScore;
use App\Models\Billing;
use App\Models\Extracurricular;
use App\Models\Notification;
use App\Models\Student;
use App\Models\Subject;
use App\Models\TeachingAssignment;
use App\Models\TeacherNote;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            AdminUserSeeder::class,
        ]);

        // ── Users ──────────────────────────────────────────────
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

        // ── Students ───────────────────────────────────────────
        $alif = Student::create([
            'nisn' => '0098765432',
            'full_name' => 'Alif Pratama',
            'birth_date' => '2009-05-15',
            'class_name' => 'XI RPL 1',
            'program_name' => 'Rekayasa Perangkat Lunak',
            'homeroom_teacher_id' => $walasRpl->id,
            'status' => 'active',
        ]);

        $alya = Student::create([
            'nisn' => '0101234567',
            'full_name' => 'Alya Pratama',
            'birth_date' => '2009-08-20',
            'class_name' => 'X SMA 2',
            'program_name' => 'Sains & Teknologi',
            'homeroom_teacher_id' => $walasSains->id,
            'status' => 'active',
        ]);

        $parent->students()->attach([
            $alif->id => ['relationship' => 'Ayah', 'is_primary' => true],
            $alya->id => ['relationship' => 'Ayah', 'is_primary' => true],
        ]);

        // ── Academic Period ────────────────────────────────────
        $period = AcademicPeriod::create([
            'academic_year' => '2026/2027',
            'semester' => 'ganjil',
            'start_date' => '2026-07-13',
            'end_date' => '2026-12-19',
            'is_active' => true,
        ]);

        // ── Subjects ───────────────────────────────────────────
        $subjectsData = [
            ['code' => 'RPL001', 'name' => 'Pemrograman', 'kkm' => 75],
            ['code' => 'BING', 'name' => 'Bahasa Inggris', 'kkm' => 75],
            ['code' => 'BD', 'name' => 'Basis Data', 'kkm' => 75],
            ['code' => 'MTK', 'name' => 'Matematika', 'kkm' => 75],
            ['code' => 'BINDO', 'name' => 'Bahasa Indonesia', 'kkm' => 75],
            ['code' => 'BIO', 'name' => 'Biologi', 'kkm' => 75],
        ];

        $createdSubjects = [];
        foreach ($subjectsData as $s) {
            $createdSubjects[$s['code']] = Subject::create($s);
        }

        // ── Teaching Assignments ───────────────────────────────
        // Alif (XI RPL 1): Pemrograman, Bahasa Inggris, Basis Data, Matematika
        $taRplProg = TeachingAssignment::create([
            'period_id' => $period->id,
            'subject_id' => $createdSubjects['RPL001']->id,
            'teacher_id' => $guru->id,
            'class_name' => 'XI RPL 1',
        ]);

        $taRplBing = TeachingAssignment::create([
            'period_id' => $period->id,
            'subject_id' => $createdSubjects['BING']->id,
            'teacher_id' => $walasRpl->id,
            'class_name' => 'XI RPL 1',
        ]);

        $taRplBd = TeachingAssignment::create([
            'period_id' => $period->id,
            'subject_id' => $createdSubjects['BD']->id,
            'teacher_id' => $walasRpl->id,
            'class_name' => 'XI RPL 1',
        ]);

        $taRplMtk = TeachingAssignment::create([
            'period_id' => $period->id,
            'subject_id' => $createdSubjects['MTK']->id,
            'teacher_id' => $walasSains->id,
            'class_name' => 'XI RPL 1',
        ]);

        // Alya (X SMA 2): Bahasa Indonesia, Matematika, Bahasa Inggris, Biologi
        $taSmaBindo = TeachingAssignment::create([
            'period_id' => $period->id,
            'subject_id' => $createdSubjects['BINDO']->id,
            'teacher_id' => $guru->id,
            'class_name' => 'X SMA 2',
        ]);

        $taSmaMtk = TeachingAssignment::create([
            'period_id' => $period->id,
            'subject_id' => $createdSubjects['MTK']->id,
            'teacher_id' => $walasSains->id,
            'class_name' => 'X SMA 2',
        ]);

        $taSmaBing = TeachingAssignment::create([
            'period_id' => $period->id,
            'subject_id' => $createdSubjects['BING']->id,
            'teacher_id' => $guru->id,
            'class_name' => 'X SMA 2',
        ]);

        $taSmaBio = TeachingAssignment::create([
            'period_id' => $period->id,
            'subject_id' => $createdSubjects['BIO']->id,
            'teacher_id' => $walasSains->id,
            'class_name' => 'X SMA 2',
        ]);

        // ── Assessments & Scores ───────────────────────────────
        $this->seedAssessments($taRplProg, $alif, [
            'quiz' => [
                ['title' => 'Kuis OOP Dasar', 'date' => '2026-07-25', 'scores' => [88]],
                ['title' => 'Kuis Inheritance & Polimorfisme', 'date' => '2026-08-15', 'scores' => [92]],
            ],
            'homework' => [
                ['title' => 'Tugas Polimorfisme', 'date' => '2026-08-01', 'scores' => [90]],
                ['title' => 'Tugas Debugging', 'date' => '2026-08-20', 'scores' => [85]],
            ],
            'project' => [
                ['title' => 'Proyek CRUD App', 'date' => '2026-09-10', 'scores' => [93]],
            ],
            'uts' => [
                ['title' => 'UTS Pemrograman', 'date' => '2026-09-20', 'scores' => [87]],
            ],
            'uas' => [
                ['title' => 'UAS Pemrograman', 'date' => '2026-12-10', 'scores' => [91]],
            ],
        ]);

        $this->seedAssessments($taRplBing, $alif, [
            'quiz' => [
                ['title' => 'Quiz Vocabulary', 'date' => '2026-07-28', 'scores' => [84]],
                ['title' => 'Quiz Grammar', 'date' => '2026-08-18', 'scores' => [88]],
            ],
            'homework' => [
                ['title' => 'Essay Reading Comprehension', 'date' => '2026-08-05', 'scores' => [86]],
            ],
            'project' => [
                ['title' => 'Oral Presentation', 'date' => '2026-09-15', 'scores' => [90]],
            ],
            'uts' => [
                ['title' => 'UTS Listening', 'date' => '2026-09-22', 'scores' => [82]],
            ],
            'uas' => [
                ['title' => 'UAS Bahasa Inggris', 'date' => '2026-12-12', 'scores' => [85]],
            ],
        ]);

        $this->seedAssessments($taRplBd, $alif, [
            'quiz' => [
                ['title' => 'Kuis ERD & Normalisasi', 'date' => '2026-08-01', 'scores' => [86]],
                ['title' => 'Kuis SQL Dasar', 'date' => '2026-08-22', 'scores' => [90]],
            ],
            'homework' => [
                ['title' => 'Tugas Query SQL', 'date' => '2026-09-01', 'scores' => [88]],
            ],
            'project' => [
                ['title' => 'Proyek Database Toko', 'date' => '2026-10-05', 'scores' => [91]],
            ],
            'uts' => [
                ['title' => 'UTS Basis Data', 'date' => '2026-09-23', 'scores' => [84]],
            ],
            'uas' => [
                ['title' => 'UAS Basis Data', 'date' => '2026-12-13', 'scores' => [88]],
            ],
        ]);

        $this->seedAssessments($taRplMtk, $alif, [
            'quiz' => [
                ['title' => 'Kuis Fungsi Komposisi', 'date' => '2026-07-30', 'scores' => [82]],
                ['title' => 'Kuis Statistika', 'date' => '2026-09-05', 'scores' => [80]],
            ],
            'homework' => [
                ['title' => 'Tugas Integral', 'date' => '2026-08-12', 'scores' => [78]],
                ['title' => 'Tugas Statistika', 'date' => '2026-09-12', 'scores' => [84]],
            ],
            'project' => [
                ['title' => 'Proyek Analisis Data', 'date' => '2026-10-10', 'scores' => [86]],
            ],
            'uts' => [
                ['title' => 'UTS Matematika', 'date' => '2026-09-24', 'scores' => [81]],
            ],
            'uas' => [
                ['title' => 'UAS Matematika', 'date' => '2026-12-14', 'scores' => [84]],
            ],
        ]);

        $this->seedAssessments($taSmaBindo, $alya, [
            'quiz' => [
                ['title' => 'Quiz Teks Eksposisi', 'date' => '2026-07-26', 'scores' => [90]],
                ['title' => 'Quiz Teks Argumentasi', 'date' => '2026-08-16', 'scores' => [88]],
            ],
            'homework' => [
                ['title' => 'Analisis Berita', 'date' => '2026-08-03', 'scores' => [92]],
            ],
            'project' => [
                ['title' => 'Pidato Persuasif', 'date' => '2026-09-12', 'scores' => [91]],
            ],
            'uts' => [
                ['title' => 'UTS Bahasa Indonesia', 'date' => '2026-09-21', 'scores' => [87]],
            ],
            'uas' => [
                ['title' => 'UAS Bahasa Indonesia', 'date' => '2026-12-11', 'scores' => [90]],
            ],
        ]);

        $this->seedAssessments($taSmaMtk, $alya, [
            'quiz' => [
                ['title' => 'Kuis Persamaan Linear', 'date' => '2026-07-29', 'scores' => [91]],
                ['title' => 'Kuis Barisan & Deret', 'date' => '2026-08-19', 'scores' => [88]],
            ],
            'homework' => [
                ['title' => 'Tugas Trigonometri', 'date' => '2026-08-08', 'scores' => [94]],
            ],
            'project' => [
                ['title' => 'Proyek Matematika Terapan', 'date' => '2026-10-01', 'scores' => [93]],
            ],
            'uts' => [
                ['title' => 'UTS Matematika', 'date' => '2026-09-25', 'scores' => [89]],
            ],
            'uas' => [
                ['title' => 'UAS Matematika', 'date' => '2026-12-15', 'scores' => [92]],
            ],
        ]);

        $this->seedAssessments($taSmaBing, $alya, [
            'quiz' => [
                ['title' => 'Quiz Reading', 'date' => '2026-07-27', 'scores' => [91]],
                ['title' => 'Quiz Writing', 'date' => '2026-08-17', 'scores' => [90]],
            ],
            'homework' => [
                ['title' => 'Essay Narrative', 'date' => '2026-08-06', 'scores' => [93]],
            ],
            'project' => [
                ['title' => 'Debat Bahasa Inggris', 'date' => '2026-09-14', 'scores' => [91]],
            ],
            'uts' => [
                ['title' => 'UTS Bahasa Inggris', 'date' => '2026-09-22', 'scores' => [90]],
            ],
            'uas' => [
                ['title' => 'UAS Bahasa Inggris', 'date' => '2026-12-12', 'scores' => [92]],
            ],
        ]);

        $this->seedAssessments($taSmaBio, $alya, [
            'quiz' => [
                ['title' => 'Quiz Sel & Jaringan', 'date' => '2026-07-31', 'scores' => [90]],
                ['title' => 'Quiz Ekosistem', 'date' => '2026-08-21', 'scores' => [92]],
            ],
            'homework' => [
                ['title' => 'Laporan Praktikum Mikroskop', 'date' => '2026-08-10', 'scores' => [94]],
            ],
            'project' => [
                ['title' => 'Proyek Eksperimen Lingkungan', 'date' => '2026-10-08', 'scores' => [95]],
            ],
            'uts' => [
                ['title' => 'UTS Biologi', 'date' => '2026-09-26', 'scores' => [90]],
            ],
            'uas' => [
                ['title' => 'UAS Biologi', 'date' => '2026-12-16', 'scores' => [93]],
            ],
        ]);

        // ── Attendance ─────────────────────────────────────────
        $this->seedAttendance($alif, $walasRpl, $period);
        $this->seedAttendance($alya, $walasSains, $period);

        // ── Teacher Notes ──────────────────────────────────────
        TeacherNote::create([
            'student_id' => $alif->id,
            'period_id' => $period->id,
            'author_id' => $walasRpl->id,
            'category' => 'academic',
            'note' => 'Perkembangan pemrograman sangat baik. Perlu meningkatkan konsistensi latihan Matematika, terutama fungsi dan statistika.',
            'follow_up' => 'Berikan latihan tambahan soal statistika mingguan.',
            'visible_to_parent' => true,
        ]);

        TeacherNote::create([
            'student_id' => $alif->id,
            'period_id' => $period->id,
            'author_id' => $walasRpl->id,
            'category' => 'behavior',
            'note' => 'Alif aktif di kelas dan membantu teman yang kesulitan. Sikap leadership terlihat saat kerja kelompok.',
            'visible_to_parent' => true,
        ]);

        TeacherNote::create([
            'student_id' => $alya->id,
            'period_id' => $period->id,
            'author_id' => $walasSains->id,
            'category' => 'academic',
            'note' => 'Alya memiliki rasa ingin tahu tinggi dan mampu bekerja sangat baik dalam kelompok. Perlu lebih percaya diri saat presentasi individual.',
            'follow_up' => 'Latihan presentasi di depan kelas secara rutin.',
            'visible_to_parent' => true,
        ]);

        TeacherNote::create([
            'student_id' => $alya->id,
            'period_id' => $period->id,
            'author_id' => $walasSains->id,
            'category' => 'career',
            'note' => 'Alya menunjukkan minat kuat di bidang sains dan riset. Disarankan mengikuti olimpiade sains tingkat kota.',
            'visible_to_parent' => true,
        ]);

        // ── Behavior Scores ────────────────────────────────────
        foreach (['discipline', 'responsibility', 'collaboration', 'independence'] as $aspect) {
            BehaviorScore::create([
                'student_id' => $alif->id,
                'period_id' => $period->id,
                'aspect' => $aspect,
                'grade' => match ($aspect) {
                    'discipline' => 'A',
                    'responsibility' => 'A-',
                    'collaboration' => 'B+',
                    'independence' => 'A',
                },
            ]);

            BehaviorScore::create([
                'student_id' => $alya->id,
                'period_id' => $period->id,
                'aspect' => $aspect,
                'grade' => match ($aspect) {
                    'discipline' => 'A',
                    'responsibility' => 'A',
                    'collaboration' => 'A',
                    'independence' => 'B+',
                },
            ]);
        }

        // ── Billings ───────────────────────────────────────────
        $billings = [
            ['student_id' => $alif->id, 'name' => 'SPP Juli 2026', 'amount' => 950000, 'due_date' => '2026-07-05', 'status' => 'lunas', 'paid_date' => '2026-07-05'],
            ['student_id' => $alif->id, 'name' => 'SPP Agustus 2026', 'amount' => 950000, 'due_date' => '2026-08-01', 'status' => 'belum'],
            ['student_id' => $alif->id, 'name' => 'Uang Kegiatan Semester', 'amount' => 250000, 'due_date' => '2026-07-15', 'status' => 'belum'],
            ['student_id' => $alif->id, 'name' => 'Uang Praktikum', 'amount' => 150000, 'due_date' => '2026-07-10', 'status' => 'lunas', 'paid_date' => '2026-07-10'],
            ['student_id' => $alya->id, 'name' => 'SPP Juli 2026', 'amount' => 1050000, 'due_date' => '2026-07-03', 'status' => 'lunas', 'paid_date' => '2026-07-03'],
            ['student_id' => $alya->id, 'name' => 'SPP Agustus 2026', 'amount' => 1050000, 'due_date' => '2026-08-01', 'status' => 'belum'],
            ['student_id' => $alya->id, 'name' => 'Uang Kegiatan Semester', 'amount' => 300000, 'due_date' => '2026-07-15', 'status' => 'belum'],
        ];

        foreach ($billings as $b) {
            Billing::create($b);
        }

        // ── Notifications ──────────────────────────────────────
        $notifications = [
            ['student_id' => $alif->id, 'type' => 'info', 'title' => 'Jadwal UTS Ganjil 2026/2027', 'body' => 'Ujian Tengah Semester akan dilaksanakan pada 15–26 September 2026. Persiapkan diri dengan belajar secara rutin.', 'created_at' => '2026-07-10'],
            ['student_id' => $alif->id, 'type' => 'warning', 'title' => 'Tagihan SPP Agustus', 'body' => 'Tagihan SPP bulan Agustus 2026 sudah terbit. Silakan lakukan pembayaran sebelum jatuh tempo.', 'created_at' => '2026-07-08'],
            ['student_id' => $alif->id, 'type' => 'info', 'title' => 'Libur Hari Raya', 'body' => 'Sekolah libur pada hari Senin 7 Juli 2026 memperingati Hari Raya. Kegiatan belajar mengajar normal pada hari berikutnya.', 'created_at' => '2026-07-05'],
            ['student_id' => $alya->id, 'type' => 'info', 'title' => 'Jadwal UTS Ganjil 2026/2027', 'body' => 'Ujian Tengah Semester akan dilaksanakan pada 15–26 September 2026. Persiapkan diri dengan belajar secara rutin.', 'created_at' => '2026-07-10'],
            ['student_id' => $alya->id, 'type' => 'success', 'title' => 'Laporan Praktikum Biologi', 'body' => 'Laporan praktikum Biologi telah dinilai dengan skor 92. Lihat detail di Laporan Nilai.', 'created_at' => '2026-07-09'],
            ['student_id' => $alya->id, 'type' => 'warning', 'title' => 'Tagihan SPP Agustus', 'body' => 'Tagihan SPP bulan Agustus 2026 sudah terbit. Silakan lakukan pembayaran sebelum jatuh tempo.', 'created_at' => '2026-07-08'],
            ['student_id' => $alya->id, 'type' => 'info', 'title' => 'Libur Hari Raya', 'body' => 'Sekolah libur pada hari Senin 7 Juli 2026 memperingati Hari Raya. Kegiatan belajar mengajar normal pada hari berikutnya.', 'created_at' => '2026-07-05'],
            ['student_id' => $alya->id, 'type' => 'success', 'title' => 'Pencapaian Target Membaca', 'body' => 'Selamat! Alya telah mencapai target membaca bulanan Juli 2026. Teruskan!', 'created_at' => '2026-07-07'],
            ['student_id' => $alya->id, 'type' => 'info', 'title' => ' Jadwal Ujian Praktik Biologi', 'body' => 'Ujian praktik Biologi akan dilaksanakan pada hari Rabu, 16 Juli 2026. Siapkan alat dan bahan yang diperlukan.', 'created_at' => '2026-07-06'],
        ];

        foreach ($notifications as $n) {
            Notification::create($n);
        }

        // ── Extracurriculars ───────────────────────────────────
        Extracurricular::create([
            'student_id' => $alif->id,
            'name' => 'Coding Club',
            'score' => 'A',
            'note' => 'Aktif sebagai koordinator tim web sekolah.',
        ]);

        Extracurricular::create([
            'student_id' => $alif->id,
            'name' => 'Basket',
            'score' => 'B+',
            'note' => 'Kehadiran dan kerja sama baik.',
        ]);

        Extracurricular::create([
            'student_id' => $alya->id,
            'name' => 'Klub Sains',
            'score' => 'A',
            'note' => 'Aktif dalam proyek eksperimen lingkungan.',
        ]);

        Extracurricular::create([
            'student_id' => $alya->id,
            'name' => 'Paduan Suara',
            'score' => 'A-',
            'note' => 'Konsisten mengikuti latihan.',
        ]);
    }

    private function seedAssessments(TeachingAssignment $ta, Student $student, array $components): void
    {
        foreach ($components as $component => $items) {
            foreach ($items as $item) {
                $assessment = Assessment::create([
                    'teaching_assignment_id' => $ta->id,
                    'title' => $item['title'],
                    'component' => $component,
                    'assessment_date' => $item['date'],
                    'max_score' => 100,
                    'published_at' => now(),
                ]);

                AssessmentScore::create([
                    'assessment_id' => $assessment->id,
                    'student_id' => $student->id,
                    'score' => $item['scores'][0],
                    'graded_at' => now(),
                ]);
            }
        }
    }

    private function seedAttendance(Student $student, User $recorder, AcademicPeriod $period): void
    {
        $start = Carbon::parse($period->start_date);
        $end = Carbon::parse(now()->min($period->end_date));
        $current = $start->copy();

        while ($current->lte($end)) {
            if ($current->isWeekday()) {
                $rand = rand(1, 100);
                $status = match (true) {
                    $rand <= 88 => 'present',
                    $rand <= 92 => 'sick',
                    $rand <= 96 => 'excused',
                    default => 'late',
                };

                Attendance::create([
                    'student_id' => $student->id,
                    'attendance_date' => $current->toDateString(),
                    'status' => $status,
                    'recorded_by' => $recorder->id,
                ]);
            }
            $current->addDay();
        }
    }
}
