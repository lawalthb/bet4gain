<?php

use App\Http\Controllers\GameController;
use App\Models\Setting;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/bets', [GameController::class, 'placeBet']);
    Route::post('/cashout', [GameController::class, 'cashOut']);
});

Route::get('/game-history', [GameController::class, 'history']);



