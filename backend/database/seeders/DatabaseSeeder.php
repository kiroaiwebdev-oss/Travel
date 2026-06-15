<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RolePermissionSeeder::class,
            SettingsSeeder::class,
            ProviderSeeder::class,
            DemoUserSeeder::class,
            DemoDataSeeder::class, // sample data across every section for testing
        ]);
    }
}
