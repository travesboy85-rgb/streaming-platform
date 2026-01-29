<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Create admin user
        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@streaming.com',
            'password' => Hash::make('password123'),
        ]);
        $admin->assignRole('admin');

        // Create regular user
        $user = User::create([
            'name' => 'Test User',
            'email' => 'user@streaming.com',
            'password' => Hash::make('password123'),
        ]);
        $user->assignRole('user');

        // Create content creator
        $creator = User::create([
            'name' => 'Content Creator',
            'email' => 'creator@streaming.com',
            'password' => Hash::make('password123'),
        ]);
        $creator->assignRole('creator');

        echo "Users created successfully!\n";
    }
}