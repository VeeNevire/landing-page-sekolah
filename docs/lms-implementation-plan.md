# LMS Implementation Plan — InvestaSchool

> Transformasi dari sistem penilaian akademik menjadi Learning Management System (LMS) berbasis struktur Teaching Assignment yang sudah ada.

---

## Daftar Isi

1. [Ringkasan](#ringkasan)
2. [Arsitektur Dasar](#arsitektur-dasar)
3. [Fase 1: Enhanced Content Management](#fase-1-enhanced-content-management)
4. [Fase 2: Assignments & Submissions](#fase-2-assignments--submissions)
5. [Fase 3: Quiz Online & Bank Soal](#fase-3-quiz-online--bank-soal)
6. [Gradebook Integration](#gradebook-integration)
7. [Timeline](#timeline)
8. [Catatan Teknis](#catatan-teknis)

---

## Ringkasan

Saat ini sistem hanya memiliki:

- **Materi** — link URL saja, tanpa upload file, tanpa struktur
- **Penilaian** — guru input angka, tidak ada pengumpulan tugas atau kuis online
- **Gradebook** — dengan bobot tetap: Quiz 15%, Tugas 20%, Proyek 20%, UTS 20%, UAS 25%

Target transformasi mencakup **3 fitur utama LMS**:

| Fitur | Deskripsi |
|---|---|
| **Enhanced Content** | Upload file (PDF, DOC, PPT, gambar, video), struktur Modul/Topik, drag-drop urutan |
| **Assignments** | Tugas + pengumpulan file oleh siswa + penilaian + feedback |
| **Kuis Online** | Bank soal (PG, Essay, True/False), timer, auto-grade, pembahasan |

---

## Arsitektur Dasar

### Struktur Course

**Teaching Assignment** yang sudah ada dijadikan sebagai **Course**:

```
Teaching Assignment (guru + mapel + kelas + periode)
  ├── Course Modules (baru) 🗂️
  │     ├── Materi (enhanced: file + link + embed)
  │     ├── Tugas (baru)
  │     └── Kuis (baru)
  ├── Assessment (existing, nilai offline: quiz/homework/project/uts/uas)
  └── Jadwal (existing)
```

### Relasi Model (baru)

```
TeachingAssignment
  ├── hasMany → CourseModule (ordered)
  │     └── hasMany → Material (enhanced)
  ├── hasMany → Assignment
  │     └── hasMany → Submission
  │           └── hasOne → SubmissionGrade
  ├── hasMany → Quiz
  │     └── belongsToMany → QuestionBank (via QuizQuestion)
  │           └── hasMany → QuizAttempt
  │                 └── hasMany → QuizAnswer
  └── hasMany → Assessment (existing)
        └── hasMany → AssessmentScore (existing)
```

---

## Fase 1: Enhanced Content Management

### 1.1 Database Migration

#### Tabel `course_modules` (baru)

```php
Schema::create('course_modules', function (Blueprint $table) {
    $table->id();
    $table->foreignId('teaching_assignment_id')
          ->constrained()
          ->cascadeOnDelete();
    $table->string('title', 255);
    $table->text('description')->nullable();
    $table->integer('order')->default(0);
    $table->timestamps();
});
```

#### Modifikasi tabel `materials`

```php
// Migration: alter_materials_for_lms
Schema::table('materials', function (Blueprint $table) {
    $table->foreignId('module_id')
          ->nullable()
          ->constrained('course_modules')
          ->nullOnDelete();
    $table->string('file_path', 500)->nullable()->after('url');
    $table->string('file_name', 255)->nullable()->after('file_path');
    $table->unsignedBigInteger('file_size')->nullable()->after('file_name');
    $table->string('file_type', 100)->nullable()->after('file_size');
    $table->string('type', 20)->default('link')
          ->after('description');         // 'file', 'link', 'embed'
    $table->integer('order')->default(0);
});
```

### 1.2 Storage

- Folder: `storage/app/public/lms/materials/`
- Sub-folder per teaching assignment: `lms/materials/{ta_id}/`
- Symlink `public/storage` sudah ada

### 1.3 Fitur Guru

| Aksi | Endpoint | View |
|---|---|---|
| Buat module | `POST /guru/module` | `guru.module.create` |
| Edit module | `PUT /guru/module/{id}` | `guru.module.edit` |
| Hapus module | `DELETE /guru/module/{id}` | — |
| Reorder module | `POST /guru/module/reorder` | — |
| Upload materi (file) | `POST /guru/materi` (modified) | `guru.materi` |
| Edit materi | `PUT /guru/materi/{id}` | Modal edit |
| Reorder materi | `POST /guru/materi/reorder` | — |
| Download file | `GET /guru/materi/{id}/download` | — |

### 1.4 Fitur Siswa

| Aksi | Endpoint | View |
|---|---|---|
| Lihat materi per-modul | `GET /siswa/materi` (modified) | `siswa.materi` |
| Preview file | `GET /siswa/materi/{id}/preview` | In-browser preview |
| Download file | `GET /siswa/materi/{id}/download` | — |
| Tandai selesai baca | `POST /siswa/materi/{id}/complete` | — |

### 1.5 Tampilan (Wireframe)

**Guru — Halaman Materi Builder:**
```
┌─────────────────────────────────────────┐
│  [+] Tambah Module (BAB 1)             │
├─────────────────────────────────────────┤
│ 🗂️  BAB 1: Pengenalan            ⚙️ 📄  │
│   ├── 📄 1.1 Pengertian Dasar    ⠇      │
│   ├── 🔗 1.2 Video Tutorial      ⠇      │
│   └── 📄 1.3 Ringkasan Materi    ⠇      │
│ 🗂️  BAB 2: Konsep Inti           ⚙️ 📄  │
│   └── [+] Tambah Materi                  │
└─────────────────────────────────────────┘
```

**Siswa — Halaman Materi Terstruktur:**
```
┌─────────────────────────────────────────┐
│  📚  Matematika Wajib          Kelas X  │
│  Guru: Pak Budi                ⏱️ 60%   │
├─────────────────────────────────────────┤
│ 🗂️  BAB 1: Pengenalan                   │
│   ├── ✅ 1.1 Pengertian Dasar           │
│   ├── ⏳ 1.2 Video Tutorial             │
│   └── 🔒 1.3 Ringkasan Materi           │
│ 🗂️  BAB 2: Konsep Inti                  │
│   └── 🔒 2.1 Definisi Formal            │
└─────────────────────────────────────────┘
```

---

## Fase 2: Assignments & Submissions

### 2.1 Database Migration

#### Tabel `assignments`

```php
Schema::create('assignments', function (Blueprint $table) {
    $table->id();
    $table->foreignId('teaching_assignment_id')
          ->constrained()
          ->cascadeOnDelete();
    $table->foreignId('module_id')
          ->nullable()
          ->constrained('course_modules')
          ->nullOnDelete();
    $table->string('title', 255);
    $table->text('instructions');
    $table->string('attachment_path', 500)->nullable();
    $table->string('attachment_name', 255)->nullable();
    $table->dateTime('due_date')->nullable();
    $table->decimal('max_score', 6, 2)->default(100);
    $table->boolean('allow_late_submission')->default(false);
    $table->dateTime('published_at')->nullable();
    $table->timestamps();
});
```

#### Tabel `submissions`

```php
Schema::create('submissions', function (Blueprint $table) {
    $table->id();
    $table->foreignId('assignment_id')
          ->constrained()
          ->cascadeOnDelete();
    $table->foreignId('student_id')
          ->constrained()
          ->cascadeOnDelete();
    $table->string('file_path', 500);
    $table->string('file_name', 255);
    $table->unsignedBigInteger('file_size');
    $table->text('notes')->nullable();
    $table->dateTime('submitted_at');
    $table->boolean('is_late')->default(false);
    $table->unique(['assignment_id', 'student_id']);
    $table->timestamps();
});
```

#### Tabel `submission_grades`

```php
Schema::create('submission_grades', function (Blueprint $table) {
    $table->id();
    $table->foreignId('submission_id')
          ->unique()
          ->constrained()
          ->cascadeOnDelete();
    $table->decimal('score', 6, 2);
    $table->text('feedback')->nullable();
    $table->foreignId('graded_by')->constrained('users');
    $table->dateTime('graded_at');
    $table->timestamps();
});
```

### 2.2 Alur Kerja

```
GURU:
  1. Buat Assignment
     → judul, instruksi, lampiran (opsional), due date
     → Simpan sebagai draft
  2. Publish Assignment
     → Muncul di halaman siswa
  3. Review Submissions
     → Lihat daftar siswa yang sudah/lum mengumpulkan
     → Preview file submission
     → Input nilai + feedback
  4. Grade otomatis masuk ke gradebook
     → Auto-create Assessment + AssessmentScore component: 'assignment'

SISWA:
  1. Lihat daftar tugas per-mapel
  2. Klik "Kerjakan" → upload file + catatan opsional
  3. Bisa edit/re-upload sebelum deadline
  4. Lihat nilai + feedback setelah dinilai
  5. Status: ⏳ Belum / ✅ Sudah / ⏰ Terlambat / 📝 Dinilai
```

### 2.3 Endpoints

| Aksi | Endpoint | Method |
|---|---|---|
| **Guru** | | |
| Index tugas | `/guru/tugas` | GET |
| Buat tugas | `/guru/tugas/create` | GET |
| Store tugas | `/guru/tugas` | POST |
| Edit tugas | `/guru/tugas/{id}/edit` | GET |
| Update tugas | `/guru/tugas/{id}` | PUT |
| Hapus tugas | `/guru/tugas/{id}` | DELETE |
| Publish tugas | `/guru/tugas/{id}/publish` | PATCH |
| Lihat submissions | `/guru/tugas/{id}/submissions` | GET |
| Nilai submission | `/guru/submissions/{id}/grade` | POST |
| **Siswa** | | |
| Index tugas | `/siswa/tugas` | GET |
| Detail tugas | `/siswa/tugas/{id}` | GET |
| Submit tugas | `/siswa/tugas/{id}/submit` | POST |
| **Umum** | | |
| Download file | `/download/assignment/{id}` | GET |
| Download file | `/download/submission/{id}` | GET |

### 2.4 Gradebook Integration

Saat guru memberikan nilai pada submission:

```php
// Di SubmissionGradeController@store
$assessment = Assessment::create([
    'teaching_assignment_id' => $submission->assignment->teaching_assignment_id,
    'title' => $submission->assignment->title,
    'component' => 'assignment',
    'assessment_date' => now(),
    'max_score' => $submission->assignment->max_score,
    'published_at' => now(),
]);

$score = AssessmentScore::create([
    'assessment_id' => $assessment->id,
    'student_id' => $submission->student_id,
    'score' => $request->score,
    'feedback' => $request->feedback,
]);
```

> **Catatan:** Bobot `assignment` perlu ditambahkan ke `PortalHelper::WEIGHTS`. Alternatif: jadikan sub-tipe dari `homework` atau buat pengaturan bobot dinamis.

### 2.5 Tampilan (Wireframe)

**Guru — Daftar Submission:**
```
┌────────────────────────────────────────────┐
│  📝  Tugas: Makalah Bab 1                 │
│  Due: 25 Juli 2026, 23:59                  │
│  Status: ✅ Published   (15 submitted/25)  │
├────────────────────────────────────────────┤
│ ✅ Andi Saputra   25 Jul 14:30  ☐ Nilai   │
│ ✅ Dewi Lestari   25 Jul 22:15  85   ⚙️    │
│ ⏰ Budi Hartono   26 Jul 08:10  70   ⚙️    │
│ ❌                    Belum          ⏰     │
└────────────────────────────────────────────┘
```

**Siswa — Halaman Tugas:**
```
┌────────────────────────────────────────────┐
│  📝  Makalah Bab 1                         │
│  Mapel: Matematika Wajib                   │
│  Due: 25 Juli 2026, 23:59                  │
│                                            │
│  Instruksi:                                │
│  Buat makalah tentang...                   │
│                                            │
│  Lampiran: 📎  contoh-makalah.pdf          │
│                                            │
│  ┌────────────────────────────────────┐    │
│  │  Status: ✅ Sudah Dikumpulkan      │    │
│  │  File: makalah-andi.pdf  (2.4 MB)  │    │
│  │  Nilai: 85 / 100                   │    │
│  │  Feedback: Bagus, rapi.            │    │
│  └────────────────────────────────────┘    │
└────────────────────────────────────────────┘
```

---

## Fase 3: Quiz Online & Bank Soal

### 3.1 Database Migration

#### Tabel `question_banks`

```php
Schema::create('question_banks', function (Blueprint $table) {
    $table->id();
    $table->foreignId('teaching_assignment_id')
          ->constrained()
          ->cascadeOnDelete();
    $table->enum('question_type', ['multiple_choice', 'essay', 'true_false']);
    $table->text('question_text');
    $table->json('options')->nullable();
    $table->text('correct_answer')->nullable();
    $table->decimal('points', 6, 2)->default(1.00);
    $table->text('explanation')->nullable();
    $table->timestamps();
});
```

**Struktur JSON untuk `options` (PG):**
```json
[
    {"label": "A", "text": "Pilihan pertama"},
    {"label": "B", "text": "Pilihan kedua"},
    {"label": "C", "text": "Pilihan ketiga"},
    {"label": "D", "text": "Pilihan keempat"}
]
```

#### Tabel `quizzes`

```php
Schema::create('quizzes', function (Blueprint $table) {
    $table->id();
    $table->foreignId('teaching_assignment_id')
          ->constrained()
          ->cascadeOnDelete();
    $table->foreignId('module_id')
          ->nullable()
          ->constrained('course_modules')
          ->nullOnDelete();
    $table->string('title', 255);
    $table->text('instructions')->nullable();
    $table->unsignedInteger('time_limit')->nullable();   // menit
    $table->unsignedTinyInteger('max_attempts')->default(1);
    $table->boolean('shuffle_questions')->default(false);
    $table->boolean('shuffle_options')->default(false);
    $table->boolean('show_result_immediately')->default(false);
    $table->dateTime('published_at')->nullable();
    $table->dateTime('start_date')->nullable();
    $table->dateTime('end_date')->nullable();
    $table->timestamps();
});
```

#### Tabel `quiz_questions`

```php
Schema::create('quiz_questions', function (Blueprint $table) {
    $table->id();
    $table->foreignId('quiz_id')->constrained()->cascadeOnDelete();
    $table->foreignId('question_bank_id')->constrained()->cascadeOnDelete();
    $table->unsignedInteger('order');
    $table->decimal('points', 6, 2);  // override dari bank
    $table->timestamps();
});
```

#### Tabel `quiz_attempts`

```php
Schema::create('quiz_attempts', function (Blueprint $table) {
    $table->id();
    $table->foreignId('quiz_id')->constrained()->cascadeOnDelete();
    $table->foreignId('student_id')->constrained()->cascadeOnDelete();
    $table->unsignedTinyInteger('attempt_number');
    $table->dateTime('started_at');
    $table->dateTime('submitted_at')->nullable();
    $table->decimal('total_score', 6, 2)->nullable();
    $table->enum('status', ['in_progress', 'submitted', 'graded']);
    $table->unique(['quiz_id', 'student_id', 'attempt_number']);
    $table->timestamps();
});
```

#### Tabel `quiz_answers`

```php
Schema::create('quiz_answers', function (Blueprint $table) {
    $table->id();
    $table->foreignId('quiz_attempt_id')->constrained()->cascadeOnDelete();
    $table->foreignId('quiz_question_id')->constrained()->cascadeOnDelete();
    $table->text('answer_text')->nullable();       // untuk essay
    $table->string('selected_option')->nullable();  // untuk PG/TF
    $table->boolean('is_correct')->nullable();      // null untuk essay (manual)
    $table->decimal('score', 6, 2)->nullable();     // untuk essay
    $table->text('feedback')->nullable();
    $table->unique(['quiz_attempt_id', 'quiz_question_id']);
    $table->timestamps();
});
```

### 3.2 Alur Kerja

```
GURU:
  1. Bank Soal
     → Buat soal PG / Essay / True-False
     → Import dari Excel (opsional)

  2. Buat Kuis
     → Judul, instruksi, waktu, attempts
     → Pilih soal dari bank (drag-drop)
     → Atur poin per soal

  3. Publish kuis
     → Bisa atur jadwal (start_date - end_date)

  4. Review hasil
     → Lihat skor otomatis untuk PG/TF
     → Nilai manual untuk essay + feedback
     → Publikasi nilai (masuk ke gradebook)

SISWA:
  1. Lihat kuis yang tersedia
  2. Klik "Mulai Kuis" → timer dimulai
  3. Jawab soal (PG: klik pilihan, Essay: tulis teks)
  4. Submit (manual atau auto saat waktu habis)
  5. Lihat hasil langsung (jika show_result_immediately)
  6. Lihat pembahasan + feedback
```

### 3.3 Auto-grade Logic

```php
// Saat submit kuis
$totalPoints = 0;
$earnedPoints = 0;

foreach ($attempt->quizAnswers as $answer) {
    $question = $answer->quizQuestion->questionBank;

    switch ($question->question_type) {
        case 'multiple_choice':
        case 'true_false':
            $isCorrect = $answer->selected_option === $question->correct_answer;
            $answer->update([
                'is_correct' => $isCorrect,
                'score' => $isCorrect ? $question->points : 0,
            ]);
            $earnedPoints += $isCorrect ? $question->points : 0;
            break;

        case 'essay':
            // Guru nilai manual
            break;
    }

    $totalPoints += $question->points;
}

$finalScore = $totalPoints > 0 ? ($earnedPoints / $totalPoints) * 100 : 0;
$attempt->update([
    'total_score' => $finalScore,
    'submitted_at' => now(),
    'status' => $hasEssay ? 'submitted' : 'graded',
]);
```

### 3.4 Timer Implementation (JavaScript)

```javascript
// Countdown timer di halaman kuis
const timer = setInterval(() => {
    const remaining = endTime - Date.now();
    if (remaining <= 0) {
        clearInterval(timer);
        document.getElementById('quizForm').submit(); // auto-submit
    }
    // Tampilkan MM:SS
}, 1000);
```

### 3.5 Endpoints

| Aksi | Endpoint | Method |
|---|---|---|
| **Guru** | | |
| Bank Soal index | `/guru/bank-soal` | GET |
| Buat soal | `/guru/bank-soal` | POST |
| Edit soal | `/guru/bank-soal/{id}` | PUT |
| Hapus soal | `/guru/bank-soal/{id}` | DELETE |
| Import soal | `/guru/bank-soal/import` | POST |
| Kuis index | `/guru/kuis` | GET |
| Buat kuis | `/guru/kuis/create` | GET |
| Store kuis | `/guru/kuis` | POST |
| Edit kuis | `/guru/kuis/{id}/edit` | GET |
| Update kuis | `/guru/kuis/{id}` | PUT |
| Hapus kuis | `/guru/kuis/{id}` | DELETE |
| Publish kuis | `/guru/kuis/{id}/publish` | PATCH |
| Hasil kuis | `/guru/kuis/{id}/hasil` | GET |
| Nilai essay | `/guru/kuis/{attemptId}/nilai-essay` | POST |
| **Siswa** | | |
| Index kuis | `/siswa/kuis` | GET |
| Mulai kuis | `/siswa/kuis/{id}/mulai` | POST |
| Kerjakan kuis | `/siswa/kuis/{attemptId}` | GET |
| Submit kuis | `/siswa/kuis/{attemptId}/submit` | POST |
| Hasil kuis | `/siswa/kuis/{attemptId}/hasil` | GET |

### 3.6 Tampilan (Wireframe)

**Siswa — Sedang Mengerjakan Kuis:**
```
┌─────────────────────────────────────────────┐
│  Kuis Bab 1 — Matematika Wajib              │
│  ⏱️ 14:32                         ⏳ 3/10  │
├─────────────────────────────────────────────┤
│  Soal 3 dari 10 (Bobot: 5 poin)             │
│                                            │
│  Manakah yang bukan merupakan bilangan      │
│  prima?                                     │
│                                            │
│  ○ A. 2                                     │
│  ● B. 4                                     │
│  ○ C. 5                                     │
│  ○ D. 7                                     │
│                                            │
│  ┌──────┐  ┌──────────┐  ┌──────┐         │
│  │Prev  │  │Ragu-ragu │  │ Next │         │
│  └──────┘  └──────────┘  └──────┘         │
│                                            │
│  Soal:  ○○○○●○○○○○                         │
│  Ragu:  ──●●─────                          │
│                                            │
│           ┌──────────┐                     │
│           │ Kumpulkan │                     │
│           └──────────┘                     │
└─────────────────────────────────────────────┘
```

---

## Gradebook Integration

### Integrasi Nilai dari Fitur LMS ke Gradebook

Semua nilai dari Assignment dan Kuis akan otomatis masuk ke tabel `assessments` dan `assessment_scores` yang sudah ada, sehingga nilai-nilai tersebut otomatis masuk ke perhitungan rapor, CSV export, dan dashboard orang tua.

| Fitur LMS | Component di `assessments` | Sumber Nilai |
|---|---|---|
| Assignment | `assignment` | Nilai dari `submission_grades.score` |
| Kuis (PG/TF) | `quiz` | Auto-grade `total_score` |
| Kuis (Essay) | `quiz` | Manual grade guru |
| Existing | `quiz/homework/project/uts/uas` | Input manual guru |

### Penyesuaian Bobot

```php
// PortalHelper::WEIGHTS (usulan baru)
const WEIGHTS = [
    'quiz'      => 0.10,   // 10% (dulu 15%)
    'homework'  => 0.10,   // 10%
    'project'   => 0.20,   // 20%
    'assignment'=> 0.10,   // 10% (baru)
    'uts'       => 0.20,   // 20%
    'uas'       => 0.25,   // 25%
    // Siswa bisa remedial
];
```

Atau buat **pengaturan bobot dinamis** di level Admin → Pengaturan Akademik.

### Grade Publishing Flow

```
Assignment graded → auto-create Assessment → langsung published
Kuis submitted → auto-create Assessment → published jika show_result
Kuis essay → guru nilai manual → published manual

Siswa/Parent bisa melihat:
  assignment → ✅ Langsung (setelah dinilai)
  kuis PG/TF → ✅ Langsung (show_result_immediately)
  kuis Essay → ⏳ Setelah guru nilai manual
```

---

## Timeline

| Fase | Fitur | Tabel Baru | Modifikasi Tabel | File Baru/Ubah | Estimasi |
|---|---|---|---|---|---|
| **Fase 1** | Enhanced Materi + Modul | 1 (`course_modules`) | 1 (`materials`) | 3 migrations, 2 views (refactor), 1 controller (refactor), 1 JS | **3-4 hari** |
| **Fase 2** | Tugas + Submission | 3 (`assignments`, `submissions`, `submission_grades`) | — | 3 migrations, 4 views baru, 1 controller baru, route baru | **4-5 hari** |
| **Fase 3** | Kuis Online + Bank Soal | 5 (`question_banks`, `quizzes`, `quiz_questions`, `quiz_attempts`, `quiz_answers`) | — | 5 migrations, 6 views baru, 1 controller baru, JS timer, route baru | **6-7 hari** |

> **Total estimasi: 13-16 hari kerja**

---

## Catatan Teknis

### 1. File Upload Validation

```php
// Config validasi file
'allowed_materials' => [
    'pdf', 'doc', 'docx', 'ppt', 'pptx', 'xls', 'xlsx',
    'jpg', 'jpeg', 'png', 'gif', 'svg', 'webp',
    'mp4', 'webm', 'mp3',
    'zip', 'rar',
],
'max_file_size' => 50 * 1024, // 50 MB (dalam KB)
```

### 2. Storage Structure

```
storage/app/public/lms/
├── materials/{teaching_assignment_id}/
│   ├── {uuid}-{filename.ext}
├── assignments/{assignment_id}/
│   ├── {uuid}-{filename.ext}
└── submissions/{assignment_id}/
    ├── {student_id}/
        └── {uuid}-{filename.ext}
```

### 3. Security Considerations

- **Authorization**: Setiap akses materi/tugas/kuis harus dicek:
  - Guru: hanya bisa akses TA miliknya
  - Siswa: hanya bisa akses TA yang terkait dengan kelasnya
  - Parent: hanya bisa lihat yang sudah dipublikasi (via gradebook)
- **File upload**: validasi tipe file, size limit, scan virus (jika ada)
- **Quiz timer**: server-side validation untuk cegah kecurangan
- **CSRF**: semua form pakai CSRF token (Laravel default)

### 4. Route Prefix Structure

```php
// Route grouping yang diusulkan
Route::middleware(['auth', 'verified', 'role:teacher,homeroom'])->prefix('guru')->name('guru.')->group(function () {
    // LMS routes (baru)
    Route::resource('module', ModuleController::class)->except(['show']);
    Route::post('module/reorder', [ModuleController::class, 'reorder'])->name('module.reorder');
    Route::resource('tugas', TugasController::class);
    Route::patch('tugas/{tugas}/publish', [TugasController::class, 'publish'])->name('tugas.publish');
    Route::get('tugas/{tugas}/submissions', [TugasController::class, 'submissions'])->name('tugas.submissions');
    Route::post('submissions/{submission}/grade', [TugasController::class, 'grade'])->name('submissions.grade');
    Route::resource('bank-soal', BankSoalController::class);
    Route::resource('kuis', KuisController::class);
    Route::patch('kuis/{kuis}/publish', [KuisController::class, 'publish'])->name('kuis.publish');
    Route::get('kuis/{kuis}/hasil', [KuisController::class, 'hasil'])->name('kuis.hasil');
    Route::post('kuis/{attempt}/nilai-essay', [KuisController::class, 'nilaiEssay'])->name('kuis.nilai-essay');
});

Route::middleware(['auth', 'verified', 'role:student'])->prefix('siswa')->name('siswa.')->group(function () {
    // LMS routes (baru)
    Route::resource('tugas', SiswaTugasController::class)->only(['index', 'show']);
    Route::post('tugas/{tugas}/submit', [SiswaTugasController::class, 'submit'])->name('tugas.submit');
    Route::resource('kuis', SiswaKuisController::class)->only(['index']);
    Route::post('kuis/{kuis}/mulai', [SiswaKuisController::class, 'mulai'])->name('kuis.mulai');
    Route::get('kuis/{attempt}/kerjakan', [SiswaKuisController::class, 'kerjakan'])->name('kuis.kerjakan');
    Route::post('kuis/{attempt}/submit', [SiswaKuisController::class, 'submit'])->name('kuis.submit');
    Route::get('kuis/{attempt}/hasil', [SiswaKuisController::class, 'hasil'])->name('kuis.hasil');
});

// Download routes (authenticated, with access check)
Route::middleware(['auth', 'verified'])->prefix('download')->name('download.')->group(function () {
    Route::get('materi/{materi}', [DownloadController::class, 'materi'])->name('materi');
    Route::get('assignment/{assignment}', [DownloadController::class, 'assignment'])->name('assignment');
    Route::get('submission/{submission}', [DownloadController::class, 'submission'])->name('submission');
});
```

### 5. Controller Structure Recommendation

Daripada memperbesar `GuruController` dan `SiswaController` yang sudah besar, buat **controller baru** per fitur:

```
app/Http/Controllers/
├── Guru/
│   ├── GuruController.php (existing, biarkan)
│   ├── ModuleController.php  🆕
│   ├── TugasController.php   🆕
│   ├── BankSoalController.php 🆕
│   └── KuisController.php    🆕
├── Siswa/
│   ├── SiswaController.php (existing, biarkan)
│   ├── SiswaTugasController.php  🆕
│   └── SiswaKuisController.php   🆕
└── Lms/
    └── DownloadController.php 🆕
```

### 6. Model Relationships (baru)

```php
// TeachingAssignment.php
public function modules(): HasMany
{
    return $this->hasMany(CourseModule::class)->ordered();
}

public function assignments(): HasMany
{
    return $this->hasMany(Assignment::class);
}

public function quizzes(): HasMany
{
    return $this->hasMany(Quiz::class);
}

public function questionBanks(): HasMany
{
    return $this->hasMany(QuestionBank::class);
}
```

---

## Milestone Checklist

### Fase 1 ✅ Enhanced Content

- [ ] Migration `course_modules` table
- [ ] Migration modify `materials` table (tambah kolom)
- [ ] Buat folder storage `lms/materials/`
- [ ] Model: `CourseModule`
- [ ] Controller: `ModuleController` (CRUD + reorder)
- [ ] Update `GuruController@materi` (upload file, module selection)
- [ ] Update `SiswaController@materi` (tampilan terstruktur per module)
- [ ] Preview file in-browser (PDF, gambar)
- [ ] Download file endpoint
- [ ] View guru: module builder with drag-drop
- [ ] View siswa: materi terstruktur

### Fase 2 ✅ Assignments

- [ ] Migration `assignments` table
- [ ] Migration `submissions` table
- [ ] Migration `submission_grades` table
- [ ] Model: `Assignment`, `Submission`, `SubmissionGrade`
- [ ] Controller: `TugasController` (CRUD + publish)
- [ ] Controller: `SiswaTugasController` (submit + status)
- [ ] View guru: daftar submission + grading panel
- [ ] View siswa: detail tugas + upload form
- [ ] Gradebook integration (auto-create Assessment + AssessmentScore)
- [ ] Download file lampiran tugas

### Fase 3 ✅ Kuis Online

- [ ] Migration `question_banks` table
- [ ] Migration `quizzes` table
- [ ] Migration `quiz_questions` table
- [ ] Migration `quiz_attempts` table
- [ ] Migration `quiz_answers` table
- [ ] Model: `QuestionBank`, `Quiz`, `QuizQuestion`, `QuizAttempt`, `QuizAnswer`
- [ ] Controller: `BankSoalController` (CRUD + import)
- [ ] Controller: `KuisController` (CRUD + publish + hasil + nilai essay)
- [ ] Controller: `SiswaKuisController` (mulai + kerjakan + submit + hasil)
- [ ] JS timer + auto-submit
- [ ] View guru: bank soal, builder kuis, hasil kuis
- [ ] View siswa: kerjakan kuis, hasil kuis
- [ ] Gradebook integration
- [ ] Loading indicator saat submit
- [ ] Konfirmasi submit

---

> **Dokumen ini dapat diperbarui seiring dengan perkembangan implementasi.**
