<?php

namespace Database\Seeders;

use App\Models\Admin;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run()
    {
        Admin::create([
            'name' => 'Super Admin',
            'email' => 'admin@bet4gain.com',
            'password' => Hash::make('12345678'),
            'role' => 'super_admin'
        ]);

        Admin::create([
            'name' => 'Game Manager',
            'email' => 'manager@bet4gain.com',
            'password' => Hash::make('12345678'),
            'role' => 'game_manager'
        ]);
    }
}
