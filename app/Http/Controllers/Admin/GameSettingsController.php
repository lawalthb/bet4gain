class GameSettingsController extends Controller
{
    public function updateCrashGame(Request $request)
    {
        Setting::updateOrCreate(
            ['key' => 'crash_min_bet'],
            ['value' => $request->min_bet]
        );

        Setting::updateOrCreate(
            ['key' => 'crash_max_bet'],
            ['value' => $request->max_bet]
        );

        return back()->with('success', 'Settings updated');
    }

    public function updateSpinWheel(Request $request)
    {
        Setting::updateOrCreate(
            ['key' => 'spin_wheel_multipliers'],
            ['value' => json_encode($request->multipliers)]
        );

        return back()->with('success', 'Settings updated');
    }
}
