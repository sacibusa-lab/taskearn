<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * IMPORTANT: All seeders use firstOrCreate() so existing data
     * is NEVER overwritten. Run `php artisan db:seed` safely anytime.
     *
     * For production updates, use ONLY: php artisan migrate
     * NEVER use migrate:fresh in production — it drops ALL data.
     */
    public function run(): void
    {
        $this->call([
            LevelSeeder::class,
            AdminSettingSeeder::class,
            AdminSeeder::class,
            BadgeSeeder::class,
        ]);
    }
}

