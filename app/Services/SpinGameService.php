<?php

namespace App\Services;

use App\Models\SpinGame;
use App\Models\SpinBet;
use App\Events\SpinGameStarted;
use App\Events\GameSpinResult;

class SpinGameService
{
    private $colors = [
        'green' => 20,
        'teal' => 3,
        'blue' => 8,
        'violet' => 4,
        'purple' => 10,
        'magenta' => 2,
        'red' => 3,
        'darkorange' => 5,
        'orange' => 14,
        'goldenrod' => 7,
        'yellow' => 2,
        'chartreuse' => 2,
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
