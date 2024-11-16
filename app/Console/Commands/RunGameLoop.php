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
            $game = $this->gameService->startNewGame();

            while (!$game->is_completed) {
                $this->gameService->updateGameState($game);
                usleep(50000); // 50ms for smooth multiplier updates
            }

            $this->info('Game crashed!');

            // 5 second countdown
            for ($i = 6; $i > 0; $i--) {
                $this->info("Next game in {$i}...");
                sleep(1);
            }
        }
    }
}
