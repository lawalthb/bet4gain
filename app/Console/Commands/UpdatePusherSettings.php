<?php

namespace App\Console\Commands;

use App\Models\Setting;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class UpdatePusherSettings extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pusher:update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update Pusher settings from remote source';
    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Get current active set (1 or 2)
        $currentSet = Setting::get('active_pusher_set', '1');

        // Determine which set to switch to
        $newSet = $currentSet === '1' ? '2' : '1';

        // Get values for the new set from settings
        $newAppId = Setting::get("pusher_app_id_{$newSet}");
        $newKey = Setting::get("pusher_key_{$newSet}");
        $newSecret = Setting::get("pusher_secret_{$newSet}");
        $newEmail = Setting::get("pusher_email_{$newSet}");

        // Update active credentials
        Setting::updateOrCreate(['key' => 'pusher_app_id'], ['value' => $newAppId]);
        Setting::updateOrCreate(['key' => 'pusher_key'], ['value' => $newKey]);
        Setting::updateOrCreate(['key' => 'pusher_secret'], ['value' => $newSecret]);
        Setting::updateOrCreate(['key' => 'pusher_email'], ['value' => $newEmail]);

        // Update the active set indicator
        Setting::updateOrCreate(['key' => 'active_pusher_set'], ['value' => $newSet]);

        $this->info("Switched to Pusher credentials set {$newSet}");
    }
}
