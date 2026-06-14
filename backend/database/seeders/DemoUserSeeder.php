<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DemoUserSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::updateOrCreate(
            ['email' => 'admin@travelcash.test'],
            [
                'name' => 'Platform Admin',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'status' => 'active',
            ]
        );
        $admin->roles()->syncWithoutDetaching([Role::where('name', 'admin')->value('id')]);

        $user = User::updateOrCreate(
            ['email' => 'user@travelcash.test'],
            [
                'name' => 'Demo Traveller',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'status' => 'active',
            ]
        );
        $user->roles()->syncWithoutDetaching([Role::where('name', 'user')->value('id')]);
    }
}
