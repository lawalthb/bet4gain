<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class GameSettingsController extends Controller
{
    public function crashSettings()
    {
        $settings = [
            'min_bet' => Setting::get('crash_min_bet') ?? 100,
            'max_bet' => Setting::get('crash_max_bet') ?? 10000,
            'house_edge' => Setting::get('crash_house_edge') ?? 5,
            'max_multiplier' => Setting::get('crash_max_multiplier') ?? 100
        ];

        return view('admin.games.crash-settings', compact('settings'));
    }

    public function updateCrashGame(Request $request)
    {
        Setting::updateOrCreate(['key' => 'crash_min_bet'], ['value' => $request->min_bet]);
        Setting::updateOrCreate(['key' => 'crash_max_bet'], ['value' => $request->max_bet]);
        Setting::updateOrCreate(['key' => 'crash_house_edge'], ['value' => $request->house_edge]);
        Setting::updateOrCreate(['key' => 'crash_max_multiplier'], ['value' => $request->max_multiplier]);

        return back()->with('success', 'Crash game settings updated successfully');
    }

    public function updateSpinWheel(Request $request)
    {
        Setting::updateOrCreate(
            ['key' => 'spin_wheel_multipliers'],
            ['value' => json_encode($request->multipliers)]
        );

        return back()->with('success', 'Settings updated');
    }

    public function index()
    {
        $settings = [
            'crash_game' => [
                'min_bet' => Setting::get('crash_min_bet'),
                'max_bet' => Setting::get('crash_max_bet'),
                'house_edge' => Setting::get('crash_house_edge'),
                'max_multiplier' => Setting::get('crash_max_multiplier')
            ],
            'spin_wheel' => [
                'min_bet' => Setting::get('spin_min_bet'),
                'max_bet' => Setting::get('spin_max_bet'),
                'multipliers' => json_decode(Setting::get('spin_wheel_multipliers'), true)
            ],
            'general' => [
                'maintenance_mode' => Setting::get('maintenance_mode'),
                'max_withdrawal' => Setting::get('max_withdrawal'),
                'min_withdrawal' => Setting::get('min_withdrawal')
            ]
        ];

        return view('admin.settings.index', compact('settings'));
    }




public function pusherSettings()
{
    $settings = [
        'app_id' => Setting::get('pusher_app_id'),
        'app_key' => Setting::get('pusher_app_key'),
        'app_secret' => Setting::get('pusher_app_secret'),
        'cluster' => Setting::get('pusher_cluster'),
        // 'host' => Setting::get('pusher_host'),
        // 'port' => Setting::get('pusher_port'),
        // 'scheme' => Setting::get('pusher_scheme')
    ];

    return view('admin.settings.pusher', compact('settings'));
}

public function updatePusherSettings(Request $request)
{
    $validated = $request->validate([
        'app_id' => 'required',
        'app_key' => 'required',
        'app_secret' => 'required',
        'cluster' => 'required',
        // 'host' => 'required',
        // 'port' => 'required',
        // 'scheme' => 'required'
    ]);

    foreach ($validated as $key => $value) {
        Setting::updateOrCreate(
            ['key' => 'pusher_' . $key],
            ['value' => $value]
        );
    }

    return back()->with('success', 'Pusher settings updated successfully');
}

}
