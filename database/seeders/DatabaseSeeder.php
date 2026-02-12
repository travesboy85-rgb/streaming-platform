<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // ✅ Ensure roles exist
        $adminRole = Role::firstOrCreate([
            'name' => 'admin',
            'guard_name' => 'web',
        ]);

        $userRole = Role::firstOrCreate([
            'name' => 'user',
            'guard_name' => 'web',
        ]);

        $creatorRole = Role::firstOrCreate([
            'name' => 'creator',
            'guard_name' => 'web',
        ]);

        // ✅ Create the admin user
        $admin = User::firstOrCreate(
            ['email' => 'admin@streaming.com'],
            [
                'name' => 'Admin User',
                'password' => Hash::make('password123'),
            ]
        );
        $admin->assignRole($adminRole);

        // ✅ Create a default test user
        $testUser = User::firstOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => 'Test User',
                'password' => Hash::make('password'),
            ]
        );
        $testUser->assignRole($userRole);

        // ✅ Create a demo creator
        $creator = User::firstOrCreate(
            ['email' => 'creator@streaming.com'],
            [
                'name' => 'Demo Creator',
                'password' => Hash::make('password'),
            ]
        );
        $creator->assignRole($creatorRole);

        echo "✅ Admin, Creator, and Test User seeded successfully!\n";
    }
}


