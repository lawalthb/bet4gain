<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GameController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BetController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\LeaderboardController;
use App\Http\Controllers\SpinGameController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\WalletController;
use Illuminate\Support\Facades\Auth;
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

    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::middleware(['auth', 'verified'])->group(function () {
        Route::post('/deposit', [TransactionController::class, 'initiateDeposit']);
        Route::post('/withdraw', [TransactionController::class, 'initiateWithdrawal']);
        Route::post('/bonus/{user}', [TransactionController::class, 'giveBonus'])->middleware('admin');

        Route::get('/wallet', [WalletController::class, 'show'])->name('wallet');
        Route::post('/deposit', [WalletController::class, 'deposit'])->name('deposit');
        Route::post('/withdraw', [WalletController::class, 'initiatewithdraw'])->name('withdraw');


    // Game routes
    Route::post('/bet', [GameController::class, 'placeBet'])->name('bet');
    Route::post('/cashout', [GameController::class, 'cashout'])->name('cashout');
});

// Public game data
Route::get('/game/history', [GameController::class, 'getHistory'])->name('game.history');Route::get('/leaderboard', [GameController::class, 'leaderboard'])->name('leaderboard');

// WebSocket channels
Broadcast::channel('game', function ($user) {
    return ['id' => $user->id, 'name' => $user->name];
});

Auth::routes(['verify' => true]);

Route::post('/payment/webhook', [TransactionController::class, 'handleWebhook'])->name('payment.webhook');
Route::get('/payment/callback', [TransactionController::class, 'handleCallback'])->name('transaction.callback');

Route::get('/transactions', [TransactionController::class, 'getTransactions'])->middleware('auth');



Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::get('/user/balance', [TransactionController::class, 'getUserBalance'])->middleware('auth');

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::post('/chat/send', [ChatController::class, 'sendMessage'])->middleware('auth');

Route::get('/chat/messages', [ChatController::class, 'getMessages']);

// Betting Routes
Route::middleware(['auth'])->group(function () {
    Route::post('/bet/place', [BetController::class, 'placeBet'])->name('bet.place');
    Route::post('/bet/cashout', [BetController::class, 'cashout'])->name('bet.cashout');
    Route::get('/bet/history', [BetController::class, 'getBetHistory'])->name('bet.history');
});

// Public Game Routes
Route::get('/game/{gameId}/bets', [BetController::class, 'getCurrentGameBets'])->name('game.bets');
Route::get('/game/{gameId}/stats', [BetController::class, 'getGameStats'])->name('game.stats');

// Demo/Guest Routes (no auth required)
Route::post('/bet/demo', [BetController::class, 'placeBet'])->name('bet.demo');

Route::get('/leaderboard/{timeFrame}', [LeaderboardController::class, 'getLeaders']);


Route::post('/game/crash', [GameController::class, 'handleGameCrash'])->name('game.crash');


Route::middleware(['auth'])->group(function () {
    Route::post('/spin/start', [SpinGameController::class, 'start']);
    Route::post('/spin/bet', [SpinGameController::class, 'placeBet']);
    Route::post('/spin/{game}/spin', [SpinGameController::class, 'spin']);
});


Route::get('/spin', function () {
    return view('spin');
})->name('spin');
