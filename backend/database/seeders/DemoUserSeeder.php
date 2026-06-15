<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

class DemoUserSeeder extends Seeder
{
    public function run(): void
    {
        // Self-sufficient: guarantee the admin role exists and holds admin.access,
        // even if RolePermissionSeeder didn't fully run. This ensures the admin
        // can always log in to /admin after seeding.
        $adminRole = Role::firstOrCreate(['name' => 'admin'], ['label' => 'Administrator', 'description' => 'Full platform control']);
        $userRole = Role::firstOrCreate(['name' => 'user'], ['label' => 'User', 'description' => 'Standard member']);
        $access = Permission::firstOrCreate(['name' => 'admin.access'], ['group' => 'dashboard', 'label' => 'Access admin panel']);
        $adminRole->permissions()->syncWithoutDetaching([$access->id]);

        // NOTE: plaintext password — the User model's `hashed` cast hashes it once.
        $admin = User::updateOrCreate(
            ['email' => 'admin@travelcash.test'],
            ['name' => 'Platform Admin', 'password' => 'password', 'email_verified_at' => now(), 'status' => 'active']
        );
        $admin->roles()->syncWithoutDetaching([$adminRole->id]);

        $user = User::updateOrCreate(
            ['email' => 'user@travelcash.test'],
            ['name' => 'Demo Traveller', 'password' => 'password', 'email_verified_at' => now(), 'status' => 'active']
        );
        $user->roles()->syncWithoutDetaching([$userRole->id]);
    }
}
