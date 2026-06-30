<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminSetting;
use Illuminate\Http\Request;

class RewardController extends Controller
{
    public function index()
    {
        $keys = [
            'daily_login_base_bonus',
            'daily_login_streak_bonus_per_week',
            'daily_login_max_streak_weeks',
            'leaderboard_reward_percentage',
            'referral_commission_rate',
            'referral_level2_rate',
            'referral_level3_rate',
        ];

        $settings = collect();
        foreach ($keys as $key) {
            $setting = AdminSetting::firstOrCreate(
                ['key' => $key],
                [
                    'type' => 'number',
                    'group' => 'rewards',
                ]
            );
            $settings->put($key, $setting);
        }

        return view('admin.rewards.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'daily_login_base_bonus' => ['required', 'numeric', 'min:0'],
            'daily_login_streak_bonus_per_week' => ['required', 'numeric', 'min:0'],
            'daily_login_max_streak_weeks' => ['required', 'integer', 'min:1', 'max:100'],
            'leaderboard_reward_percentage' => ['required', 'numeric', 'min:0', 'max:100'],
            'referral_commission_rate' => ['required', 'numeric', 'min:0', 'max:100'],
            'referral_level2_rate' => ['required', 'numeric', 'min:0', 'max:100'],
            'referral_level3_rate' => ['required', 'numeric', 'min:0', 'max:100'],
        ]);

        foreach ($request->except('_token') as $key => $value) {
            AdminSetting::setValue($key, $value);
        }

        return back()->with('success', 'Reward settings updated successfully.');
    }
}
