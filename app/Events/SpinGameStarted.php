<?php

namespace App\Events;

use App\Models\SpinGame;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SpinGameStarted implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $game;

    public function __construct(SpinGame $game)
    {
        $this->game = $game;
    }

    public function broadcastOn()
    {
        return new Channel('game');
    }
}
