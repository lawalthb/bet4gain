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

    public function processBetResults($gameId, $crashPoint)
    {
        $bets = Bet::where('game_id', $gameId)
            ->where('status', 'pending')
            ->get();

        foreach ($bets as $bet) {
            if ($bet->cashout_multiplier && $bet->cashout_multiplier <= $crashPoint) {
                // Win
                $winAmount = $bet->amount * $bet->cashout_multiplier;
                $profit = $winAmount - $bet->amount;

                $bet->update([
                    'status' => 'won',
                    'profit' => $profit
                ]);

                if (!$bet->is_demo) {
                    $bet->user->increment('wallet_balance', $winAmount);
                }
            } else {
                // Loss
                $bet->update([
                    'status' => 'lost',
                    'profit' => -$bet->amount
                ]);
            }
            // event(new GameHistoryUpdated($this->getHistory()));
            // event(new LeaderboardUpdated($this->getLeaders('daily')));

        }

        return [
            'total_bets' => $bets->count(),
            'winners' => $bets->where('status', 'won')->count(),
            'total_profit' => $bets->sum('profit')
        ];
    }

    public function handleGameCrash(Request $request)
    {
        $crashPoint = $request->crash_point;
        $gameId = ($request->game_id - 1);
        // Update all pending bets for this game to lost status
        $pendingBets = Bet::where('game_id', $gameId)
            ->where('status', 'pending')
            ->get();

        foreach ($pendingBets as $bet) {
            $bet->update([
                'status' => 'lost',
                'profit' => -$bet->amount
            ]);
        }

        // Return updated balances for all affected users
        return response()->json([
            'success' => true,
            'message' => 'Bets updated successfully'
        ]);
    }



    public function getHistory()
    {
        $history = Game::with(['bets' => function ($query) {
            $query->where('user_id', auth()->id());
        }])
        ->latest()
        ->paginate(10)
        ->through(function ($game) {
            $bet = $game->bets->first();
            return [
                'id' => $game->id,
                'created_at' => $game->created_at,
                'crash_point' => $game->crash_point,
                'bet_amount' => $bet ? $bet->amount : 0,
                'profit' => $bet ? $bet->profit : 0,
                'status' => $bet ? $bet->status : 'no_bet'
            ];
        });

        return response()->json($history);
    }
}
