<?php

namespace App\Services;

use App\Events\GameStarted;
use App\Events\GameUpdated;
use App\Events\GameCrashed;
use App\Models\Game;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Event;
use Exception;
use Pusher\Pusher;

class GameService
{
    private $pusher;
    public function __construct()
    {


        $this->pusher = new Pusher(
            "87892ed076b91483ee2a",
            "1043bfa797b5c0b09de5",
            "1769030",
            [
                'cluster' => 'mt1',
                'useTLS' => true
            ]
        );
    }
    public function startNewGame()
    {
        try {
            $game = Game::create([
                'started_at' => now(),
                'crash_point' => $this->generateCrashPoint(),
                'is_completed' => false
            ]);

            Log::info('Attempting to broadcast GameStarted event', [
                'game_id' => $game->id
            ]);

            $event = new GameStarted($game);
            event($event);

            Log::info('Game Started event dispatched', [
                'game_id' => $game->id,
                'crash_point' => $game->crash_point,
                'channel' => 'game',
                'event_name' => 'game.started'
            ]);

            return $game;
        } catch (\Exception $e) {
            Log::error('Failed to start game or broadcast event', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }


    public function updateGameState($game)
    {
        try {
            $elapsedTime = now()->diffInMilliseconds($game->started_at) / 1000;
            $currentMultiplier = $this->calculateMultiplier($elapsedTime);

            $gameState = [
                'multiplier' => $currentMultiplier,
                'elapsed_time' => $elapsedTime,
                'game_id' => $game->id
            ];

            event(new GameUpdated($gameState));

            Log::info('Game State Updated and Broadcasted', [
                'game_id' => $game->id,
                'multiplier' => $currentMultiplier
            ]);

            if ($currentMultiplier >= $game->crash_point) {
                $this->crashGame($game);
            }
        } catch (Exception $e) {
            Log::error('Failed to update game state', [
                'game_id' => $game->id,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    private function crashGame($game)
    {
        try {
            $game->update(['is_completed' => true]);

            event(new GameCrashed($game));

            Log::info('Game Crashed and Broadcasted', [
                'game_id' => $game->id,
                'final_multiplier' => $game->crash_point
            ]);
        } catch (Exception $e) {
            Log::error('Failed to crash game', [
                'game_id' => $game->id,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
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
