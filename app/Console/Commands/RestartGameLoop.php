<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class RestartGameLoop extends Command
{
    protected $signature = 'game:restart';
    protected $description = 'Restart the crash game loop';

    public function handle()
    {
        $this->info('Stopping existing game loop...');

        // Kill existing game:run processes on Linux
        exec('killall -9 "php artisan game:run"');

        $this->info('Starting new game loop...');

        // Start new game loop in background
        exec('nohup php artisan game:run > /dev/null 2>&1 &');

        $this->info('Game loop restarted successfully!');
    }
}
