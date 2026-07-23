<?php

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\JadwalController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PPDBController;
use App\Http\Controllers\Portal\DashboardController;
use App\Http\Controllers\Portal\ReportController;
use App\Http\Controllers\Portal\StudentController;
use App\Http\Controllers\Guru\BankSoalController;
use App\Http\Controllers\Guru\GuruController;
use App\Http\Controllers\Guru\KuisController;
use App\Http\Controllers\Guru\ModuleController;
use App\Http\Controllers\Guru\TugasController;
use App\Http\Controllers\Lms\DownloadController;
use App\Http\Controllers\Siswa\SiswaController;
use App\Http\Controllers\Siswa\SiswaKuisController;
use App\Http\Controllers\Siswa\SiswaTugasController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'beranda'])->name('beranda');
Route::get('/profil', [HomeController::class, 'profil'])->name('profil');
Route::get('/akademik', [HomeController::class, 'akademik'])->name('akademik');
Route::get('/ppdb', [HomeController::class, 'ppdb'])->name('ppdb');
Route::get('/kontak', [HomeController::class, 'kontak'])->name('kontak');

Route::prefix('ppdb')->name('ppdb.')->group(function () {
    Route::get('/daftar', [PPDBController::class, 'start'])->name('start');
    // Route::get('/auth/google', [PPDBController::class, 'redirectToGoogle'])->name('auth.google');
    // Route::get('/auth/google/callback', [PPDBController::class, 'handleGoogleCallback']);
    Route::post('/register', [PPDBController::class, 'manualRegisterStore'])->name('manual.register');

    Route::middleware('auth')->group(function () {
        Route::get('/form', [PPDBController::class, 'showForm'])->name('form');
        Route::post('/form/step-1', [PPDBController::class, 'saveStep1'])->name('form.step1');
        Route::post('/form/step-2', [PPDBController::class, 'saveStep2'])->name('form.step2');
        Route::get('/upload', [PPDBController::class, 'uploadPage'])->name('upload');
        Route::post('/upload', [PPDBController::class, 'uploadStore'])->name('upload.store');
        Route::delete('/upload/{document}', [PPDBController::class, 'uploadDestroy'])->name('upload.destroy');
        Route::get('/document/{document}/preview', [PPDBController::class, 'previewDocument'])->name('document.preview');
        Route::post('/submit', [PPDBController::class, 'finalSubmit'])->name('submit');
        Route::get('/status', [PPDBController::class, 'status'])->name('status');
        Route::get('/payment', [PPDBController::class, 'payment'])->name('payment');
        Route::post('/pay', [PPDBController::class, 'payProcess'])->name('pay');
        Route::get('/sukses', [PPDBController::class, 'success'])->name('success');
    });
});

Route::middleware(['auth', 'verified', 'role:parent'])->prefix('portal')->name('portal.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/laporan', [ReportController::class, 'index'])->name('laporan');
    Route::get('/laporan/csv', [ReportController::class, 'exportCsv'])->name('laporan.csv');
    Route::get('/kehadiran', [StudentController::class, 'kehadiran'])->name('kehadiran');
    Route::get('/jadwal', [StudentController::class, 'jadwal'])->name('jadwal');
    Route::get('/tagihan', [StudentController::class, 'tagihan'])->name('tagihan');
    Route::post('/tagihan/{billing}/bayar', [StudentController::class, 'tagihanBayar'])->name('tagihan.bayar');
    Route::get('/profil', [StudentController::class, 'profil'])->name('profil');
    Route::get('/notifikasi', [StudentController::class, 'notifikasi'])->name('notifikasi');
});

Route::middleware(['auth', 'verified', 'role:student'])->prefix('siswa')->name('siswa.')->group(function () {
    Route::get('/dashboard', [SiswaController::class, 'dashboard'])->name('dashboard');
    Route::get('/nilai', [SiswaController::class, 'nilai'])->name('nilai');
    Route::get('/kehadiran', [SiswaController::class, 'kehadiran'])->name('kehadiran');
    Route::get('/jadwal', [SiswaController::class, 'jadwal'])->name('jadwal');
    Route::get('/materi', [SiswaController::class, 'materi'])->name('materi');
    Route::get('/tugas', [SiswaTugasController::class, 'index'])->name('tugas.index');
    Route::get('/tugas/{assignment}', [SiswaTugasController::class, 'show'])->name('tugas.show');
    Route::post('/tugas/{assignment}/submit', [SiswaTugasController::class, 'submit'])->name('tugas.submit');
    Route::get('/kuis', [SiswaKuisController::class, 'index'])->name('kuis.index');
    Route::post('/kuis/{quiz}/mulai', [SiswaKuisController::class, 'mulai'])->name('kuis.mulai');
    Route::get('/kuis/{attempt}/kerjakan', [SiswaKuisController::class, 'kerjakan'])->name('kuis.kerjakan');
    Route::post('/kuis/{attempt}/submit', [SiswaKuisController::class, 'submit'])->name('kuis.submit');
    Route::post('/kuis/{attempt}/auto-save', [SiswaKuisController::class, 'autoSave'])->name('kuis.auto-save');
    Route::get('/kuis/{attempt}/hasil', [SiswaKuisController::class, 'hasil'])->name('kuis.hasil');
    Route::get('/profil', [SiswaController::class, 'profil'])->name('profil');
});

Route::middleware(['auth', 'verified', 'role:teacher,homeroom,admin'])->prefix('guru')->name('guru.')->group(function () {
    Route::get('/dashboard', [GuruController::class, 'dashboard'])->name('dashboard');
    Route::get('/kelas', [GuruController::class, 'kelas'])->name('kelas');
    Route::get('/kelas/{className}/data', [GuruController::class, 'kelasData'])->name('kelas.data');
    Route::get('/nilai', [GuruController::class, 'nilai'])->name('nilai');
    Route::get('/nilai/{class}/{subject}', [GuruController::class, 'nilaiDetail'])->name('nilai.detail');
    Route::post('/nilai/{class}/{subject}', [GuruController::class, 'nilaiStore'])->name('nilai.store');
    Route::get('/absensi', [GuruController::class, 'absensi'])->name('absensi');
    Route::post('/absensi', [GuruController::class, 'absensiStore'])->name('absensi.store');
    Route::get('/jadwal', [GuruController::class, 'jadwal'])->name('jadwal');
    Route::get('/catatan', [GuruController::class, 'catatan'])->name('catatan');
    Route::post('/catatan', [GuruController::class, 'catatanStore'])->name('catatan.store');
    Route::get('/publikasi', [GuruController::class, 'publikasi'])->name('publikasi');
    Route::post('/publikasi/{class}', [GuruController::class, 'publikasiStore'])->name('publikasi.store');
    Route::get('/materi', [GuruController::class, 'materi'])->name('materi');
    Route::post('/materi', [GuruController::class, 'materiStore'])->name('materi.store');
    Route::delete('/materi/{material}', [GuruController::class, 'materiDestroy'])->name('materi.destroy');

    Route::post('/module', [ModuleController::class, 'store'])->name('module.store');
    Route::put('/module/{module}', [ModuleController::class, 'update'])->name('module.update');
    Route::delete('/module/{module}', [ModuleController::class, 'destroy'])->name('module.destroy');
    Route::post('/module/reorder', [ModuleController::class, 'reorder'])->name('module.reorder');
    Route::post('/module/reorder-materials', [ModuleController::class, 'reorderMaterials'])->name('module.reorder-materials');
    Route::get('/module/data', [ModuleController::class, 'data'])->name('module.data');

    Route::get('/tugas', [TugasController::class, 'index'])->name('tugas.index');
    Route::get('/tugas/create', [TugasController::class, 'create'])->name('tugas.create');
    Route::post('/tugas', [TugasController::class, 'store'])->name('tugas.store');
    Route::get('/tugas/{assignment}/edit', [TugasController::class, 'edit'])->name('tugas.edit');
    Route::put('/tugas/{assignment}', [TugasController::class, 'update'])->name('tugas.update');
    Route::delete('/tugas/{assignment}', [TugasController::class, 'destroy'])->name('tugas.destroy');
    Route::patch('/tugas/{assignment}/publish', [TugasController::class, 'publish'])->name('tugas.publish');
    Route::get('/tugas/{assignment}/submissions', [TugasController::class, 'submissions'])->name('tugas.submissions');
    Route::post('/submissions/{submission}/grade', [TugasController::class, 'grade'])->name('submissions.grade');

    Route::get('/bank-soal', [BankSoalController::class, 'index'])->name('bank-soal.index');
    Route::get('/bank-soal/create', [BankSoalController::class, 'create'])->name('bank-soal.create');
    Route::post('/bank-soal', [BankSoalController::class, 'store'])->name('bank-soal.store');
    Route::get('/bank-soal/{questionBank}', [BankSoalController::class, 'edit'])->name('bank-soal.edit')->whereNumber('questionBank');
    Route::put('/bank-soal/{questionBank}', [BankSoalController::class, 'update'])->name('bank-soal.update');
    Route::delete('/bank-soal/{questionBank}', [BankSoalController::class, 'destroy'])->name('bank-soal.destroy');

    Route::get('/kuis', [KuisController::class, 'index'])->name('kuis.index');
    Route::get('/kuis/create', [KuisController::class, 'create'])->name('kuis.create');
    Route::post('/kuis', [KuisController::class, 'store'])->name('kuis.store');
    Route::get('/kuis/{quiz}/edit', [KuisController::class, 'edit'])->name('kuis.edit');
    Route::put('/kuis/{quiz}', [KuisController::class, 'update'])->name('kuis.update');
    Route::delete('/kuis/{quiz}', [KuisController::class, 'destroy'])->name('kuis.destroy');
    Route::patch('/kuis/{quiz}/publish', [KuisController::class, 'publish'])->name('kuis.publish');
    Route::get('/kuis/{quiz}/hasil', [KuisController::class, 'hasil'])->name('kuis.hasil');
    Route::get('/kuis/{attempt}/nilai-essay-data', [KuisController::class, 'nilaiEssayData'])->name('kuis.nilai-essay-data');
    Route::post('/kuis/{attempt}/nilai-essay', [KuisController::class, 'nilaiEssay'])->name('kuis.nilai-essay');
});

Route::middleware(['auth', 'verified', 'role:admin,principal'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');

    Route::middleware('role:admin')->group(function () {
        Route::get('/users', [AdminController::class, 'users'])->name('users.index');
        Route::get('/users/create', [AdminController::class, 'usersCreate'])->name('users.create');
        Route::get('/users/{user}/edit', [AdminController::class, 'usersEdit'])->name('users.edit');
        Route::post('/users/{user}/reset-password', [AdminController::class, 'usersResetPassword'])->name('users.reset-password');
        Route::delete('/users/{user}', [AdminController::class, 'usersDestroy'])->name('users.destroy');
    });

    Route::post('/users', [AdminController::class, 'usersStore'])->name('users.store');
    Route::get('/users/{user}/data', [AdminController::class, 'userData'])->name('users.data');
    Route::put('/users/{user}', [AdminController::class, 'usersUpdate'])->name('users.update');
    Route::patch('/users/{user}/toggle', [AdminController::class, 'usersToggle'])->name('users.toggle');

    Route::get('/guru', [AdminController::class, 'guru'])->name('guru.index');
    Route::get('/guru/{user}/data', [AdminController::class, 'guruData'])->name('guru.data');

    Route::get('/students', [AdminController::class, 'students'])->name('students.index');
    Route::get('/students/create', [AdminController::class, 'studentsCreate'])->name('students.create');
    Route::post('/students', [AdminController::class, 'studentsStore'])->name('students.store');
    Route::get('/students/import', [AdminController::class, 'studentImportForm'])->name('students.import');
    Route::post('/students/import', [AdminController::class, 'studentImport'])->name('students.import.store');
    Route::get('/students/{student}/edit', [AdminController::class, 'studentsEdit'])->name('students.edit');
    Route::get('/students/{student}/data', [AdminController::class, 'studentData'])->name('students.data');
    Route::put('/students/{student}', [AdminController::class, 'studentsUpdate'])->name('students.update');
    Route::delete('/students/{student}', [AdminController::class, 'studentsDestroy'])->name('students.destroy');
    Route::post('/students/{student}/reset-password', [AdminController::class, 'studentResetPassword'])->name('students.reset-password');
    Route::get('/parents/list', [AdminController::class, 'parentsList'])->name('parents.list');
    Route::get('/check-email', [AdminController::class, 'checkEmail'])->name('check-email');
    Route::get('/jurusans/{jurusan}/kelas', [AdminController::class, 'kelasByJurusan'])->name('jurusans.kelas');

    Route::get('/subjects', [AdminController::class, 'subjects'])->name('subjects.index');
    Route::get('/subjects/{subject}/data', [AdminController::class, 'subjectData'])->name('subjects.data');
    Route::get('/subjects/{subject}/detail', [AdminController::class, 'subjectDetail'])->name('subjects.detail');
    Route::post('/subjects/{subject}/assign', [AdminController::class, 'subjectAssignStore'])->name('subjects.assign');
    Route::post('/subjects/assign-cs', [AdminController::class, 'subjectAssignCSStore'])->name('subjects.assign-cs');
    Route::post('/subjects', [AdminController::class, 'subjectsStore'])->name('subjects.store');
    Route::put('/subjects/{subject}', [AdminController::class, 'subjectsUpdate'])->name('subjects.update');
    Route::delete('/subjects/{subject}', [AdminController::class, 'subjectsDestroy'])->name('subjects.destroy');

    Route::get('/jurusans', [AdminController::class, 'jurusans'])->name('jurusans.index');
    Route::get('/jurusans/{jurusan}/data', [AdminController::class, 'jurusanData'])->name('jurusans.data');
    Route::post('/jurusans', [AdminController::class, 'jurusansStore'])->name('jurusans.store');
    Route::put('/jurusans/{jurusan}', [AdminController::class, 'jurusansUpdate'])->name('jurusans.update');
    Route::delete('/jurusans/{jurusan}', [AdminController::class, 'jurusansDestroy'])->name('jurusans.destroy');
    Route::get('/jurusans/{jurusan}/detail', [AdminController::class, 'jurusanDetail'])->name('jurusans.detail');
    Route::post('/jurusans/{jurusan}/subjects', [AdminController::class, 'jurusansSubjectsSave'])->name('jurusans.subjects.save');
    Route::post('/jurusans/{jurusan}/custom-subjects', [AdminController::class, 'jurusanCustomSubjectStore'])->name('jurusans.custom-subjects.store');
    Route::put('/jurusans/custom-subjects/{customSubject}', [AdminController::class, 'jurusanCustomSubjectUpdate'])->name('jurusans.custom-subjects.update');
    Route::delete('/jurusans/custom-subjects/{customSubject}', [AdminController::class, 'jurusanCustomSubjectDestroy'])->name('jurusans.custom-subjects.destroy');

    Route::get('/periods', [AdminController::class, 'periods'])->name('periods.index');
    Route::get('/periods/{period}/data', [AdminController::class, 'periodData'])->name('periods.data');
    Route::post('/periods', [AdminController::class, 'periodsStore'])->name('periods.store');
    Route::put('/periods/{period}', [AdminController::class, 'periodsUpdate'])->name('periods.update');
    Route::delete('/periods/{period}', [AdminController::class, 'periodsDestroy'])->name('periods.destroy');
    Route::patch('/periods/{period}/activate', [AdminController::class, 'periodsActivate'])->name('periods.activate');

    Route::get('/teaching', [AdminController::class, 'teaching'])->name('teaching.index');
    Route::get('/teaching/create', [AdminController::class, 'teachingCreate'])->name('teaching.create');
    Route::get('/teaching/{assignment}/data', [AdminController::class, 'teachingData'])->name('teaching.data');
    Route::post('/teaching', [AdminController::class, 'teachingStore'])->name('teaching.store');
    Route::put('/teaching/{assignment}', [AdminController::class, 'teachingUpdate'])->name('teaching.update');
    Route::delete('/teaching/{assignment}', [AdminController::class, 'teachingDestroy'])->name('teaching.destroy');

    Route::get('/jadwal', [JadwalController::class, 'index'])->name('jadwal.index');
    Route::post('/jadwal', [JadwalController::class, 'store'])->name('jadwal.store');
    Route::put('/jadwal/{jadwal}', [JadwalController::class, 'update'])->name('jadwal.update');
    Route::delete('/jadwal/{jadwal}', [JadwalController::class, 'destroy'])->name('jadwal.destroy');

    Route::get('/billing', [AdminController::class, 'billing'])->name('billing.index');
    Route::post('/billing', [AdminController::class, 'billingStore'])->name('billing.store');
    Route::put('/billing/{billing}', [AdminController::class, 'billingUpdate'])->name('billing.update');
    Route::delete('/billing/{billing}', [AdminController::class, 'billingDestroy'])->name('billing.destroy');

    Route::get('/parent-student', [AdminController::class, 'parentStudent'])->name('parent-student.index');
    Route::post('/parent-student', [AdminController::class, 'parentStudentStore'])->name('parent-student.store');
    Route::delete('/parent-student', [AdminController::class, 'parentStudentDestroy'])->name('parent-student.destroy');

    Route::get('/audit', [AdminController::class, 'audit'])->name('audit.index');

    Route::get('/applicants', [AdminController::class, 'applicants'])->name('applicants.index');
    Route::get('/applicants/{applicant}/data', [AdminController::class, 'applicantData'])->name('applicants.data');
    Route::patch('/applicants/{applicant}/status', [AdminController::class, 'applicantStatus'])->name('applicants.status');
    Route::post('/applicants/bulk-status', [AdminController::class, 'applicantsBulkStatus'])->name('applicants.bulk-status');
    Route::delete('/applicants/{applicant}', [AdminController::class, 'applicantDestroy'])->name('applicants.destroy');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::prefix('download')->name('download.')->group(function () {
        Route::get('/materi/{material}', [DownloadController::class, 'materi'])->name('materi');
        Route::get('/materi/{material}/preview', [DownloadController::class, 'preview'])->name('materi.preview');
        Route::get('/assignment/{assignment}', [DownloadController::class, 'assignment'])->name('assignment');
        Route::get('/submission/{submission}', [DownloadController::class, 'submission'])->name('submission');
    });
});

require __DIR__ . '/auth.php';
