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
        Log::info('GameService initialized');
    }

    public function startNewGame()
    {
        try {
            $game = Game::create([
                'started_at' => now(),
                'crash_point' => $this->generateCrashPoint(),
                'is_completed' => false
            ]);

            Log::info('New game started', [
                'game_id' => $game->id,
                'crash_point' => $game->crash_point,
                'started_at' => $game->started_at
            ]);

            $this->pusher->trigger('game', 'GameStarted', [
                'game' => $game
            ]);

            return $game;
        } catch (\Exception $e) {
            Log::error('Error starting new game', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    public function updateGameState($game)
    {
        try {
            $elapsedTime = abs(now()->diffInMilliseconds($game->started_at) / 1000);
            $currentMultiplier = $this->calculateMultiplier($elapsedTime);

            Log::debug('Game state update', [
                'game_id' => $game->id,
                'elapsed_time' => $elapsedTime,
                'current_multiplier' => $currentMultiplier,
                'crash_point' => $game->crash_point
            ]);

            $this->pusher->trigger('game', 'GameUpdated', [
                'multiplier' => $currentMultiplier,
                'elapsed_time' => $elapsedTime
            ]);

            if ($currentMultiplier >= $game->crash_point) {
                $this->crashGame($game);
            }
        } catch (\Exception $e) {
            Log::error('Error updating game state', [
                'game_id' => $game->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    private function calculateMultiplier($elapsedTime)
    {
        $multiplier = pow(1.0678, $elapsedTime * 6);
        Log::debug('Multiplier calculated', [
            'elapsed_time' => $elapsedTime,
            'multiplier' => $multiplier
        ]);
        return $multiplier;
    }

    private function crashGame($game)
    {
        try {
            $game->update(['is_completed' => true]);

            Log::info('Game crashed', [
                'game_id' => $game->id,
                'final_crash_point' => $game->crash_point,
                'total_duration' => now()->diffInSeconds($game->started_at)
            ]);

            $this->pusher->trigger('game', 'GameCrashed', [
                'crash_point' => $game->crash_point
            ]);
        } catch (\Exception $e) {
            Log::error('Error crashing game', [
                'game_id' => $game->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    private function generateCrashPoint()
    {
        $crashPoint = mt_rand(100, 1000) / 100;
        Log::debug('Crash point generated', ['crash_point' => $crashPoint]);
        return $crashPoint;
    }
}
