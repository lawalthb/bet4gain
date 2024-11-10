<?php

namespace App\Http\Controllers;

use App\Models\Game;
use App\Models\Bet;
use App\Events\GameUpdate;
use Illuminate\Http\Request;

class GameController extends Controller
{
    private $minCrashPoint = 1.00;
    private $maxCrashPoint = 100.00;

    public function startNewGame()
    {
        $crashPoint = $this->generateCrashPoint();

        $game = Game::create([
            'crash_point' => $crashPoint,
            'started_at' => now(),
        ]);

        broadcast(new GameUpdate($game))->toOthers();

        return response()->json($game);
    }

    private function generateCrashPoint()
    {
        // Algorithm to generate crash point
        $random = mt_rand() / mt_getrandmax();
        return max($this->minCrashPoint, 0.99 / $random);
    }

    public function placeBet(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1'
        ]);

        $bet = Bet::create([
            'user_id' => auth()->id(),
            'game_id' => Game::current()->id,
            'amount' => $request->amount
        ]);

        return response()->json($bet);
    }
}
