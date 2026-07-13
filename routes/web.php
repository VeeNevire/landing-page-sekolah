<?php

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Portal\DashboardController;
use App\Http\Controllers\Portal\ReportController;
use App\Http\Controllers\Portal\StudentController;
use App\Http\Controllers\Guru\GuruController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'beranda'])->name('beranda');
Route::get('/profil', [HomeController::class, 'profil'])->name('profil');
Route::get('/akademik', [HomeController::class, 'akademik'])->name('akademik');
Route::get('/ppdb', [HomeController::class, 'ppdb'])->name('ppdb');
Route::get('/kontak', [HomeController::class, 'kontak'])->name('kontak');

Route::middleware(['auth', 'verified'])->prefix('portal')->name('portal.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/laporan', [ReportController::class, 'index'])->name('laporan');
    Route::get('/laporan/csv', [ReportController::class, 'exportCsv'])->name('laporan.csv');
    Route::get('/kehadiran', [StudentController::class, 'kehadiran'])->name('kehadiran');
    Route::get('/jadwal', [StudentController::class, 'jadwal'])->name('jadwal');
    Route::get('/tagihan', [StudentController::class, 'tagihan'])->name('tagihan');
    Route::get('/profil', [StudentController::class, 'profil'])->name('profil');
    Route::get('/notifikasi', [StudentController::class, 'notifikasi'])->name('notifikasi');
});

Route::middleware(['auth', 'verified', 'role:teacher,homeroom,admin,principal'])->prefix('guru')->name('guru.')->group(function () {
    Route::get('/dashboard', [GuruController::class, 'dashboard'])->name('dashboard');
    Route::get('/kelas', [GuruController::class, 'kelas'])->name('kelas');
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
});

Route::middleware(['auth', 'verified', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');

    Route::get('/users', [AdminController::class, 'users'])->name('users.index');
    Route::get('/users/create', [AdminController::class, 'usersCreate'])->name('users.create');
    Route::post('/users', [AdminController::class, 'usersStore'])->name('users.store');
    Route::get('/users/{user}/edit', [AdminController::class, 'usersEdit'])->name('users.edit');
    Route::get('/users/{user}/data', [AdminController::class, 'userData'])->name('users.data');
    Route::put('/users/{user}', [AdminController::class, 'usersUpdate'])->name('users.update');
    Route::patch('/users/{user}/toggle', [AdminController::class, 'usersToggle'])->name('users.toggle');
    Route::post('/users/{user}/reset-password', [AdminController::class, 'usersResetPassword'])->name('users.reset-password');
    Route::delete('/users/{user}', [AdminController::class, 'usersDestroy'])->name('users.destroy');

    Route::get('/students', [AdminController::class, 'students'])->name('students.index');
    Route::get('/students/create', [AdminController::class, 'studentsCreate'])->name('students.create');
    Route::post('/students', [AdminController::class, 'studentsStore'])->name('students.store');
    Route::get('/students/import', [AdminController::class, 'studentImportForm'])->name('students.import');
    Route::post('/students/import', [AdminController::class, 'studentImport'])->name('students.import.store');
    Route::get('/students/{student}/edit', [AdminController::class, 'studentsEdit'])->name('students.edit');
    Route::get('/students/{student}/data', [AdminController::class, 'studentData'])->name('students.data');
    Route::put('/students/{student}', [AdminController::class, 'studentsUpdate'])->name('students.update');
    Route::delete('/students/{student}', [AdminController::class, 'studentsDestroy'])->name('students.destroy');
    Route::get('/parents/list', [AdminController::class, 'parentsList'])->name('parents.list');

    Route::get('/subjects', [AdminController::class, 'subjects'])->name('subjects.index');
    Route::get('/subjects/{subject}/data', [AdminController::class, 'subjectData'])->name('subjects.data');
    Route::post('/subjects', [AdminController::class, 'subjectsStore'])->name('subjects.store');
    Route::put('/subjects/{subject}', [AdminController::class, 'subjectsUpdate'])->name('subjects.update');
    Route::delete('/subjects/{subject}', [AdminController::class, 'subjectsDestroy'])->name('subjects.destroy');

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

    Route::get('/parent-student', [AdminController::class, 'parentStudent'])->name('parent-student.index');
    Route::post('/parent-student', [AdminController::class, 'parentStudentStore'])->name('parent-student.store');
    Route::delete('/parent-student', [AdminController::class, 'parentStudentDestroy'])->name('parent-student.destroy');

    Route::get('/audit', [AdminController::class, 'audit'])->name('audit.index');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';
