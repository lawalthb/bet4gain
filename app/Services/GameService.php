<?php

namespace App\Services;

use App\Events\GameStarted;
use App\Events\GameUpdated;
use App\Events\GameCrashed;
use App\Models\Game;

class GameService
{
    public function startNewGame()
    {
        $game = Game::create([
            'started_at' => now(),
            'crash_point' => $this->generateCrashPoint(),
        ]);

        broadcast(new GameStarted($game));

        return $game;
    }

    public function updateGameState(Game $game)
    {
        $elapsedTime = now()->diffInMilliseconds($game->started_at) / 1000;
        $currentMultiplier = $this->calculateMultiplier($elapsedTime);

        if ($currentMultiplier >= $game->crash_point) {
            $this->crashGame($game);
            return;
        }

        broadcast(new GameUpdated([
            'multiplier' => $currentMultiplier,
            'elapsed_time' => $elapsedTime
        ]));
    }

    private function generateCrashPoint()
    {
        $random = mt_rand() / mt_getrandmax();
        return 0.99 / (1 - $random);
    }

    private function calculateMultiplier($elapsedTime)
    {
        return pow(1.0678, $elapsedTime);
    }

    private function crashGame(Game $game)
    {
        $game->update([
            'ended_at' => now(),
            'is_completed' => true
        ]);

        broadcast(new GameCrashed($game));
    }
}
