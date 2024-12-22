<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class GameUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $gameId;
    public $multiplier;

    public function __construct($gameId, $multiplier)
    {
        $this->gameId = $gameId;
        $this->multiplier = $multiplier;
    }

    public function broadcastOn()
    {
        return new Channel('game');
    }

    public function broadcastAs()
    {
        return 'GameUpdated';
    }
}
