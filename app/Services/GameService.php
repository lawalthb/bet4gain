<?php

namespace App\Services;

use App\Events\GameStarted;
use App\Events\GameUpdated;
use App\Events\GameCrashed;
use App\Models\Game;
use Illuminate\Support\Facades\Log;
use Pusher\Pusher;

class GameService
{
    private $pusher;

    public function __construct()
    {
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

    public function startNewGame()
    {
        $game = Game::create([
            'started_at' => now(),
            'crash_point' => $this->generateCrashPoint(),
            'is_completed' => false
        ]);

        $this->pusher->trigger('game', 'GameStarted', [
            'game' => $game
        ]);

        Log::info('Game Started and Broadcasted', [
            'game_id' => $game->id,
            'crash_point' => $game->crash_point
        ]);

        return $game;
    }

    public function updateGameState($game)
    {
        $elapsedTime = now()->diffInMilliseconds($game->started_at) / 1000;
        $currentMultiplier = $this->calculateMultiplier($elapsedTime);

        $this->pusher->trigger('game', 'GameUpdated', [
            'multiplier' => $currentMultiplier,
            'elapsed_time' => $elapsedTime
        ]);

        Log::info('Game State Updated and Broadcasted', [
            'game_id' => $game->id,
            'multiplier' => $currentMultiplier
        ]);

        if ($currentMultiplier >= $game->crash_point) {
            $this->crashGame($game);
        }
    }

    private function crashGame($game)
    {
        $game->update(['is_completed' => true]);

        $this->pusher->trigger('game', 'GameCrashed', [
            'game' => $game
        ]);

        Log::info('Game Crashed and Broadcasted', [
            'game_id' => $game->id,
            'final_multiplier' => $game->crash_point
        ]);
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
}
