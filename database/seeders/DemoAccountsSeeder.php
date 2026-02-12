<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class DemoAccountsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Admin account
        User::updateOrCreate(
            ['email' => 'admin@demo.com'],
            [
                'name' => 'Demo Admin',
                'password' => Hash::make('password'),
                'role' => 'admin',
            ]
        );

        // Creator account
        User::updateOrCreate(
            ['email' => 'creator@demo.com'],
            [
                'name' => 'Demo Creator',
                'password' => Hash::make('password'),
                'role' => 'creator',
            ]
        );

        // User account
        User::updateOrCreate(
            ['email' => 'user@demo.com'],
            [
                'name' => 'Demo User',
                'password' => Hash::make('password'),
                'role' => 'user',
            ]
        );
    }
}
