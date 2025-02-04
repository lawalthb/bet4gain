<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\GameSettingsController;
use App\Http\Controllers\Admin\TransactionController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\UserController;

// Admin Auth Routes
Route::get('/admin/login', [AuthController::class, 'showLoginForm'])->name('admin.login');
Route::post('/admin/login', [AuthController::class, 'login']);
Route::post('/admin/logout', [AuthController::class, 'logout'])->name('admin.logout');
Route::get('/admin/otp', [AuthController::class, 'showOtpForm'])->name('admin.otp');
Route::post('/admin/otp/verify', [AuthController::class, 'verifyOtp'])->name('admin.otp.verify');

Route::middleware(['auth:admin'])->group(function () {
    // Dashboard
    Route::get('/admin/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');

    // Users Management
    Route::resource('users', UserController::class)->names('admin.users');
    Route::post('/users/{user}/ban', [UserController::class, 'ban'])->name('admin.users.ban');
    Route::get('/users/{user}/transactions', [UserController::class, 'transactions'])->name('admin.users.transactions');

    // Admin Management
    Route::resource('admins', AdminController::class)->names('admin.admins');
    Route::post('/admins/{admin}/role', [AdminController::class, 'assignRole'])->name('admin.admins.role');

    // Game Settings
    Route::get('/games/crash', [GameSettingsController::class, 'crashSettings'])->name('admin.games.crash');
    Route::post('/games/crash', [GameSettingsController::class, 'updateCrashGame'])->name('admin.games.crash.update');
    Route::get('/games/spin', [GameSettingsController::class, 'spinSettings'])->name('admin.games.spin');
    Route::post('/games/spin', [GameSettingsController::class, 'updateSpinWheel'])->name('admin.games.spin.update');

    // Transactions
    Route::get('/transactions', [TransactionController::class, 'index'])->name('admin.transactions');
    Route::get('/transactions/pending', [TransactionController::class, 'pending'])->name('admin.transactions.pending');
    Route::post('/transactions/{transaction}/approve', [TransactionController::class, 'approve'])->name('admin.transactions.approve');
    Route::post('/transactions/{transaction}/reject', [TransactionController::class, 'reject'])->name('admin.transactions.reject');

    // Settings
    Route::get('/settings', [GameSettingsController::class, 'index'])->name('admin.settings');
    Route::post('/settings', [GameSettingsController::class, 'update'])->name('admin.settings.update');

    Route::get('/profile', [AdminController::class, 'profile'])->name('admin.profile');
    Route::put('/profile/update', [AdminController::class, 'updateProfile'])->name('admin.profile.update');
    Route::put('/profile/password', [AdminController::class, 'updatePassword'])->name('admin.profile.password');


    Route::get('/settings/pusher', [GameSettingsController::class, 'pusherSettings'])->name('admin.settings.pusher');
    Route::post('/settings/pusher', [GameSettingsController::class, 'updatePusherSettings'])->name('admin.settings.pusher.update');

      Route::get('/admin/settings', [GameSettingsController::class, 'index'])->name('admin.settings');

});


