<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Game;
use App\Models\Bet;
use App\Models\User;

class GameSeeder extends Seeder
{
    public function run()
    {
        // Create sample games
        Game::factory(10)->create();

        // Create bot players
        User::factory(5)->create(['is_bot' => true]);

        // Create some sample bets
        Bet::factory(20)->create();
    }
}
