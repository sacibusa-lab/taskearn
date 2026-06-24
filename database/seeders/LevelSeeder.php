<?php

namespace Database\Seeders;

use App\Models\Level;
use Illuminate\Database\Seeder;

class LevelSeeder extends Seeder
{
    public function run(): void
    {
        $levels = [
            ['level' => 0, 'deposit_amount' => 0, 'weekly_payout' => 0, 'description' => 'Free member - no deposit required'],
            ['level' => 1, 'deposit_amount' => 100, 'weekly_payout' => 10, 'description' => 'Bronze member'],
            ['level' => 2, 'deposit_amount' => 250, 'weekly_payout' => 30, 'description' => 'Silver member'],
            ['level' => 3, 'deposit_amount' => 500, 'weekly_payout' => 65, 'description' => 'Gold member'],
            ['level' => 4, 'deposit_amount' => 1000, 'weekly_payout' => 140, 'description' => 'Platinum member'],
            ['level' => 5, 'deposit_amount' => 2500, 'weekly_payout' => 375, 'description' => 'Diamond member'],
        ];

        foreach ($levels as $level) {
            Level::firstOrCreate(
                ['level' => $level['level']],
                $level
            );
        }
    }
}
