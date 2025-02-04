<?php

namespace App\Http\Controllers;

use App\Models\SpinGame;
use App\Models\SpinBet;
use App\Events\SpinGameStarted;
use App\Events\GameSpinResult;
use Illuminate\Http\Request;

class SpinGameController extends Controller
{

    private $wheelSegments = [
    0 => 'red',
    1 => 'black',
    2 => 'green',
    3 => 'yellow',
    4 => 'blue',
    5 => 'purple',
    6 => 'orange',
    7 => 'pink',
    8 => 'cyan',
    9 => 'brown',
    10 => 'magenta',
    11 => 'lime'
];


    public function placeBet(Request $request)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:1',
            'color' => 'required|string'
        ]);

        $user = auth()->user();

        if ($user->wallet_balance < $validated['amount']) {
            return response()->json(['error' => 'Insufficient balance'], 422);
        }

        $currentGame = SpinGame::where('is_completed', false)->first();

        if (!$currentGame) {
            return response()->json(['error' => 'No active game'], 422);
        }

        // Get multiplier based on color
        $multipliers = [
            'green' => 20,
            'teal' => 3,
            'blue' => 14,
            'violet' => 4,
            'purple' => 10,
            'magenta' => 2,
            'red' => 3,
            'vermilion' => 5,
            'orange' => 14,
            'goldenrod' => 7,
            'yellow' => 2,
            'chartreuse' => 2
        ];

        $bet = SpinBet::create([
            'user_id' => $user->id,
            'spin_game_id' => $currentGame->id,
            'amount' => $validated['amount'],
            'color' => $validated['color'],
            'multiplier' => $multipliers[$validated['color']],
            'status' => 'pending'
        ]);

        $user->decrement('wallet_balance', $validated['amount']);

        return response()->json([
            'success' => true,
            'bet_id' => $bet->id,
            'balance' => $user->wallet_balance
        ]);
    }

    public function getHistory()
    {
        $history = SpinGame::with(['bets' => function ($query) {
            $query->where('user_id', auth()->id());
        }])
            ->where('is_completed', true)
            ->latest()
            ->paginate(10);

        return response()->json($history);
    }
}
