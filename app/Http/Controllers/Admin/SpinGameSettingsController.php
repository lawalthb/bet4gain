<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Models\SpinGame;
use Illuminate\Http\Request;

class SpinGameSettingsController extends Controller
{
    public function index()
    {
        $settings = [
            'min_bet' => Setting::get('spin_min_bet') ?? 100,
            'max_bet' => Setting::get('spin_max_bet') ?? 10000,
            'house_edge' => Setting::get('spin_house_edge') ?? 5,
        ];

        $recentGames = SpinGame::with('bets.user')
            ->latest()
            ->take(20)
            ->get();

        $statistics = [
            'total_games' => SpinGame::count(),
            'total_bets' => SpinGame::join('spin_bets', 'spin_games.id', '=', 'spin_bets.spin_game_id')
                ->sum('spin_bets.amount'),
            'average_multiplier' => SpinGame::avg('multiplier'),
            'most_common_color' => SpinGame::selectRaw('result_color, COUNT(*) as count')
                ->groupBy('result_color')
                ->orderByDesc('count')
                ->first()?->result_color
        ];

        return view('admin.games.spin-settings', compact('settings', 'recentGames', 'statistics'));
    }

    public function updateSettings(Request $request)
    {
        $validated = $request->validate([
            'min_bet' => 'required|numeric|min:1',
            'max_bet' => 'required|numeric|gt:min_bet',
            'house_edge' => 'required|numeric|between:0,100'
        ]);

        foreach ($validated as $key => $value) {
            Setting::updateOrCreate(
                ['key' => 'spin_' . $key],
                ['value' => $value]
            );
        }

        return back()->with('success', 'Spin game settings updated successfully');
    }
}
