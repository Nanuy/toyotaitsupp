<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\ReportPublicController;
use App\Http\Controllers\ITSupportController;
use App\Http\Controllers\ReportDetailController;



// =======================
// ✅ PUBLIC
// =======================

// Halaman awal
Route::get('/', fn () => view('welcome'))->name('welcome.view');

// Halaman uji layout
Route::get('/master', fn () => view('master'))->name('master.view');

// Form Login & Register
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [RegisterController::class, 'register']);
// Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Form Lapor Tanpa Login
Route::get('/lapor', [ReportPublicController::class, 'create'])->name('lapor.create');
Route::post('/lapor', [ReportPublicController::class, 'store'])->name('lapor.store');
Route::get('/lapor/{id}', [ReportPublicController::class, 'show'])->name('lapor.show');

// Tombol "Accept" hanya untuk user yang sudah login
Route::post('/lapor/{id}/accept', [ReportPublicController::class, 'accept'])
    ->middleware('auth')
    ->name('lapor.accept');

// =======================
// ✅ PROTECTED (LOGIN + ROLE)
// =======================
Route::middleware(['auth'])->group(function () {

    // ✅ SUPERADMIN DASHBOARD
    Route::middleware(['role:superadmin'])->group(function () {
        Route::get('/dashboard-superadmin', fn () => view('dashboard.superadmin'))->name('dashboard.superadmin');
    });

    // ✅ IT SUPPORT DASHBOARD
    Route::middleware(['role:it_supp'])->group(function () {
        Route::get('/dashboard-it', fn () => redirect()->route('it_support.reports'))->name('dashboard.it');

        // Laporan IT Support
        Route::get('/reports', [ITSupportController::class, 'showReports'])->name('it_support.reports');
        Route::post('/report/{id}/accept', [ITSupportController::class, 'accept'])->name('report.accept');
        Route::get('/report/{id}/surat', [ITSupportController::class, 'generateSuratTugas'])->name('report.surat');
        Route::post('/report/{id}/pindah-divisi', [ITSupportController::class, 'pindahDivisi'])->name('report.pindahDivisi');
        Route::get('/report/{id}/detail', [ReportController::class, 'detail'])->name('report.detail');
        Route::get('/report-detail/{id}/edit', [ReportDetailController::class, 'edit'])->name('report_detail.edit');
        Route::put('/report-detail/{id}', [ReportDetailController::class, 'update'])->name('report_detail.update');


    });

    // ✅ LAPORAN UMUM (ADMIN & IT Support)
    Route::prefix('report')->name('report.')->group(function () {
        Route::get('/create', [ReportController::class, 'create'])->name('create');
        Route::post('/store', [ReportController::class, 'store'])->name('store');
        Route::get('/', [ReportController::class, 'index'])->name('index');
        Route::get('/{id}', [ReportController::class, 'show'])->name('show');

        // Tambah detail & update surat jalan
        Route::post('/{id}/add-detail', [ITSupportController::class, 'addDetail'])->name('addDetail');
        Route::post('/{id}/forward', [ITSupportController::class, 'forward'])->name('forward');
        Route::put('/{id}/update-surat-jalan', [ReportController::class, 'updateSuratJalanDate'])->name('updateSuratJalanDate');

        // Surat dan detail tindakan
        Route::get('/{id}/surat', [ITSupportController::class, 'generateSuratTugas'])->name('surat');
        Route::get('/detail/{detail_id}/edit', [ITSupportController::class, 'editDetail'])->name('detail.edit');
        Route::post('/detail/{detail_id}/update', [ITSupportController::class, 'updateDetail'])->name('detail.update');
    });

});

// =======================
// ✅ DEBUG & TES
// =======================
Route::middleware(['auth', 'role:it_supp'])->get('/tes-role', function () {
    return 'Kamu berhasil masuk sebagai IT Support!';
});
