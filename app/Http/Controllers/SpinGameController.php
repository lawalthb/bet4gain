<?php

namespace App\Http\Controllers;

use App\Events\GameSpinResult;
use App\Models\SpinGame;
use App\Models\SpinBet;
use App\Events\SpinGameStarted;
use App\Events\SpinGameEnded;
use App\Services\GameService;
use Illuminate\Http\Request;

class SpinGameController extends Controller
{
    private $gameService;

    public function __construct(GameService $gameService)
    {
        $this->gameService = $gameService;
    }


    public function start()
    {
        $game = SpinGame::create([
            'status' => 'active',
            'started_at' => now()
        ]);

        broadcast(new SpinGameStarted($game));

        return response()->json($game);
    }

    public function spin(SpinGame $game)
    {
        $segments = [
            ['color' => 'red', 'multiplier' => 2],
            ['color' => 'black', 'multiplier' => 2],
            ['color' => 'green', 'multiplier' => 14]
        ];

        $result = $segments[array_rand($segments)];

        $game->update([
            'result' => $result['color'],
            'multiplier' => $result['multiplier'],
            'status' => 'completed',
            'ended_at' => now()
        ]);

        broadcast(new SpinGameEnded($game));

        return response()->json($game);
    }

    public function placeBet(Request $request)
    {
        $result = $this->gameService->processBet(
            auth()->id(),
            $request->amount,
            $request->color
        );

        broadcast(new GameSpinResult($result))->toOthers();
        return response()->json($result);
    }
}
