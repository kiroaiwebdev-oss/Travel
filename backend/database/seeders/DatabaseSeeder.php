<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Resilient: each seeder is isolated. Admin (DemoUserSeeder) runs EARLY and
        // is self-sufficient, so even if a later seeder fails, admin login still works.
        $seeders = [
            RolePermissionSeeder::class,
            DemoUserSeeder::class,   // <-- admin + user created up front, guaranteed
            SettingsSeeder::class,
            ProviderSeeder::class,
            DemoDataSeeder::class,
        ];

        foreach ($seeders as $seeder) {
            try {
                $this->call($seeder);
            } catch (\Throwable $e) {
                $this->command?->warn("Seeder {$seeder} failed (skipped): ".$e->getMessage());
            }
        }
    }
}
