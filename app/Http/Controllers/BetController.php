<?php

namespace App\Http\Controllers;

use App\Http\Requests\PlaceBetRequest;
use App\Services\BettingService;
use App\Models\Bet;
use App\Models\Game;
use Illuminate\Http\Request;
use Pusher\Pusher;

class BetController extends Controller
{
    protected $bettingService;
    private $pusher;
    public function __construct(BettingService $bettingService)
    {
        $this->bettingService = $bettingService;

        $this->pusher = new Pusher(
            '87892ed076b91483ee2a',
            '1043bfa797b5c0b09de5',
            '1769030',
            [
                'cluster' => 'mt1',
                'useTLS' => true
            ]
        );

        
    }

    public function placeBet(PlaceBetRequest $request)
    {
        try {
            $bet = $this->bettingService->placeBet(
                $request->validated(),
                auth()->user()
            );

            return response()->json([
                'success' => true,
                'bet' => $bet,
                'wallet_balance' => auth()->user()->wallet_balance
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 422);
        }
    }

    public function cashout(Request $request)
    {
        $request->validate([
            'bet_id' => 'required|exists:bets,id',
            'crash_point' => 'required|numeric|min:1.1'
        ]);

        try {
            $bet = Bet::findOrFail($request->bet_id);
            $winAmount = $this->bettingService->processCashout($bet, $request->crash_point);


            $this->pusher->trigger('game', 'LeaderboardUpdated', []);

            $this->pusher->trigger('game', 'GameHistoryUpdated', []);


            return response()->json([
                'success' => true,
                'win_amount' => $winAmount,
                'wallet_balance' => auth()->user()->wallet_balance
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 422);
        }
    }

    public function getBetHistory(Request $request)
    {
        $user = auth()->user();

        $bets = Bet::with('game')
            ->where('user_id', $user->id)
            ->latest()
            ->paginate(10);

        return response()->json($bets);
    }

    public function getCurrentGameBets($gameId)
    {
        $bets = Bet::with('user')
            ->where('game_id', $gameId)
            ->get()
            ->map(function ($bet) {
                return [
                    'id' => $bet->id,
                    'username' => $bet->is_bot ? $bet->bot_name : ($bet->user->username ?? 'Anonymous'),
                    'amount' => $bet->amount,
                    'auto_cashout' => $bet->auto_cashout,
                    'status' => $bet->status,
                    'profit' => $bet->profit,
                    'cashout_multiplier' => $bet->cashout_multiplier
                ];
            });

        return response()->json($bets);
    }

    public function getGameStats($gameId)
    {
        $game = Game::findOrFail($gameId);

        $stats = [
            'total_bets' => $game->bets->count(),
            'total_amount' => $game->bets->sum('amount'),
            'total_winners' => $game->bets->where('status', 'won')->count(),
            'total_profit' => $game->bets->sum('profit'),
            'highest_multiplier' => $game->bets->max('cashout_multiplier'),
            'crash_point' => $game->crash_point
        ];

        return response()->json($stats);
    }
}
