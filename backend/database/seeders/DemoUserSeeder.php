<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DemoUserSeeder extends Seeder
{
    public function run(): void
    {
        // Roles + the critical admin.access permission (self-sufficient).
        $adminRole = Role::firstOrCreate(['name' => 'admin'], ['label' => 'Administrator', 'description' => 'Full platform control']);
        $userRole = Role::firstOrCreate(['name' => 'user'], ['label' => 'User', 'description' => 'Standard member']);
        $access = Permission::firstOrCreate(['name' => 'admin.access'], ['group' => 'dashboard', 'label' => 'Access admin panel']);
        $adminRole->permissions()->syncWithoutDetaching([$access->id]);

        $admin = User::updateOrCreate(['email' => 'admin@travelcash.test'], [
            'name' => 'Platform Admin', 'email_verified_at' => now(), 'status' => 'active',
        ]);
        $user = User::updateOrCreate(['email' => 'user@travelcash.test'], [
            'name' => 'Demo Traveller', 'email_verified_at' => now(), 'status' => 'active',
        ]);

        // Set passwords at the DB level (exact bcrypt) — bypasses any cast edge case
        // so the seeded login ALWAYS works.
        DB::table('users')->whereIn('email', ['admin@travelcash.test', 'user@travelcash.test'])
            ->update(['password' => bcrypt('password')]);

        $admin->roles()->syncWithoutDetaching([$adminRole->id]);
        $user->roles()->syncWithoutDetaching([$userRole->id]);
    }
}
