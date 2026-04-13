<?php

use App\Http\Controllers\Admin\ActivityLogController;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\TicketController as AdminTicketController;
use App\Http\Controllers\User\HomeController;
use App\Http\Controllers\User\TicketController;
use App\Http\Controllers\User\TicketTrackController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// ──────────────────────────────────────────────
//  PUBLIC — User Routes
// ──────────────────────────────────────────────

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/home', [HomeController::class, 'index'])->name('user.home');

Route::prefix('tickets')->name('user.tickets.')->group(function () {
    Route::get('/create', [TicketController::class, 'create'])->name('create');
    Route::post('/store', [TicketController::class, 'store'])->name('store');
    Route::get('/success/{ticket_code}', [TicketController::class, 'success'])->name('success');
    Route::get('/track', [TicketTrackController::class, 'showTrackForm'])->name('track.form');
    Route::post('/track', [TicketTrackController::class, 'track'])->name('track');
    Route::post('/track/search', [TicketTrackController::class, 'trackAjax'])->name('track.do');
    Route::get('/track/{ticket_code}', [TicketTrackController::class, 'showTrackResult'])->name('track.result');
    Route::get('/view/{ticket_code}', [TicketTrackController::class, 'showTrackResult'])->name('show');
});

Route::prefix('pages')->name('pages.')->group(function () {
    Route::get('/about', fn () => view('pages.about'))->name('about');
    Route::get('/contact', fn () => view('pages.contact'))->name('contact');
    Route::get('/faq', fn () => view('pages.faq'))->name('faq');
    Route::get('/privacy', fn () => view('pages.privacy'))->name('privacy');
});

// ──────────────────────────────────────────────
//  ADMIN Routes (DENGAN SESSION TIMEOUT)
// ──────────────────────────────────────────────

Route::prefix('admin')->name('admin.')->middleware(['session.timeout'])->group(function () {

    // Guest only
    Route::middleware('guest')->group(function () {
        Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
        Route::post('/login', [AuthController::class, 'login'])->name('login.post');
    });

    // Auth + Admin role (minimal admin)
    Route::middleware(['auth', 'admin.role:admin,super_admin'])->group(function () {

        Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
        Route::get('/', fn () => redirect()->route('admin.dashboard'));
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        // Tiket (bisa diakses semua admin)
        Route::prefix('tickets')->name('tickets.')->group(function () {
            Route::get('/', [AdminTicketController::class, 'index'])->name('index');
            Route::get('/search', [AdminTicketController::class, 'search'])->name('search');
            Route::get('/{id}', [AdminTicketController::class, 'show'])->name('show');
            Route::patch('/{id}/status', [AdminTicketController::class, 'updateStatus'])->name('update-status');
            Route::post('/{id}/reply', [AdminTicketController::class, 'reply'])->name('reply');
            Route::delete('/{id}', [AdminTicketController::class, 'destroy'])->name('destroy');
        });

        // Laporan (bisa diakses semua admin)
        Route::prefix('reports')->name('reports.')->group(function () {
            Route::get('/', [ReportController::class, 'index'])->name('index');
            Route::get('/export', [ReportController::class, 'export'])->name('export');
        });

        // =============================================================
        // MANAJEMEN ADMIN (HANYA SUPER ADMIN)
        // =============================================================
        Route::middleware(['auth', 'admin.role:super_admin'])->prefix('manajemen')->name('manajemen.')->group(function () {
            Route::get('/', [App\Http\Controllers\Admin\AdminController::class, 'index'])->name('index');
            Route::get('/create', [App\Http\Controllers\Admin\AdminController::class, 'create'])->name('create');
            Route::post('/', [App\Http\Controllers\Admin\AdminController::class, 'store'])->name('store');
            Route::get('/{id}', [App\Http\Controllers\Admin\AdminController::class, 'show'])->name('show');
            Route::get('/{id}/edit', [App\Http\Controllers\Admin\AdminController::class, 'edit'])->name('edit');
            Route::put('/{id}', [App\Http\Controllers\Admin\AdminController::class, 'update'])->name('update');
            Route::delete('/{id}', [App\Http\Controllers\Admin\AdminController::class, 'destroy'])->name('destroy');
            Route::post('/{id}/regenerate-avatar', [App\Http\Controllers\Admin\AdminController::class, 'regenerateAvatar'])->name('regenerate-avatar');
        });


        // Profile routes
        Route::get('/profile', [App\Http\Controllers\Admin\ProfileController::class, 'index'])->name('profile');
        Route::get('/profile/edit', [App\Http\Controllers\Admin\ProfileController::class, 'edit'])->name('profile.edit');
        Route::put('/profile/name', [App\Http\Controllers\Admin\ProfileController::class, 'updateName'])->name('profile.update-name');
        Route::post('/profile/email-change', [App\Http\Controllers\Admin\ProfileController::class, 'requestEmailChange'])->name('profile.email-change');
        Route::get('/profile/confirm-email-change/{token}', [App\Http\Controllers\Admin\ProfileController::class, 'confirmEmailChange'])->name('profile.confirm-email-change');
        Route::get('/profile/password', [App\Http\Controllers\Admin\ProfileController::class, 'passwordForm'])->name('profile.password.form');
        Route::post('/profile/password-reset', [App\Http\Controllers\Admin\ProfileController::class, 'requestPasswordReset'])->name('profile.password-reset');
        Route::get('/profile/confirm-password-reset/{token}', [App\Http\Controllers\Admin\ProfileController::class, 'confirmPasswordReset'])->name('profile.confirm-password-reset');

        // Pengaturan (hanya super admin)
        Route::middleware(['admin.role:super_admin'])->prefix('settings')->name('settings.')->group(function () {
            Route::get('/', [SettingController::class, 'index'])->name('index');
            Route::post('/', [SettingController::class, 'update'])->name('update');
        });
    });
});

// ──────────────────────────────────────────────
//  404 Fallback
// ──────────────────────────────────────────────
Route::fallback(fn () => response()->view('errors.404', [], 404));
