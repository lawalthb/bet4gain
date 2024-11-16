<?php

namespace App\Http\Controllers;

use App\Models\SpinGame;
use App\Models\SpinBet;
use App\Events\SpinGameStarted;
use App\Events\SpinGameEnded;
use Illuminate\Http\Request;

class SpinGameController extends Controller
{
    public function start()
    {
        $game = SpinGame::create([
            'status' => 'active',
            'started_at' => now()
        ]);

        broadcast(new SpinGameStarted($game));

        return response()->json($game);
    }

    public function spin(SpinGame $game)
    {
        $segments = [
            ['color' => 'red', 'multiplier' => 2],
            ['color' => 'black', 'multiplier' => 2],
            ['color' => 'green', 'multiplier' => 14]
        ];

        $result = $segments[array_rand($segments)];

        $game->update([
            'result' => $result['color'],
            'multiplier' => $result['multiplier'],
            'status' => 'completed',
            'ended_at' => now()
        ]);

        broadcast(new SpinGameEnded($game));

        return response()->json($game);
    }

    public function placeBet(Request $request)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:1',
            'color' => 'required|in:red,black,green',
            'game_id' => 'required|exists:spin_games,id'
        ]);

        $bet = SpinBet::create([
            'user_id' => auth()->id(),
            'game_id' => $validated['game_id'],
            'amount' => $validated['amount'],
            'color' => $validated['color']
        ]);

        return response()->json($bet);
    }
}
