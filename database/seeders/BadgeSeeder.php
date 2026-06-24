<?php

namespace Database\Seeders;

use App\Models\Badge;
use Illuminate\Database\Seeder;

class BadgeSeeder extends Seeder
{
    public function run(): void
    {
        $badges = [
            ['name' => 'Task Master', 'description' => 'Complete 10 tasks', 'icon' => '✅', 'criteria_type' => 'tasks_completed', 'criteria_value' => 10],
            ['name' => 'Task Champion', 'description' => 'Complete 50 tasks', 'icon' => '🏅', 'criteria_type' => 'tasks_completed', 'criteria_value' => 50],
            ['name' => 'Task Legend', 'description' => 'Complete 100 tasks', 'icon' => '👑', 'criteria_type' => 'tasks_completed', 'criteria_value' => 100],
            ['name' => 'Referral King', 'description' => 'Refer 5 friends', 'icon' => '🤝', 'criteria_type' => 'referrals_count', 'criteria_value' => 5],
            ['name' => 'Referral Legend', 'description' => 'Refer 20 friends', 'icon' => '🌟', 'criteria_type' => 'referrals_count', 'criteria_value' => 20],
            ['name' => 'Big Spender', 'description' => 'Deposit ₦10,000+', 'icon' => '💎', 'criteria_type' => 'deposit_amount', 'criteria_value' => 10000],
            ['name' => 'High Roller', 'description' => 'Deposit ₦50,000+', 'icon' => '👑', 'criteria_type' => 'deposit_amount', 'criteria_value' => 50000],
            ['name' => 'Streak 7', 'description' => '7-day login streak', 'icon' => '🔥', 'criteria_type' => 'login_streak', 'criteria_value' => 7],
            ['name' => 'Streak 30', 'description' => '30-day login streak', 'icon' => '⚡', 'criteria_type' => 'login_streak', 'criteria_value' => 30],
            ['name' => 'Early Adopter', 'description' => 'Joined first week', 'icon' => '🚀', 'criteria_type' => 'early_adopter', 'criteria_value' => 1],
            ['name' => 'Earner', 'description' => 'Earn ₦5,000+', 'icon' => '💰', 'criteria_type' => 'total_earned', 'criteria_value' => 5000],
        ];

        foreach ($badges as $badge) {
            Badge::firstOrCreate(['name' => $badge['name']], $badge);
        }
    }
}
