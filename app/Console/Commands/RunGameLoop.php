<?php

namespace App\Console\Commands;

use App\Services\GameService;
use Illuminate\Console\Command;

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
        while (true) {
            $game = $this->gameService->startNewGame();

            while (!$game->is_completed) {
                $this->gameService->updateGameState($game);
                usleep(100000); // 100ms interval
            }

            sleep(5); // Wait between games
        }
    }
}
