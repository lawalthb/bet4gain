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
        $result = Process::run('ps aux | grep "[g]ame:run"');
        $processes = array_filter(explode("\n", $result->output()));

        if (empty($processes)) {
            $this->info('No game loop processes found running.');
            return;
        }

        foreach ($processes as $process) {
            $pid = preg_split('/\s+/', trim($process))[1];
            Process::run("kill {$pid}");
            $this->info("Stopped game loop process {$pid}");
        }

        $this->info('All game loop processes have been stopped.');
    }
}
