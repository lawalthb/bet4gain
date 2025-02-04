<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GameController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BankAccountController;
use App\Http\Controllers\BetController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\LeaderboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SpinGameController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\WalletController;
use App\Models\Setting;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Artisan;

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
    // Route::post('/withdraw', [TransactionController::class, 'initiateWithdrawal']);
    Route::post('/bonus/{user}', [TransactionController::class, 'giveBonus'])->middleware('admin');

    Route::get('/wallet', [WalletController::class, 'show'])->name('wallet');
    Route::post('/deposit', [WalletController::class, 'deposit'])->name('deposit');
    Route::post('/withdraw', [WalletController::class, 'withdraw'])->name('withdraw');

    Route::get('/banks', [BankAccountController::class, 'getBanks']);
    Route::post('/bank-account', [BankAccountController::class, 'createTransferRecipient']);

    Route::get('player/profile', [ProfileController::class, 'show'])->name('player.profile');
    Route::post('player/profile/update', [ProfileController::class, 'update']);

    // Game routes
    Route::post('/bet', [GameController::class, 'placeBet'])->name('bet');
    Route::post('/cashout', [GameController::class, 'cashout'])->name('cashout');
});

// Public game data
Route::get('/game/history', [GameController::class, 'getHistory'])->name('game.history');
Route::get('/leaderboard', [GameController::class, 'leaderboard'])->name('leaderboard');

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


    Route::post('/spin/bet', [SpinGameController::class, 'placeBet']);
    Route::get('/spin/history', [SpinGameController::class, 'getHistory']);
});


Route::get('/spin', function () {
    return view('spin');
})->name('spin');




require __DIR__ . '/admin.php';


Route::get('/settings', function () {
    return response()->json([
        'pusher_key' => Setting::get('pusher_key'),
        'pusher_cluster' => Setting::get('pusher_cluster'),
        'pusher_email' => Setting::get('pusher_email')
    ]);
})->name('settings')->middleware('web');


Route::get('/broadcast-test', function () {
    broadcast(new \App\Events\TestEvent('Hello from Reverb!'));
    return "Event broadcasted";
});


Route::get('/test-event', function () {
    $game = App\Models\Game::latest()->first();
    broadcast(new App\Events\GameStarted($game))->toOthers();
    return "Event broadcasted";
});


Route::get('/reverb-test', function () {
    return view('reverb-test');
});



Route::get('/update-betonline', function () {
    Artisan::call('pusher:betonline');
    return "Pusher credentials updated to Betonline successfully";
});

Route::get('/update-aishat', function () {
    Artisan::call('pusher:aishat');
    return "Pusher credentials updated to aishat successfully";
});


Route::get('/update-lawal', function () {
    Artisan::call('pusher:lawal');
    return "Pusher credentials updated to lawal successfully";
});

Route::get('/update-debby', function () {
    Artisan::call('pusher:debby');
    return "Pusher credentials updated to debby successfully";
});

