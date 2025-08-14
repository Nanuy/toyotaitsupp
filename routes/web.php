<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\ReportPublicController;
use App\Http\Controllers\ITSupportController;
use App\Http\Controllers\ReportDetailController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ChartController;
use App\Http\Controllers\SignatureController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\SuperadminController;

// =======================
// ✅ PUBLIC
// =======================

// Halaman awal
Route::get('/laravel', fn () => view('welcome'))->name('welcome.view');

// Halaman uji layout
Route::get('/master', fn () => view('master'))->name('master.view');

// Form Login - HAPUS REGISTER DARI SINI karena sudah ada di protected
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');

Route::post('/login', [LoginController::class, 'login'])->name('login.custom');

// Landing Page Layanan IT
Route::get('/', [ReportPublicController::class, 'landing'])->name('report.public');

// Form Lapor Tanpa Login
Route::get('/layanan-it', [ReportPublicController::class, 'create'])->name('lapor.create');
Route::post('/lapor', [ReportPublicController::class, 'store'])->name('lapor.store');
Route::get('/lapor/{id}', [ReportPublicController::class, 'show'])->name('lapor.show');
Route::get('/track', [ReportPublicController::class, 'trackForm'])->name('report.track.form');
Route::get('/track/result', [ReportPublicController::class, 'trackResult'])->name('report.track');
Route::get('/cek-laporan', [App\Http\Controllers\ReportPublicController::class, 'showCheckForm'])->name('report.check');
Route::post('/cek-laporan', [App\Http\Controllers\ReportPublicController::class, 'processCheck']);
Route::post('/report/{id}/signature', [ReportPublicController::class, 'uploadSignature'])->name('report.uploadSignature');
Route::get('/cek-laporan', [App\Http\Controllers\ReportPublicController::class, 'showCheckForm'])->name('report.check');

// Tombol "Accept" hanya untuk user yang sudah login
Route::post('/lapor/{id}/accept', [ReportPublicController::class, 'accept'])
    ->middleware('auth')
    ->name('lapor.accept');

// =======================
// Pindahkan route cek-laporan ke public routes
Route::get('/cek-laporan', [App\Http\Controllers\ReportPublicController::class, 'showCheckForm'])->name('report.check');
Route::post('/cek-laporan', [App\Http\Controllers\ReportPublicController::class, 'processCheck']);

// ✅ PROTECTED (LOGIN + ROLE)
// =======================
Route::middleware(['auth'])->group(function () {
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
    Route::get('/reports/data', [ChartController::class, 'getReportsData'])->name('reports.data');
    Route::get('/chart/widgets', [ChartController::class, 'getWidgetsData'])->name('chart.widgets');

    // ✅ SUPERADMIN DASHBOARD
    Route::middleware(['role:superadmin'])->group(function () {
        Route::get('/dashboard-superadmin', fn () => view('dashboard.superadmin'))->name('dashboard.superadmin');
        
        // REGISTER - Hanya superadmin yang bisa akses
        Route::get('/register', [RegisterController::class, 'showRegisterForm'])->name('register');
        Route::post('/register', [RegisterController::class, 'customRegister'])->name('register.custom');
        Route::get('/chart/perangkat', [ChartController::class, 'index'])->name('chart.perangkat');
        Route::get('/reports/charts', [ChartController::class, 'getChartData'])->name('reports.charts');

        Route::post('/reports/comparison', [ChartController::class, 'getComparisonData'])->name('reports.comparison');
        Route::get('/reports/export', [ChartController::class, 'exportData'])->name('reports.export');
        Route::get('/reports/realtime-stats', [ChartController::class, 'getRealTimeStats'])->name('reports.realtime-stats');
        
        // Superadmin Routes
        Route::get('/superadmin', [SuperadminController::class, 'index'])->name('superadmin.dashboard');
        Route::get('/superadmin/reports', [SuperadminController::class, 'showReports'])->name('superadmin.reports');
        Route::get('/superadmin/report/{id}', [SuperadminController::class, 'showReport'])->name('superadmin.report.show');
        Route::post('/superadmin/report/{report}/signature', [SuperadminController::class, 'storeSignature'])->name('superadmin.signature.store');
        Route::get('/superadmin/report/{id}/surat-tugas', [SuperadminController::class, 'generateSuratTugas'])->name('superadmin.surat-tugas');
        
        // Rute untuk manajemen data
        Route::get('/superadmin/manage-data', [SuperadminController::class, 'manageData'])->name('superadmin.manage-data');
        
        // Rute untuk kategori
        Route::post('/superadmin/category', [SuperadminController::class, 'storeCategory'])->name('superadmin.category.store');
        Route::put('/superadmin/category/{id}', [SuperadminController::class, 'updateCategory'])->name('superadmin.category.update');
        Route::delete('/superadmin/category/{id}', [SuperadminController::class, 'deleteCategory'])->name('superadmin.category.delete');
        
        // Rute untuk cabang
        Route::post('/superadmin/branch', [SuperadminController::class, 'storeBranch'])->name('superadmin.branch.store');
        Route::put('/superadmin/branch/{id}', [SuperadminController::class, 'updateBranch'])->name('superadmin.branch.update');
        Route::delete('/superadmin/branch/{id}', [SuperadminController::class, 'deleteBranch'])->name('superadmin.branch.delete');
        
        // Test route for debugging
        Route::get('/test-chart', function() {
            try {
                return response()->json([
                    'message' => 'Chart routes are working',
                    'timestamp' => now(),
                    'test_data' => [
                        'locations' => \App\Models\Location::count(),
                        'items' => \App\Models\Item::count(),
                        'reports' => \App\Models\Report::count()
                    ],
                    'database_connection' => 'OK'
                ]);
            } catch (\Exception $e) {
                return response()->json([
                    'error' => 'Database connection failed',
                    'message' => $e->getMessage()
                ], 500);
            }
        });

        // User Management Routes
        Route::resource('user', UserController::class);
        Route::get('/superadmin/users', [UserController::class, 'index'])->name('superadmin.users');
        Route::get('/profile', [UserController::class, 'editProfile'])->name('user.profile');
        Route::put('/profile', [UserController::class, 'updateProfile'])->name('user.profile.update');
        
        // Phone tracking routes
        Route::get('/phone-tracking', [DashboardController::class, 'phoneTracking'])->name('phone.tracking');
        Route::get('/phone-tracking/{phone}', [DashboardController::class, 'phoneDetail'])->name('phone.detail');
    });

    // ✅ IT SUPPORT DASHBOARD
    Route::middleware(['role:it_supp'])->group(function () {
        Route::get('/dashboard-it', fn () => redirect()->route('it.reports'))->name('dashboard.it');
        
        // IT Support Charts
        Route::get('/chart/itsupport', [ChartController::class, 'itSupportCharts'])->name('chart.itsupport');
        Route::get('/itsupport/charts', [ChartController::class, 'getITSupportChartData'])->name('itsupport.charts');
        Route::get('/itsupport/export', [ChartController::class, 'exportData'])->name('itsupport.export');
        
        // Laporan IT Support
        Route::get('/reports', [ITSupportController::class, 'showReports'])->name('it_support.reports');
        Route::get('/reportss', [ITSupportController::class, 'showReports'])->name('it.reports'); // Alias untuk template baru
        Route::get('/report/{id}', [ReportController::class, 'show'])->name('it.show'); // Alias untuk template baru
        Route::post('/report/{id}/accept', [ITSupportController::class, 'accept'])->name('report.accept');
        Route::get('/report/{id}/surat', [ITSupportController::class, 'generateSuratTugas'])->name('report.surat');
        Route::post('/report/{id}/pindah-divisi', [ITSupportController::class, 'pindahDivisi'])->name('report.pindahDivisi');
        Route::get('/report/{id}/detail', [ReportController::class, 'detail'])->name('report.detail');
        Route::get('/report-detail/{id}/edit', [ReportDetailController::class, 'edit'])->name('report_detail.edit');
        Route::post('/signature', [SignatureController::class, 'store'])->name('signature.store');
        Route::put('/report-detail/{id}', [ReportDetailController::class, 'update'])->name('report_detail.update');
        Route::delete('/report/detail/{id}', [ReportDetailController::class, 'destroy'])->name('report_detail.destroy');
        Route::post('/report/{id}/next-day', [ITSupportController::class, 'nextDay'])
            ->name('report.nextDay');
        Route::get('/report/{id}/surat-pemeriksaan', [ITSupportController::class, 'generateSuratPemeriksaan'])->name('report.surat-pemeriksaan');
        Route::put('/report/{id}/inspection', [ITSupportController::class, 'updateInspectionData'])->name('report.update-inspection');
     
        // Routes untuk manajemen item dalam laporan
        Route::post('/report/{report_id}/items', [ITSupportController::class, 'addItemToReport'])->name('report.add-item');
        Route::put('/report/{report_id}/items/{detail_id}', [ITSupportController::class, 'updateReportItem'])->name('report.update-item');
        Route::delete('/report/{report_id}/items/{detail_id}', [ITSupportController::class, 'removeItemFromReport'])->name('report.remove-item');
    });

    // ✅ SHARED ROUTES - Bisa diakses SUPERADMIN dan IT_SUPP
    Route::middleware(['role:superadmin,it_supp'])->group(function () {
        
    });

    // ✅ LAPORAN UMUM (ADMIN & IT Support)
    Route::prefix('report')->name('report.')->group(function () {
        Route::get('/create', [ReportController::class, 'create'])->name('create');
        Route::post('/store', [ReportController::class, 'store'])->name('store');
        Route::get('/', [ReportController::class, 'index'])->name('index');
        Route::get('/{id}', [ReportController::class, 'show'])->name('show');

        // Tambah detail & update surat jalan
        Route::post('/{id}/add-detail', [ITSupportController::class, 'addDetail'])->name('addDetail');
        Route::post('/{id}/simpan-tanggal-surat', [ITSupportController::class, 'simpanTanggalSurat'])->name('simpanTanggalSurat');
        Route::post('/{id}/forward', [ITSupportController::class, 'forward'])->name('forward');
        Route::put('/{id}/update-surat-jalan', [ReportController::class, 'updateSuratJalanDate'])->name('updateSuratJalanDate');

        // Surat dan detail tindakan
        Route::get('/{id}/surat', [ITSupportController::class, 'generateSuratTugas'])->name('surat');
        Route::get('/detail/{detail_id}/edit', [ITSupportController::class, 'editDetail'])->name('detail.edit');
        Route::post('/detail/{detail_id}/update', [ITSupportController::class, 'updateDetail'])->name('detail.update');
        
    });

    // ✅ DASHBOARD
    Route::get('/dashboard', function () {
        $user = auth()->user();
        if ($user && ($user->role ?? null) === 'it_supp') {
            return redirect()->route('it.reports');
        }
        return app(\App\Http\Controllers\DashboardController::class)->index();
    })->name('dashboard');
    
    // ✅ PROFILE ROUTES
    Route::get('/profile', [UserController::class, 'editProfile'])->name('profile.edit');
    Route::put('/profile', [UserController::class, 'updateProfile'])->name('profile.update');
});

// =======================
// ✅ DEBUG & TES
// =======================
Route::middleware(['role:it_supp'])->get('/tes-role', function () {
    return 'Kamu berhasil masuk sebagai IT Support!';
});

// Route untuk IT Support
Route::middleware(['auth', 'role:it_supp'])->prefix('it')->name('it.')->group(function () {
    Route::get('/reports', [ITSupportController::class, 'showReports'])->name('it_support.reports');
    Route::get('/reportss', [ITSupportController::class, 'showReports'])->name('it.reports'); // Alias untuk template baru
    Route::get('/report/{id}', [ReportController::class, 'show'])->name('it.show'); // Alias untuk template baru
    Route::post('/report/{id}/accept', [ITSupportController::class, 'accept'])->name('report.accept');
    Route::get('/report/{id}/surat', [ITSupportController::class, 'generateSuratTugas'])->name('report.surat');
    Route::post('/report/{id}/pindah-divisi', [ITSupportController::class, 'pindahDivisi'])->name('report.pindahDivisi');
    Route::get('/report/{id}/detail', [ReportController::class, 'detail'])->name('report.detail');
    Route::get('/report-detail/{id}/edit', [ReportDetailController::class, 'edit'])->name('report_detail.edit');
    Route::post('/signature', [SignatureController::class, 'store'])->name('signature.store');
    Route::put('/report-detail/{id}', [ReportDetailController::class, 'update'])->name('report_detail.update');
    Route::delete('/report/detail/{id}', [ReportDetailController::class, 'destroy'])->name('report_detail.destroy');
    Route::post('/report/{id}/next-day', [ITSupportController::class, 'nextDay'])
        ->name('report.nextDay');
    Route::get('/chart/widgets', [ChartController::class, 'getWidgetsData'])->name('chart.widgets');
     
    // Routes untuk manajemen item dalam laporan
    Route::post('/report/{report_id}/items', [ITSupportController::class, 'addItemToReport'])->name('report.add-item');
    Route::put('/report/{report_id}/items/{detail_id}', [ITSupportController::class, 'updateReportItem'])->name('report.update-item');
    Route::delete('/report/{report_id}/items/{detail_id}', [ITSupportController::class, 'removeItemFromReport'])->name('report.remove-item');
    Route::post('/reports/{id}/done', [ITSupportController::class, 'markAsDone'])->name('reports.done');
});

// ✅ PROFILE ROUTES
Route::get('/profile', [UserController::class, 'editProfile'])->name('profile.edit');
Route::put('/profile', [UserController::class, 'updateProfile'])->name('profile.update');


// =======================
// ✅ DEBUG & TES
// =======================
Route::middleware(['role:it_supp'])->get('/tes-role', function () {
    return 'Kamu berhasil masuk sebagai IT Support!';
});
Route::get('/report/{id}/form-pemeriksaan', [ITSupportController::class, 'showFormPemeriksaan'])->name('report.form-pemeriksaan');
Route::put('/report/{id}/inspection', [ITSupportController::class, 'updateInspectionData'])->name('report.update-inspection');

// Route untuk superadmin
Route::middleware(['auth', 'role:superadmin'])->prefix('superadmin')->name('superadmin.')->group(function () {
    Route::get('/superadmin', [SuperadminController::class, 'index'])->name('superadmin.dashboard');
    Route::get('/superadmin/reports', [SuperadminController::class, 'showReports'])->name('superadmin.reports');
    Route::get('/superadmin/report/{id}', [SuperadminController::class, 'showReport'])->name('superadmin.report.show');
    Route::post('/superadmin/report/{report}/signature', [SuperadminController::class, 'storeSignature'])->name('superadmin.signature.store');
    Route::get('/superadmin/report/{id}/surat-tugas', [SuperadminController::class, 'generateSuratTugas'])->name('superadmin.surat-tugas');
    
    // Manajemen data (halaman gabungan - opsional)
    Route::get('/manage-data', [SuperadminController::class, 'manageData'])->name('manage.data');
    
    // Manajemen kategori (halaman terpisah)
    Route::get('/manage-categories', [SuperadminController::class, 'manageCategories'])->name('manage.categories');
    
    // Manajemen cabang (halaman terpisah)
    Route::get('/manage-branches', [SuperadminController::class, 'manageBranches'])->name('manage.branches');
    
    // Test route for debugging
    Route::get('/test-chart', function() {
        try {
            return response()->json([
                'message' => 'Chart routes are working',
                'timestamp' => now(),
                'test_data' => [
                    'locations' => \App\Models\Location::count(),
                    'items' => \App\Models\Item::count(),
                    'reports' => \App\Models\Report::count()
                ],
                'database_connection' => 'OK'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Database connection failed',
                'message' => $e->getMessage()
            ], 500);
        }
    });

    // User Management Routes
    Route::resource('user', UserController::class);
    Route::get('/superadmin/users', [UserController::class, 'index'])->name('superadmin.users');
    Route::get('/profile', [UserController::class, 'editProfile'])->name('user.profile');
    Route::put('/profile', [UserController::class, 'updateProfile'])->name('user.profile.update');
    
    // Phone tracking routes
    Route::get('/phone-tracking', [DashboardController::class, 'phoneTracking'])->name('phone.tracking');
    Route::get('/phone-tracking/{phone}', [DashboardController::class, 'phoneDetail'])->name('phone.detail');
});

// ✅ PROFILE ROUTES
Route::get('/profile', [UserController::class, 'editProfile'])->name('profile.edit');
Route::put('/profile', [UserController::class, 'updateProfile'])->name('profile.update');


// =======================
// ✅ DEBUG & TES
// =======================
Route::middleware(['role:it_supp'])->get('/tes-role', function () {
    return 'Kamu berhasil masuk sebagai IT Support!';
});
Route::put('/report/{id}/inspection', [ITSupportController::class, 'updateInspectionData'])->name('report.update-inspection');
Route::put('/report/{id}/inspection', [ITSupportController::class, 'updateInspectionData'])->name('report.update-inspection');
// Hapus rute duplikat di baris 140 (di dalam grup middleware role:it_supp)
// Route::get('/report/{id}', [ReportController::class, 'show'])->name('it.show'); // HAPUS INI

// Pastikan rute ini tetap ada di grup dengan prefix 'it' (sekitar baris 210):
Route::middleware(['auth', 'role:it_supp'])->prefix('it')->name('it.')->group(function () {
    Route::get('/reports', [ITSupportController::class, 'showReports'])->name('it_support.reports');
    Route::get('/reportss', [ITSupportController::class, 'showReports'])->name('it.reports'); // Alias untuk template baru
    Route::get('/report/{id}', [ReportController::class, 'show'])->name('show'); // Ubah dari 'it.show' menjadi 'show'
    Route::post('/report/{id}/accept', [ITSupportController::class, 'accept'])->name('report.accept');
    Route::get('/report/{id}/surat', [ITSupportController::class, 'generateSuratTugas'])->name('report.surat');
    Route::post('/report/{id}/pindah-divisi', [ITSupportController::class, 'pindahDivisi'])->name('report.pindahDivisi');
    Route::get('/report/{id}/detail', [ReportController::class, 'detail'])->name('report.detail');
    Route::get('/report-detail/{id}/edit', [ReportDetailController::class, 'edit'])->name('report_detail.edit');
    Route::post('/signature', [SignatureController::class, 'store'])->name('signature.store');
    Route::put('/report-detail/{id}', [ReportDetailController::class, 'update'])->name('report_detail.update');
    Route::delete('/report/detail/{id}', [ReportDetailController::class, 'destroy'])->name('report_detail.destroy');
    Route::post('/report/{id}/next-day', [ITSupportController::class, 'nextDay'])
        ->name('report.nextDay');
    Route::get('/chart/widgets', [ChartController::class, 'getWidgetsData'])->name('chart.widgets');
     
    // Routes untuk manajemen item dalam laporan
    Route::post('/report/{report_id}/items', [ITSupportController::class, 'addItemToReport'])->name('report.add-item');
    Route::put('/report/{report_id}/items/{detail_id}', [ITSupportController::class, 'updateReportItem'])->name('report.update-item');
    Route::delete('/report/{report_id}/items/{detail_id}', [ITSupportController::class, 'removeItemFromReport'])->name('report.remove-item');
    Route::post('/reports/{id}/done', [ITSupportController::class, 'markAsDone'])->name('reports.done');
});

// ✅ PROFILE ROUTES
Route::get('/profile', [UserController::class, 'editProfile'])->name('profile.edit');
Route::put('/profile', [UserController::class, 'updateProfile'])->name('profile.update');


// =======================
// ✅ DEBUG & TES
// =======================
Route::middleware(['role:it_supp'])->get('/tes-role', function () {
    return 'Kamu berhasil masuk sebagai IT Support!';
});
Route::put('/report/{id}/inspection', [ITSupportController::class, 'updateInspectionData'])->name('report.update-inspection');
Route::put('/report/{id}/inspection', [ITSupportController::class, 'updateInspectionData'])->name('report.update-inspection');
