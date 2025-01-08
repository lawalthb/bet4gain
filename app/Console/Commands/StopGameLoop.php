<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Process;

class StopGameLoop extends Command
{
    protected $signature = 'game:stop';
    protected $description = 'Stop the running game loop';

    public function handle()
    {
        $this->info('Stopping existing game loop...');

        // Kill existing game:run processes on Linux
        exec('killall -9 "php artisan game:run"');
    }
}
