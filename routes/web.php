<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GameController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\WalletController;
use Illuminate\Support\Facades\Broadcast;

Route::get('/', function () {
    return view('game');
});

// Guest routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

// Authenticated routes
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Wallet routes
    Route::get('/wallet', [WalletController::class, 'show'])->name('wallet');
    Route::post('/deposit', [WalletController::class, 'deposit'])->name('deposit');
    Route::post('/withdraw', [WalletController::class, 'withdraw'])->name('withdraw');

    // Game routes
    Route::post('/bet', [GameController::class, 'placeBet'])->name('bet');
    Route::post('/cashout', [GameController::class, 'cashout'])->name('cashout');
});

// Public game data
Route::get('/game-history', [GameController::class, 'history'])->name('game.history');
Route::get('/leaderboard', [GameController::class, 'leaderboard'])->name('leaderboard');

// WebSocket channels
Broadcast::channel('game', function ($user) {
    return ['id' => $user->id, 'name' => $user->name];
});

Route::get('/test-event', function () {
    broadcast(new \App\Events\TestEvent('Hello from Reverb!'));
    return 'Event sent!';
});


Route::get('/gamess', function () {
    broadcast(new \App\Events\GameStarted('Hello from Reverb!'));
    return 'Event sent from reverb!';
});

// In routes/web.php
Route::get('/test-broadcast', function () {
    $game = \App\Models\Game::create([
        'started_at' => now(),
        'crash_point' => 2.0,
        'is_completed' => false
    ]);

    event(new \App\Events\GameStarted($game));

    return "Test broadcast sent!";
});

