<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class GameStarted implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $gameId;
    public $startTime;

    public function __construct($data)
    {
        $this->gameId = $data['game_id'];
        $this->startTime = $data['start_time'];
    }

    public function broadcastOn()
    {
        return new Channel('game');
    }

    public function broadcastAs()
    {
        return 'GameStarted';
    }
}
