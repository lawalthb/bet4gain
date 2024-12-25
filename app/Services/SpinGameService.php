<?php

namespace App\Services;

use App\Models\SpinGame;
use App\Models\SpinBet;
use App\Events\SpinGameStarted;
use App\Events\GameSpinResult;

class SpinGameService
{
    private $colors = [
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

    public function startNewGame()
    {
        $game = SpinGame::create([
            'started_at' => now(),
            'is_completed' => false
        ]);

        event(new SpinGameStarted($game));

        return $game;
    }

    public function determineResult(SpinGame $game)
    {
        $result = array_rand($this->colors);
        $multiplier = $this->colors[$result];

        $game->update([
            'result_color' => $result,
            'multiplier' => $multiplier,
            'is_completed' => true
        ]);

        $this->processBets($game);
        event(new GameSpinResult($result));

        return $result;
    }

    private function processBets(SpinGame $game)
    {
        $bets = SpinBet::where('spin_game_id', $game->id)
                       ->where('status', 'pending')
                       ->get();

        foreach ($bets as $bet) {
            if ($bet->color === $game->result_color) {
                $profit = $bet->amount * $this->colors[$game->result_color];
                $bet->user->increment('wallet_balance', $profit);
                $bet->update([
                    'profit' => $profit - $bet->amount,
                    'status' => 'won'
                ]);
            } else {
                $bet->update([
                    'profit' => -$bet->amount,
                    'status' => 'lost'
                ]);
            }
        }
    }
}
