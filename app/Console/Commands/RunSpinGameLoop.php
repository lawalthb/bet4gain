<?php

namespace App\Console\Commands;

use App\Models\SpinGame;
use App\Services\GameService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Pusher\Pusher;
use App\Models\Setting;

class RunSpinGameLoop extends Command
{
    protected $signature = 'spin:run';
    protected $description = 'Run the spin game loop';

    private $pusher;


    private $gameService;

    public function __construct(GameService $gameService)
    {
        parent::__construct();
        $this->gameService = $gameService;

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

    public function handle()
    {
        Log::info('Spin Game Loop Started');

        while (true) {
            $this->info('Starting new spin game...');

            // Create new game with your schema columns
            $game = SpinGame::create([
                'started_at' => now(),
                'is_completed' => false,
                'result_color' => null,
                'multiplier' => null
            ]);

            // Wait for bets (10 seconds)
            sleep(10);

            // Generate result and update game
            $result = $this->gameService->spinWheel();
            $game->update([
                'is_completed' => true,
                'result_color' => $result['color'],
                'multiplier' => $result['multiplier']
            ]);

            // Broadcast result
            $this->gameService->trigger('spin-game', 'SpinResult', [
                'game_id' => $game->id,
                'result_color' => $result['color'],
                'multiplier' => $result['multiplier']
            ]);

            sleep(5); // Cool down before next game
        }
    }
}
