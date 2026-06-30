@extends('admin.layouts.admin')

@section('title', 'Rewards & Commissions')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Rewards & Commissions</h1>
            <p class="text-sm text-gray-500 mt-1">Configure all platform reward amounts, referral rates, and bonus percentages.</p>
        </div>
    </div>

    @php
        $dailyBase = $settings->get('daily_login_base_bonus')?->value ?? 1;
        $dailyStreak = $settings->get('daily_login_streak_bonus_per_week')?->value ?? 0.5;
        $maxWeeks = $settings->get('daily_login_max_streak_weeks')?->value ?? 10;
        $leaderboardPct = $settings->get('leaderboard_reward_percentage')?->value ?? 10;
        $l1Rate = $settings->get('referral_commission_rate')?->value ?? 10;
        $l2Rate = $settings->get('referral_level2_rate')?->value ?? 3;
        $l3Rate = $settings->get('referral_level3_rate')?->value ?? 1;
    @endphp

    {{-- Summary Card --}}
    <div class="bg-gradient-to-r from-emerald-500 to-green-700 rounded-2xl p-6 text-white shadow-lg mb-8">
        <p class="text-sm text-emerald-100 mb-4">Current Reward Summary</p>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
            <div>
                <p class="text-3xl font-bold">{{ currency_raw($dailyBase) }}</p>
                <p class="text-xs text-emerald-200 mt-1">Daily Login Base</p>
            </div>
            <div>
                <p class="text-3xl font-bold">{{ currency_raw($dailyStreak) }}</p>
                <p class="text-xs text-emerald-200 mt-1">Streak Bonus/wk</p>
            </div>
            <div>
                <p class="text-3xl font-bold">{{ $leaderboardPct }}%</p>
                <p class="text-xs text-emerald-200 mt-1">Leaderboard Bonus</p>
            </div>
            <div>
                <p class="text-3xl font-bold">{{ $l1Rate }}/{{ $l2Rate }}/{{ $l3Rate }}%</p>
                <p class="text-xs text-emerald-200 mt-1">Referral (L1/L2/L3)</p>
            </div>
        </div>
    </div>

    {{-- Daily Login Rewards --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-6">
        <div class="flex items-center space-x-3 mb-6">
            <div class="w-10 h-10 bg-orange-100 text-orange-600 rounded-xl flex items-center justify-center">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.66 11.2c-.23-.3-.51-.56-.77-.82-.67-.6-1.43-1.03-2.07-1.66C13.33 7.26 13 4.85 13.95 3c-1.04.23-1.87.75-2.58 1.32-2.59 2.08-3.61 5.75-2.39 8.9.04.1.08.2.08.33 0 .22-.15.42-.35.5-.23.1-.47.04-.66-.12-.06-.05-.1-.1-.14-.17-1.13-1.43-1.31-3.48-.55-5.12C5.78 10 4.87 12.3 5 14.47c.06.5.12 1 .29 1.5.14.6.41 1.2.71 1.73 1.08 1.73 2.95 2.97 4.96 3.22 2.14.27 4.43-.12 5.93-1.6 1.66-1.64 2.26-4.27.77-8.12z"/></svg>
            </div>
            <div>
                <h2 class="text-lg font-semibold text-gray-900">🔥 Daily Login Rewards</h2>
                <p class="text-sm text-gray-500">Amounts earned when users log in each day</p>
            </div>
        </div>

        <form action="{{ route('admin.rewards.update') }}" method="POST">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Base Bonus ({{ currency_symbol() }})</label>
                    <input type="number" name="daily_login_base_bonus" value="{{ $dailyBase }}" step="0.01" min="0"
                           class="w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <p class="text-xs text-gray-400 mt-1">Earned per daily login</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Streak Bonus Per Week ({{ currency_symbol() }})</label>
                    <input type="number" name="daily_login_streak_bonus_per_week" value="{{ $dailyStreak }}" step="0.01" min="0"
                           class="w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <p class="text-xs text-gray-400 mt-1">Extra per week of consecutive streak</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Max Streak Weeks</label>
                    <input type="number" name="daily_login_max_streak_weeks" value="{{ $maxWeeks }}" min="1" max="100"
                           class="w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <p class="text-xs text-gray-400 mt-1">Maximum streak weeks that count</p>
                </div>
            </div>

            <div class="mt-4 bg-gray-50 rounded-xl p-4">
                <p class="text-xs text-gray-500">
                    <strong>Example:</strong> A user on a 14-day streak with current settings earns
                    <strong>{{ currency_raw($dailyBase + min(floor(14 / 7), (int) $maxWeeks) * (float) $dailyStreak) }}</strong> per login
                    ({{ currency_raw($dailyBase) }} base + 2 weeks × {{ currency_raw($dailyStreak) }} streak bonus).
                </p>
            </div>
    </div>

    {{-- Leaderboard Rewards --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-6">
        <div class="flex items-center space-x-3 mb-6">
            <div class="w-10 h-10 bg-amber-100 text-amber-600 rounded-xl flex items-center justify-center">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
            </div>
            <div>
                <h2 class="text-lg font-semibold text-gray-900">🏆 Leaderboard Bonus</h2>
                <p class="text-sm text-gray-500">Percentage bonus awarded to the top earner each period</p>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Reward Percentage (%)</label>
                <input type="number" name="leaderboard_reward_percentage" value="{{ $leaderboardPct }}" step="0.1" min="0" max="100"
                       class="w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                <p class="text-xs text-gray-400 mt-1">% of period earnings awarded as bonus</p>
            </div>
        </div>
    </div>

    {{-- Referral Commissions --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-6">
        <div class="flex items-center space-x-3 mb-6">
            <div class="w-10 h-10 bg-blue-100 text-blue-600 rounded-xl flex items-center justify-center">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            </div>
            <div>
                <h2 class="text-lg font-semibold text-gray-900">🤝 Referral Commissions</h2>
                <p class="text-sm text-gray-500">Commission rates for multi-level referral earnings</p>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Level 1 (%)</label>
                <input type="number" name="referral_commission_rate" value="{{ $l1Rate }}" step="0.1" min="0" max="100"
                       class="w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                <p class="text-xs text-gray-400 mt-1">Direct referrals</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Level 2 (%)</label>
                <input type="number" name="referral_level2_rate" value="{{ $l2Rate }}" step="0.1" min="0" max="100"
                       class="w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                <p class="text-xs text-gray-400 mt-1">Referrals of your referrals</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Level 3 (%)</label>
                <input type="number" name="referral_level3_rate" value="{{ $l3Rate }}" step="0.1" min="0" max="100"
                       class="w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                <p class="text-xs text-gray-400 mt-1">Third-level referrals</p>
            </div>
        </div>
    </div>

    {{-- Save Button --}}
    <div class="flex items-center justify-between bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <p class="text-xs text-gray-400">Changes apply immediately across the platform</p>
        <button type="submit" class="px-6 py-3 bg-indigo-600 text-white rounded-xl hover:bg-indigo-700 font-medium text-sm transition-colors shadow-sm flex items-center space-x-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/></svg>
            <span>Save Rewards</span>
        </button>
    </div>

    </form>
</div>
@endsection