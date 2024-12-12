<?php

namespace App\Providers;

use App\Models\Setting;
use Illuminate\Support\Facades\Schema as FacadesSchema;
use Illuminate\Support\ServiceProvider;


class PusherSettingsProvider extends ServiceProvider
{
    public function boot()
    {
        if (FacadesSchema::hasTable('settings')) {
            config([
                'broadcasting.connections.pusher.key' => Setting::get('pusher_key'),
                'broadcasting.connections.pusher.secret' => Setting::get('pusher_secret'),
                'broadcasting.connections.pusher.app_id' => Setting::get('pusher_app_id'),
                'broadcasting.connections.pusher.cluster' => Setting::get('pusher_cluster'),
            ]);
        }
    }
}
