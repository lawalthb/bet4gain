<?php

namespace App\Http\Controllers;

use App\Models\SpinGame;
use App\Models\SpinBet;
use App\Events\SpinGameStarted;
use App\Events\GameSpinResult;
use Illuminate\Http\Request;

class SpinGameController extends Controller
{
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
            'red' => 2,
            'black' => 2,
            'green' => 14,
            'yellow' => 3,
            'blue' => 5,
            'purple' => 2,
            'orange' => 2,
            'pink' => 7,
            'cyan' => 2,
            'brown' => 2,
            'magenta' => 9,
            'lime' => 2
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
