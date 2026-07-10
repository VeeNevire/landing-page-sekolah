<?php

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

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';
