<?php

namespace App\Services;

use App\Models\Game as ModelsGame;
use App\Services\Game;
use App\Models\GameSetting;
use Illuminate\Support\Facades\Log;
use Pusher\Pusher;
use App\Models\Setting;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Support\Facades\DB;

use Pusher\ApiErrorException;

class GameService
{


    private $segments = [
        ['color' => 'red', 'multiplier' => 2],
        ['color' => 'black', 'multiplier' => 2],
        ['color' => 'green', 'multiplier' => 14],
        ['color' => 'yellow', 'multiplier' => 3],
        ['color' => 'blue', 'multiplier' => 5],
        ['color' => 'purple', 'multiplier' => 2],
        ['color' => 'orange', 'multiplier' => 2],
        ['color' => 'pink', 'multiplier' => 7],
        ['color' => 'cyan', 'multiplier' => 2],
        ['color' => 'brown', 'multiplier' => 2],
        ['color' => 'magenta', 'multiplier' => 9],
        ['color' => 'lime', 'multiplier' => 2]
    ];

    private $pusher;

    public function __construct()
    {
        $this->pusher = new Pusher(
            Setting::get('pusher_key'),
            Setting::get('pusher_secret'),
            Setting::get('pusher_app_id'),
            [
                'cluster' => Setting::get('pusher_cluster'),
                'useTLS' => true
            ]
        );
        Log::info('GameService initialized');
    }

    private function handlePusherQuotaExceeded() {
        // Get current active set
        $currentSet = Setting::get('active_pusher_set', 1);

        // Calculate next set (1-4)
        $nextSet = ($currentSet % 4) + 1;

        // Update Pusher credentials
        Setting::updateOrCreate(['key' => 'pusher_app_id'], ['value' => Setting::get("pusher_app_id_{$nextSet}")]);
        Setting::updateOrCreate(['key' => 'pusher_key'], ['value' => Setting::get("pusher_key_{$nextSet}")]);
        Setting::updateOrCreate(['key' => 'pusher_secret'], ['value' => Setting::get("pusher_secret_{$nextSet}")]);
        Setting::updateOrCreate(['key' => 'pusher_email'], ['value' => Setting::get("pusher_email_{$nextSet}")]);
        Setting::updateOrCreate(['key' => 'active_pusher_set'], ['value' => $nextSet]);

        // Reinitialize Pusher with new credentials
        $this->pusher = new Pusher(
            Setting::get('pusher_key'),
            Setting::get('pusher_secret'),
            Setting::get('pusher_app_id'),
            [
                'cluster' => Setting::get('pusher_cluster'),
                'useTLS' => true
            ]
        );
    }

    public function trigger($channel, $event, $data) {
        try {
            $this->pusher->trigger($channel, $event, $data);
        } catch (ApiErrorException $e) {
            if (strpos($e->getMessage(), 'quota exceeded') !== false) {
                $this->handlePusherQuotaExceeded();
                // Retry with new credentials
                $this->pusher->trigger($channel, $event, $data);
            } else {
                throw $e;
            }
        }
    }
    public function spinWheel()
    {
        // Generate random result from segments
        $index = random_int(0, count($this->segments) - 1);
        return $this->segments[$index];
    }



    public function startNewGame()
    {
        try {
            $game = ModelsGame::create([
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
        // Exponential growth formula: a * e^(b*t)
        // where a and b are constants that control the growth rate
        $baseMultiplier = 1.0;
        $growthRate = 0.1; // Adjust this to control how fast it grows

        return $baseMultiplier * exp($growthRate * $elapsedTime);
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

            $this->pusher->trigger('game', 'LeaderboardUpdated', []);

            $this->pusher->trigger('game', 'GameHistoryUpdated', []);
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
        $maxMultiplier =Setting::get('crash_max_multiplier');
        $crashPoint = mt_rand(100, $maxMultiplier * 100) / 100;
        Log::debug('Crash point generated', ['crash_point' => $crashPoint]);
        return $crashPoint;
    }


    public function processBet($userId, $amount, $selectedColor)
    {
        return DB::transaction(function () use ($userId, $amount, $selectedColor) {
            $user = User::findOrFail($userId);

            // Verify user has sufficient balance
            if ($user->wallet_balance < $amount) {
                throw new \Exception('Insufficient balance');
            }

            // Deduct bet amount
            $user->wallet_balance -= $amount;
            $user->save();

            // Create game record
            $game = Game::create([
                'user_id' => $userId,
                'bet_amount' => $amount,
                'selected_color' => $selectedColor,
                'status' => 'pending'
            ]);

            // Generate result
            $result = $this->spinWheel();

            // Calculate winnings
            $multiplier = $this->getMultiplier($result['color']);
            $winAmount = $selectedColor === $result['color'] ? $amount * $multiplier : 0;

            // Update user balance if won
            if ($winAmount > 0) {
                $user->wallet_balance += $winAmount;
                $user->save();
            }

            // Record transaction
            Transaction::create([
                'user_id' => $userId,
                'type' => 'game',
                'amount' => $winAmount - $amount,
                'reference' => 'SPIN-' . $game->id,
                'status' => 'completed'
            ]);

            // Update game record
            $game->update([
                'result_color' => $result['color'],
                'win_amount' => $winAmount,
                'status' => 'completed'
            ]);

            return [
                'success' => true,
                'result' => $result,
                'win_amount' => $winAmount
            ];
        });
    }


    private function getMultiplier($color)
    {
        foreach ($this->segments as $segment) {
            if ($segment['color'] === $color) {
                return $segment['multiplier'];
            }
        }
        return 0;
    }

    public function getGameHistory($userId)
    {
        return Game::where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();
    }

    public function getLeaderboard()
    {
        return User::orderBy('wallet_balance', 'desc')
            ->take(10)
            ->get(['id', 'name', 'wallet_balance']);
    }



    public function processSpinResults($game)
    {
        $result = $this->spinWheel();

        $this->pusher->trigger('spin-game', 'SpinResult', [
            'game_id' => $game->id,
            'result' => $result
        ]);

        // Process all pending bets
        $game->bets()->where('status', 'pending')->each(function ($bet) use ($result) {
            $winAmount = 0;
            if ($bet->color === $result['color']) {
                $multiplier = $this->getMultiplier($result['color']);
                $winAmount = $bet->amount * $multiplier;

                // Update user balance
                $bet->user->increment('wallet_balance', $winAmount);
            }

            $bet->update([
                'status' => 'completed',
                'win_amount' => $winAmount
            ]);
        });
    }

}

