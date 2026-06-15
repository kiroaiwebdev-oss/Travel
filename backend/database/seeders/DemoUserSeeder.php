<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

class DemoUserSeeder extends Seeder
{
    public function run(): void
    {
        // NOTE: pass the PLAINTEXT password. The User model's `password => 'hashed'`
        // cast hashes it exactly once. (Hashing here too would risk a double-hash.)
        $admin = User::updateOrCreate(
            ['email' => 'admin@travelcash.test'],
            [
                'name' => 'Platform Admin',
                'password' => 'password',
                'email_verified_at' => now(),
                'status' => 'active',
            ]
        );
        $admin->roles()->syncWithoutDetaching([Role::where('name', 'admin')->value('id')]);

        $user = User::updateOrCreate(
            ['email' => 'user@travelcash.test'],
            [
                'name' => 'Demo Traveller',
                'password' => 'password',
                'email_verified_at' => now(),
                'status' => 'active',
            ]
        );
        $user->roles()->syncWithoutDetaching([Role::where('name', 'user')->value('id')]);
    }
}
