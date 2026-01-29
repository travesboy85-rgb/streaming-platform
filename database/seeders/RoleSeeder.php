<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        // Create roles with guard_name
        $roles = ['admin', 'user', 'creator', 'premium_user'];
        
        foreach ($roles as $role) {
            Role::firstOrCreate([
                'name' => $role,
                'guard_name' => 'web', // align with your user guard
            ]);
        }

        // Create permissions with guard_name
        $permissions = [
            'create video',
            'edit video', 
            'delete video',
            'view premium content',
            'manage users'
        ];
        
        foreach ($permissions as $permission) {
            Permission::firstOrCreate([
                'name' => $permission,
                'guard_name' => 'web', // align with your user guard
            ]);
        }

        // Assign permissions to roles
        $adminRole = Role::findByName('admin', 'web');
        $adminRole->givePermissionTo(Permission::all());

        $creatorRole = Role::findByName('creator', 'web');
        $creatorRole->givePermissionTo(['create video', 'edit video']);

        $premiumRole = Role::findByName('premium_user', 'web');
        $premiumRole->givePermissionTo(['view premium content']);

        // Assign roles to users if they exist
        $firstUser = User::find(1); // default admin
        if ($firstUser) {
            $firstUser->assignRole('admin');
        }

        $secondUser = User::find(2); // default creator
        if ($secondUser) {
            $secondUser->assignRole('creator');
        }

        $thirdUser = User::find(3); // default regular user
        if ($thirdUser) {
            $thirdUser->assignRole('user');
        }

        echo "Roles, permissions, and initial user assignments created!\n";
    }
}
