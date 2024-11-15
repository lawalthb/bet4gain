<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Bet;
use Carbon\Carbon;

class LeaderboardController extends Controller
{
    public function getLeaders($timeFrame)
    {
        $query = Bet::query()->where('status', 'won');

        switch ($timeFrame) {
            case 'daily':
                $query->whereDate('created_at', Carbon::today());
                break;
            case 'weekly':
                $query->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);
                break;
            case 'monthly':
                $query->whereMonth('created_at', Carbon::now()->month);
                break;
        }

        $leaders = $query->select('user_id')
            ->selectRaw('COUNT(*) as total_wins')
            ->selectRaw('SUM(profit) as total_profit')
            ->with('user:id,name')
            ->groupBy('user_id')
            ->orderByDesc('total_profit')
            ->limit(10)
            ->get()
            ->map(function ($bet) {
                return [
                    'id' => $bet->user_id,
                    'name' => $bet->user->name,
                    'total_wins' => $bet->total_wins,
                    'total_profit' => number_format($bet->total_profit, 2)
                ];
            });

        return response()->json($leaders);
    }
}
