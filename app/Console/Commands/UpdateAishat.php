<?php

namespace App\Console\Commands;

use App\Models\Setting;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class UpdateAishat extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pusher:aishat';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update Pusher settings to Aishat details';
    /**
     * Execute the console command.
     */
    public function handle()
    {


        // Get values for the new set from settings
        $newAppId = Setting::get("pusher_app_id_4");
        $newKey = Setting::get("pusher_key_4");
        $newSecret = Setting::get("pusher_secret_4");
        $newEmail = Setting::get("pusher_email_4");

        // Update active credentials
        Setting::updateOrCreate(['key' => 'pusher_app_id'], ['value' => $newAppId]);
        Setting::updateOrCreate(['key' => 'pusher_key'], ['value' => $newKey]);
        Setting::updateOrCreate(['key' => 'pusher_secret'], ['value' => $newSecret]);
        Setting::updateOrCreate(['key' => 'pusher_email'], ['value' => $newEmail]);

        // Update the active set indicator
        Setting::updateOrCreate(['key' => 'active_pusher_set'], ['value' => 4]);

        $this->info("Switched to Pusher credentials set Aishat");
    }
}
