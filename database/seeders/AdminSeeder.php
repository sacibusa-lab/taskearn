<?php

namespace Database\Seeders;

use App\Models\Level;
use App\Models\User;
use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        // Create the platform admin account (only if not exists)
        User::firstOrCreate(
            ['phone' => '08010000000'],
            [
                'name' => 'Admin',
                'email' => 'admin@taskearn.com',
                'password' => bcrypt('password'),
                'is_admin' => true,
                'level_id' => Level::where('level', 5)->first()?->id,
                'status' => 'active',
                'is_probation' => false,
            ]
        );
    }
}
