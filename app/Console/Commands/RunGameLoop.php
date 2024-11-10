<?php

namespace App\Console\Commands;

use App\Services\GameService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class RunGameLoop extends Command
{
    protected $signature = 'game:run';
    protected $description = 'Run the crash game loop';

    private $gameService;

    public function __construct(GameService $gameService)
    {
        parent::__construct();
        $this->gameService = $gameService;
    }

    public function handle()
    {
        Log::info('Game Loop Started');

        while (true) {
            $this->info('Starting new game...');
            Log::info('New Game Round Starting');

            $game = $this->gameService->startNewGame();

            while (!$game->is_completed) {
                $this->gameService->updateGameState($game);
                Log::info('Game State Updated', ['game_id' => $game->id]);
                usleep(100000); // 100ms interval
            }

            $this->info('Game crashed! Waiting for next round...');
            Log::info('Game Round Completed', ['game_id' => $game->id]);
            sleep(5); // Wait between games
        }
    }
}
