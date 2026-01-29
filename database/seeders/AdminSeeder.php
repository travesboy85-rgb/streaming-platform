<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Spatie\Permission\Models\Role;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        // Ensure the admin role exists
        Role::firstOrCreate([
            'name' => 'admin',
            'guard_name' => 'web',
        ]);

        // Create the admin user if not already present
        $admin = User::firstOrCreate(
            ['email' => 'admin@streaming.com'],
            [
                'name' => 'Admin User',
                'password' => Hash::make('password123'),
            ]
        );

        // Assign the admin role
        if (!$admin->hasRole('admin')) {
            $admin->assignRole('admin');
        }

        echo "Admin user created successfully!\n";
    }
}
