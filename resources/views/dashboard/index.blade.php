@extends('layouts.app')

@section('title', 'Dashboard')

@php
    $user = Auth::user();
    $streak = $user->login_streak ?? 0;
    $dailyBase = (float) \App\Models\AdminSetting::getValue('daily_login_base_bonus', 1);
    $dailyStreakPerWeek = (float) \App\Models\AdminSetting::getValue('daily_login_streak_bonus_per_week', 0.5);
    $dailyMaxWeeks = (int) \App\Models\AdminSetting::getValue('daily_login_max_streak_weeks', 10);
    $dailyBonus = $dailyBase + min(floor($streak / 7), $dailyMaxWeeks) * $dailyStreakPerWeek;
    $nextStreakMilestone = (floor($streak / 7) + 1) * 7;
    $streakPercent = $nextStreakMilestone > 0 ? min(100, ($streak / $nextStreakMilestone) * 100) : 0;

    $categoryLabels = [
        'tasks_completed' => '📋 Tasks',
        'referrals_count' => '🤝 Referrals',
        'deposit_amount' => '💎 Deposits',
        'total_earned' => '💰 Earnings',
        'login_streak' => '🔥 Streaks',
        'early_adopter' => '⏰ Early Adopter',
    ];
    $allBadges = \App\Models\Badge::all()->groupBy('criteria_type');
    $userBadges = $user->badges()->pluck('badge_id')->toArray();
@endphp

@section('content')
<div class="max-w-[1600px] mx-auto px-3 sm:px-6 lg:px-8 py-4 sm:py-8">

    {{-- Announcement Popup --}}
    @php $announcement = App\Models\Announcement::active()->latest()->first(); @endphp
    @if($announcement)
    <div x-data="{ show: true }" x-show="show" x-cloak
         class="mb-6 rounded-2xl border-2 overflow-hidden
            @if($announcement->type === 'info') border-blue-200 bg-blue-50
            @elseif($announcement->type === 'warning') border-amber-200 bg-amber-50
            @elseif($announcement->type === 'success') border-green-200 bg-green-50
            @elseif($announcement->type === 'danger') border-red-200 bg-red-50
            @else border-purple-200 bg-purple-50 @endif">
        @if($announcement->image)
            <img src="{{ asset('storage/' . $announcement->image) }}" alt="{{ $announcement->title }}" class="w-full h-48 object-cover">
        @endif
        <div class="p-6 @if($announcement->image) relative @endif">
            <div class="flex items-start justify-between">
                <div class="flex items-start space-x-4 flex-1 min-w-0">
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0 text-xl
                        @if($announcement->type === 'info') bg-blue-200 text-blue-900
                        @elseif($announcement->type === 'warning') bg-amber-200 text-amber-900
                        @elseif($announcement->type === 'success') bg-green-200 text-green-900
                        @elseif($announcement->type === 'danger') bg-red-200 text-red-900
                        @else bg-purple-200 text-purple-900 @endif">
                        {{ ['info'=>'📢','warning'=>'⚠️','success'=>'✅','danger'=>'🚨','promo'=>'🎉'][$announcement->type] }}
                    </div>
                    <div class="min-w-0">
                        <h3 class="text-lg font-extrabold @if($announcement->type === 'info') text-blue-900 @elseif($announcement->type === 'warning') text-amber-900 @elseif($announcement->type === 'success') text-green-900 @elseif($announcement->type === 'danger') text-red-900 @else text-purple-900 @endif">{{ $announcement->title }}</h3>
                        <p class="text-sm mt-1 opacity-80">{{ $announcement->message }}</p>
                        @if($announcement->action_url && $announcement->action_label)
                            <a href="{{ $announcement->action_url }}" class="inline-block mt-3 px-5 py-2 rounded-xl text-sm font-bold shadow-sm text-white
                                @if($announcement->type === 'info') bg-blue-600 hover:bg-blue-700
                                @elseif($announcement->type === 'warning') bg-amber-600 hover:bg-amber-700
                                @elseif($announcement->type === 'success') bg-green-600 hover:bg-green-700
                                @elseif($announcement->type === 'danger') bg-red-600 hover:bg-red-700
                                @else bg-purple-600 hover:bg-purple-700 @endif">
                                {{ $announcement->action_label }}
                            </a>
                        @endif
                    </div>
                </div>
                <button @click="show = false" class="shrink-0 p-1.5 rounded-lg hover:bg-black/10 transition-colors ml-4">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
        </div>
    </div>
    @endif

    {{-- Two Column Layout --}}
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-4 sm:gap-6 lg:gap-8">

        {{-- ================= MAIN CONTENT (Left - 3/4) ================= --}}
        <div class="lg:col-span-3 space-y-4 sm:space-y-6 lg:space-y-8">

            {{-- Welcome Banner --}}
            <div class="bg-gradient-to-r from-blue-600 via-indigo-600 to-indigo-700 text-white rounded-2xl sm:rounded-3xl p-5 sm:p-8 shadow-lg relative overflow-hidden">
                <div class="absolute right-0 top-0 opacity-[0.04] -translate-y-8 translate-x-8">
                    <svg class="w-80 h-80" fill="currentColor" viewBox="0 0 24 24"><path d="M21 6H3c-1.1 0-2 .9-2 2v8c0 1.1.9 2 2 2h18c1.1 0 2-.9 2-2V8c0-1.1-.9-2-2-2zm0 10H3V8h2v4h2V8h2v4h2V8h2v4h2V8h2v4h2V8h2v8z"/></svg>
                </div>
                <div class="relative z-10 flex items-center justify-between flex-wrap gap-4">
                    <div>
                        <h1 class="text-xl sm:text-2xl md:text-3xl font-extrabold tracking-tight">Welcome back, {{ $user->name }}! 👋</h1>
                        <p class="text-sm text-blue-100/80 mt-1.5">Here's your financial overview for today</p>
                    </div>
                    <div class="flex items-center space-x-2 sm:space-x-3 w-full sm:w-auto">
                        <a href="{{ route('deposits.create') }}" class="flex-1 sm:flex-none px-3 sm:px-5 py-2.5 bg-white text-blue-600 font-bold rounded-xl sm:rounded-2xl hover:bg-blue-50 transition-all text-xs sm:text-sm flex items-center justify-center space-x-1.5 sm:space-x-2 shadow-md">
                            <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                            <span>Deposit</span>
                        </a>
                        <a href="{{ route('withdrawals.create') }}" class="flex-1 sm:flex-none px-3 sm:px-5 py-2.5 bg-blue-500/20 backdrop-blur border border-blue-400/30 text-white font-bold rounded-xl sm:rounded-2xl hover:bg-blue-500/30 transition-all text-xs sm:text-sm flex items-center justify-center space-x-1.5 sm:space-x-2">
                            <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
                            <span>Withdraw</span>
                        </a>
                    </div>
                </div>
            </div>

            {{-- Notices --}}
            @if($user->isOnProbation())
                <div class="bg-amber-50 border border-amber-200 rounded-xl sm:rounded-2xl p-4 sm:p-5 flex items-start sm:items-center space-x-3 sm:space-x-4">
                    <div class="w-10 h-10 bg-amber-100 text-amber-700 rounded-xl flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-bold text-amber-800">Account Probation Period</p>
                        <p class="text-xs text-amber-600 mt-0.5">Your trial probation will end in <strong>{{ $user->probation_ends_at->diffForHumans() }}</strong>. Upgrade your level to unlock higher paying premium tasks immediately.</p>
                    </div>
                </div>
            @endif

            @if($user->isDepositLocked())
                <div class="bg-red-50 border border-red-200 rounded-xl sm:rounded-2xl p-4 sm:p-5 flex items-start sm:items-center space-x-3 sm:space-x-4">
                    <div class="w-10 h-10 bg-red-100 text-red-600 rounded-xl flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-bold text-red-800">🔒 Deposit Locked</p>
                        <p class="text-xs text-red-600 mt-0.5">Your deposit is locked until <strong>{{ $user->deposit_locked_until->format('M d, Y') }}</strong>. You can upgrade or renew after it unlocks.</p>
                    </div>
                </div>
            @elseif($user->hasDeposited())
                <div class="bg-green-50 border border-green-200 rounded-xl sm:rounded-2xl p-4 sm:p-5 flex items-start sm:items-center space-x-3 sm:space-x-4">
                    <div class="w-10 h-10 bg-green-100 text-green-600 rounded-xl flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 11V7a4 4 0 118 0m-4 8v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2z"/></svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-bold text-green-800">🔓 Deposit Unlocked</p>
                        <p class="text-xs text-green-600 mt-0.5">You can renew your current level or upgrade to a higher one.</p>
                    </div>
                    <a href="{{ route('deposits.create') }}" class="shrink-0 px-3 sm:px-4 py-2 bg-green-600 text-white rounded-lg sm:rounded-xl hover:bg-green-700 text-[10px] sm:text-xs font-bold">Deposit Now</a>
                </div>
            @endif

            {{-- Login Streak + Stats Cards Row --}}
            <div class="grid grid-cols-2 sm:grid-cols-2 md:grid-cols-4 gap-3 sm:gap-4">

                {{-- Login Streak Card --}}
                <div class="bg-white rounded-xl sm:rounded-2xl border border-gray-100 p-3 sm:p-5 shadow-sm relative overflow-hidden md:col-span-1">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Login Streak</span>
                        <span class="text-lg">🔥</span>
                    </div>
                    <p class="text-lg sm:text-2xl font-extrabold text-gray-900">{{ $streak }} <span class="text-xs sm:text-sm font-normal text-gray-500">days</span></p>
                    <div class="mt-2.5 space-y-1.5">
                        <div class="flex justify-between text-[10px] text-gray-500">
                            <span>Next milestone: {{ $nextStreakMilestone }} days</span>
                            <span class="font-semibold text-orange-600">+${{ number_format($dailyBonus, 2) }}/day</span>
                        </div>
                        <div class="w-full bg-orange-100 rounded-full h-1.5 overflow-hidden">
                            <div class="bg-orange-500 h-full rounded-full transition-all duration-500" style="width: {{ $streakPercent }}%"></div>
                        </div>
                    </div>
                </div>

                {{-- Total Earned --}}
                <div class="bg-white rounded-xl sm:rounded-2xl border border-gray-100 p-3 sm:p-5 shadow-sm">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Total Earned</span>
                        <div class="w-7 h-7 sm:w-8 sm:h-8 bg-blue-50 text-blue-500 rounded-lg flex items-center justify-center">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"/></svg>
                        </div>
                    </div>
                    <p class="text-base sm:text-xl font-extrabold text-gray-900">{{ currency($stats['total_earned']) }}</p>
                    <p class="text-[10px] text-green-600 font-semibold mt-1.5 flex items-center">
                        <svg class="w-3 h-3 me-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                        {{ $stats['completed_tasks'] }} tasks approved
                    </p>
                </div>

                {{-- Total Withdrawn --}}
                <div class="bg-white rounded-xl sm:rounded-2xl border border-gray-100 p-3 sm:p-5 shadow-sm">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Withdrawn</span>
                        <div class="w-7 h-7 sm:w-8 sm:h-8 bg-red-50 text-red-500 rounded-lg flex items-center justify-center">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
                        </div>
                    </div>
                    <p class="text-base sm:text-xl font-extrabold text-gray-900">{{ currency($stats['total_withdrawn']) }}</p>
                    <p class="text-[10px] text-amber-600 font-semibold mt-1.5 flex items-center">
                        <svg class="w-3 h-3 me-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        {{ $stats['pending_tasks'] }} pending review
                    </p>
                </div>

                {{-- Balance --}}
                <div class="bg-blue-600 rounded-xl sm:rounded-2xl p-3 sm:p-5 text-white shadow-sm">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-[10px] font-bold text-blue-200 uppercase tracking-wider">Balance</span>
                        <div class="w-7 h-7 sm:w-8 sm:h-8 bg-blue-500 text-blue-200 rounded-lg flex items-center justify-center">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                        </div>
                    </div>
                    <p class="text-base sm:text-xl font-extrabold tracking-tight">{{ currency($stats['balance']) }}</p>
                    <p class="text-[10px] text-blue-200 font-semibold mt-1.5 flex items-center">
                        <svg class="w-3 h-3 me-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        {{ $stats['referrals_count'] }} referrals
                    </p>
                </div>
            </div>

            {{-- Available Tasks --}}
            <div class="bg-white rounded-xl sm:rounded-2xl border border-gray-100 shadow-sm">
                <div class="px-4 sm:px-6 py-3 sm:py-4 border-b border-gray-50 flex items-center justify-between">
                    <div>
                        <h3 class="text-sm font-bold text-gray-900">📋 Available Tasks</h3>
                        <p class="text-[11px] text-gray-400 mt-0.5">Complete tasks and earn rewards</p>
                    </div>
                    <a href="{{ route('tasks.index') }}" class="text-xs font-semibold text-blue-600 hover:text-blue-700">View All →</a>
                </div>
                <div class="divide-y divide-gray-50">
                    @forelse($activeTasks as $task)
                        <div class="px-4 sm:px-6 py-3 sm:py-3.5 flex items-center justify-between hover:bg-gray-50/50 transition-colors">
                            <div class="flex items-center space-x-2 sm:space-x-3 min-w-0">
                                <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-lg bg-blue-50 text-blue-500 flex items-center justify-center shrink-0 text-[10px] sm:text-xs font-bold">{{ $task->reward }}</div>
                                <div class="min-w-0">
                                    <p class="text-sm font-semibold text-gray-900 truncate">{{ $task->title }}</p>
                                    <p class="text-[11px] text-gray-400">{{ $task->category ?? 'General' }} • {{ $task->estimated_time ?? 'Quick task' }}</p>
                                </div>
                            </div>
                            <a href="{{ route('tasks.show', $task) }}" class="shrink-0 px-3.5 py-1.5 text-xs font-semibold text-blue-600 bg-blue-50 hover:bg-blue-100 rounded-xl transition-colors">
                                Start
                            </a>
                        </div>
                    @empty
                        <div class="px-6 py-8 text-center">
                            <p class="text-sm text-gray-400">No tasks available right now. Check back later!</p>
                        </div>
                    @endforelse
                </div>
            </div>

            {{-- Chart --}}
            <div class="bg-white rounded-xl sm:rounded-2xl border border-gray-100 p-4 sm:p-6 shadow-sm">
                <div class="flex items-center justify-between mb-4 sm:mb-5">
                    <div>
                        <h3 class="text-sm font-bold text-gray-900">Savings & Expenditure</h3>
                        <p class="text-[11px] text-gray-400 mt-0.5">Monthly earnings vs withdrawals</p>
                    </div>
                    <div class="flex items-center space-x-3 sm:space-x-4 text-[11px] font-semibold text-gray-500">
                        <div class="flex items-center space-x-1.5">
                            <span class="w-2.5 h-2.5 rounded-full bg-blue-600"></span>
                            <span class="hidden sm:inline">Earnings</span>
                            <span class="sm:hidden">Earn</span>
                        </div>
                        <div class="flex items-center space-x-1.5">
                            <span class="w-2.5 h-2.5 rounded-full bg-gray-300"></span>
                            <span class="hidden sm:inline">Withdrawals</span>
                            <span class="sm:hidden">Wd</span>
                        </div>
                    </div>
                </div>
                <div class="relative h-48 sm:h-64 w-full">
                    <canvas id="savingsExpenditureChart" class="w-full h-full"></canvas>
                </div>
            </div>

            {{-- Recent Transactions --}}
            <div class="bg-white rounded-xl sm:rounded-2xl border border-gray-100 shadow-sm">
                <div class="px-4 sm:px-6 py-3 sm:py-4 border-b border-gray-50 flex items-center justify-between">
                    <div>
                        <h3 class="text-sm font-bold text-gray-900">Recent Transactions</h3>
                        <p class="text-[11px] text-gray-400 mt-0.5 hidden sm:block">Latest wallet activity</p>
                    </div>
                    <a href="{{ route('transactions.index') }}" class="px-3 sm:px-4 py-1.5 text-[10px] sm:text-xs font-semibold text-blue-600 bg-blue-50 hover:bg-blue-100 rounded-lg sm:rounded-xl transition-all">
                        View All
                    </a>
                </div>
                <div class="divide-y divide-gray-50">
                    @forelse($stats['recent_transactions'] as $transaction)
                        <div class="px-4 sm:px-6 py-3 sm:py-3.5 flex items-center justify-between hover:bg-gray-50/50 transition-colors">
                            <div class="flex items-center space-x-2 sm:space-x-3 min-w-0">
                                <div class="w-8 h-8 sm:w-9 sm:h-9 rounded-lg sm:rounded-xl flex items-center justify-center shrink-0
                                    @if(in_array($transaction->type, ['deposit', 'referral_bonus', 'festive_bonus', 'task_reward', 'daily_bonus'])) bg-green-50 text-green-600
                                    @else bg-red-50 text-red-500 @endif">
                                    @if($transaction->type === 'deposit')
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                    @elseif($transaction->type === 'daily_bonus')
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.66 11.2c-.23-.3-.51-.56-.77-.82-.67-.6-1.43-1.03-2.07-1.66C13.33 7.26 13 4.85 13.95 3c-1.04.23-1.87.75-2.58 1.32-2.59 2.08-3.61 5.75-2.39 8.9.04.1.08.2.08.33 0 .22-.15.42-.35.5-.23.1-.47.04-.66-.12-.06-.05-.1-.1-.14-.17-1.13-1.43-1.31-3.48-.55-5.12C5.78 10 4.87 12.3 5 14.47c.06.5.12 1 .29 1.5.14.6.41 1.2.71 1.73 1.08 1.73 2.95 2.97 4.96 3.22 2.14.27 4.43-.12 5.93-1.6 1.66-1.64 2.26-4.27.77-8.12z"/></svg>
                                    @elseif($transaction->type === 'task_reward')
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                                    @elseif(in_array($transaction->type, ['referral_bonus', 'festive_bonus']))
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    @else
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/></svg>
                                    @endif
                                </div>
                                <div class="min-w-0">
                                    <p class="text-sm font-semibold text-gray-900 truncate">{{ ucwords(str_replace('_', ' ', $transaction->type)) }}</p>
                                    <p class="text-[11px] text-gray-400">{{ $transaction->created_at->format('M d, Y • h:i A') }}</p>
                                </div>
                            </div>
                            <div class="text-right shrink-0">
                                <p class="text-sm font-bold tracking-tight
                                    @if(in_array($transaction->type, ['deposit', 'referral_bonus', 'festive_bonus', 'task_reward', 'daily_bonus'])) text-green-600
                                    @else text-red-500 @endif">
                                    @if(in_array($transaction->type, ['deposit', 'referral_bonus', 'festive_bonus', 'task_reward', 'daily_bonus']))+@endif{{ currency($transaction->amount) }}
                                </p>
                                <span class="inline-flex items-center text-[10px] font-bold px-2 py-0.5 rounded-full mt-1
                                    @if($transaction->status === 'completed') bg-green-50 text-green-600
                                    @elseif($transaction->status === 'pending') bg-amber-50 text-amber-600
                                    @else bg-red-50 text-red-600 @endif">
                                    {{ ucfirst($transaction->status) }}
                                </span>
                            </div>
                        </div>
                    @empty
                        <div class="px-4 sm:px-6 py-8 sm:py-10 text-center">
                            <div class="w-8 h-8 sm:w-10 sm:h-10 bg-gray-50 text-gray-300 rounded-lg sm:rounded-xl flex items-center justify-center mx-auto mb-2 sm:mb-3">
                                <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </div>
                            <p class="text-sm font-medium text-gray-400">No transactions yet</p>
                            <a href="{{ route('deposits.create') }}" class="text-xs font-bold text-blue-600 hover:text-blue-700 mt-2 inline-block">Make a deposit to begin</a>
                        </div>
                    @endforelse
                </div>
            </div>

        </div>

        {{-- ================= SIDEBAR (Right - 1/4) ================= --}}
        <div class="lg:col-span-1 space-y-4 sm:space-y-6">

            {{-- Balance Overview Card --}}
            <div class="bg-gradient-to-br from-blue-600 via-indigo-600 to-indigo-800 text-white rounded-2xl sm:rounded-3xl p-4 sm:p-6 shadow-xl relative overflow-hidden">
                <div class="absolute right-0 bottom-0 opacity-10 translate-x-4 translate-y-4">
                    <svg class="w-32 h-32" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 17h-2v-2h2v2zm2.07-7.75l-.9.92C13.45 12.9 13 13.5 13 15h-2v-.5c0-1.1.45-2.1 1.17-2.83l1.24-1.26c.37-.36.59-.86.59-1.41 0-1.1-.9-2-2-2s-2 .9-2 2H7c0-2.76 2.24-5 5-5s5 2.24 5 5c0 1.04-.42 1.99-1.07 2.75z"/></svg>
                </div>
                <span class="text-[10px] font-semibold text-blue-100/80 uppercase tracking-wider">Available Balance</span>
                <h3 class="text-2xl sm:text-3xl font-extrabold mt-1 tracking-tight">{{ currency($stats['balance']) }}</h3>

                {{-- Level Progress --}}
                @if($levelProgress)
                    <div class="mt-5 space-y-2">
                        <div class="flex justify-between text-[11px] text-blue-200">
                            <span>Level {{ $levelProgress['current']->level }} → Level {{ $levelProgress['next']->level }}</span>
                            <span>{{ $user->deposit_amount > 0 ? min(100, round(($user->deposit_amount / $levelProgress['next']->deposit_amount) * 100)) : 0 }}%</span>
                        </div>
                        <div class="w-full bg-blue-900/50 rounded-full h-2 overflow-hidden">
                            <div class="bg-white h-full rounded-full transition-all duration-500" style="width: {{ $user->deposit_amount > 0 ? min(100, ($user->deposit_amount / $levelProgress['next']->deposit_amount) * 100) : 0 }}%"></div>
                        </div>
                        <p class="text-[10px] text-blue-200/70">Deposit {{ currency($levelProgress['next']->deposit_amount) }} to reach Level {{ $levelProgress['next']->level }}</p>
                    </div>
                @else
                    <div class="mt-5 pt-4 border-t border-blue-500/30">
                        <div class="flex items-center justify-between text-xs text-blue-200">
                            <span>🏆 Max Level Reached</span>
                            <span class="font-bold text-white">100%</span>
                        </div>
                    </div>
                @endif

                <div class="mt-4 pt-4 border-t border-blue-500/30 flex items-center justify-between text-xs text-blue-200">
                    <span>Total Earned</span>
                    <span class="font-bold text-white">{{ currency($stats['total_earned']) }}</span>
                </div>
            </div>

            {{-- Quick Actions --}}
            <div class="grid grid-cols-4 sm:grid-cols-2 gap-2 sm:gap-3">
                <a href="{{ route('tasks.index') }}" class="bg-white rounded-xl sm:rounded-2xl border border-gray-100 p-2 sm:p-4 shadow-sm hover:shadow-md hover:border-blue-100 transition-all text-center">
                    <div class="w-7 h-7 sm:w-10 sm:h-10 bg-blue-50 text-blue-500 rounded-lg sm:rounded-xl flex items-center justify-center mx-auto mb-1 sm:mb-2">
                        <svg class="w-3.5 h-3.5 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                    </div>
                    <p class="text-[9px] sm:text-xs font-bold text-gray-900">Tasks</p>
                </a>
                <a href="{{ route('referrals.index') }}" class="bg-white rounded-xl sm:rounded-2xl border border-gray-100 p-2 sm:p-4 shadow-sm hover:shadow-md hover:border-emerald-100 transition-all text-center">
                    <div class="w-7 h-7 sm:w-10 sm:h-10 bg-emerald-50 text-emerald-500 rounded-lg sm:rounded-xl flex items-center justify-center mx-auto mb-1 sm:mb-2">
                        <svg class="w-3.5 h-3.5 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    </div>
                    <p class="text-[9px] sm:text-xs font-bold text-gray-900">Referrals</p>
                </a>
                <a href="{{ route('deposits.create') }}" class="bg-white rounded-xl sm:rounded-2xl border border-gray-100 p-2 sm:p-4 shadow-sm hover:shadow-md hover:border-purple-100 transition-all text-center">
                    <div class="w-7 h-7 sm:w-10 sm:h-10 bg-purple-50 text-purple-500 rounded-lg sm:rounded-xl flex items-center justify-center mx-auto mb-1 sm:mb-2">
                        <svg class="w-3.5 h-3.5 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    </div>
                    <p class="text-[9px] sm:text-xs font-bold text-gray-900">Deposit</p>
                </a>
                <a href="{{ route('withdrawals.create') }}" class="bg-white rounded-xl sm:rounded-2xl border border-gray-100 p-2 sm:p-4 shadow-sm hover:shadow-md hover:border-amber-100 transition-all text-center">
                    <div class="w-7 h-7 sm:w-10 sm:h-10 bg-amber-50 text-amber-500 rounded-lg sm:rounded-xl flex items-center justify-center mx-auto mb-1 sm:mb-2">
                        <svg class="w-3.5 h-3.5 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
                    </div>
                    <p class="text-[9px] sm:text-xs font-bold text-gray-900">Withdraw</p>
                </a>
            </div>

            {{-- Referral Widget --}}
            <div class="bg-gradient-to-br from-emerald-500 to-green-700 rounded-2xl sm:rounded-3xl p-4 sm:p-6 shadow-lg relative overflow-hidden text-white">
                <div class="absolute right-0 bottom-0 opacity-10 translate-x-2 translate-y-2">
                    <svg class="w-36 h-36" fill="currentColor" viewBox="0 0 24 24"><path d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                </div>
                <div class="relative z-10">
                    <div class="flex items-center justify-between mb-3">
                        <h3 class="text-sm font-bold text-emerald-100 uppercase tracking-wider">My Referral</h3>
                        <span class="text-xl">🤝</span>
                    </div>
                    <div class="flex items-end space-x-4">
                        <div>
                            <p class="text-3xl font-extrabold">{{ $stats['referrals_count'] }}</p>
                            <p class="text-[11px] text-emerald-200 mt-0.5">Total Referrals</p>
                        </div>
                        <div class="border-l border-emerald-400/30 pl-4">
                            <p class="text-2xl font-extrabold">{{ currency($stats['referral_earnings']) }}</p>
                            <p class="text-[11px] text-emerald-200 mt-0.5">Bonus Earned</p>
                        </div>
                    </div>
                    <div class="mt-4 flex items-center text-[11px] text-emerald-100 bg-emerald-600/50 rounded-full px-3 py-1.5 w-max">
                        <svg class="w-3.5 h-3.5 me-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                        <span>Earn {{ currency(App\Models\AdminSetting::getValue('referral_commission_rate', 10)) }}% per referral deposit</span>
                    </div>
                    <div class="mt-4">
                        <p class="text-[10px] text-emerald-200 mb-1">Your referral link:</p>
                        <div class="flex space-x-2">
                            <input type="text" readonly value="{{ route('register', ['ref' => $user->referral_code]) }}"
                                   class="flex-1 bg-white border border-emerald-300 rounded-xl px-3 py-2 text-xs text-gray-900 font-medium focus:outline-none"
                                   onclick="this.select()">
                            <button onclick="navigator.clipboard.writeText('{{ route('register', ['ref' => $user->referral_code]) }}')"
                                    class="px-3 py-2 bg-white text-gray-700 rounded-xl hover:bg-gray-100 transition-colors" title="Copy">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Badges Progress --}}
            <div class="bg-white rounded-xl sm:rounded-2xl border border-gray-100 p-4 sm:p-5 shadow-sm">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-sm font-bold text-gray-900">🏆 Achievements</h3>
                    <a href="{{ route('leaderboard.index') }}" class="text-[10px] font-semibold text-blue-600 hover:text-blue-700">View All</a>
                </div>
                <div class="space-y-3">
                    @foreach($allBadges as $type => $badges)
                        @php
                            $unearned = $badges->filter(fn($b) => !in_array($b->id, $userBadges));
                            $earned = $badges->filter(fn($b) => in_array($b->id, $userBadges));
                        @endphp
                        @if($unearned->isNotEmpty() || $earned->isNotEmpty())
                            <div>
                                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1.5">{{ $categoryLabels[$type] ?? $type }}</p>
                                <div class="flex flex-wrap gap-1.5">
                                    @foreach($earned as $badge)
                                        <span class="inline-flex items-center space-x-1 px-2 py-1 bg-green-50 text-green-700 rounded-lg text-[10px] font-semibold" title="{{ $badge->description }}">
                                            <span>{{ $badge->icon }}</span>
                                            <span>{{ $badge->name }}</span>
                                        </span>
                                    @endforeach
                                    @foreach($unearned->take(2) as $badge)
                                        <span class="inline-flex items-center space-x-1 px-2 py-1 bg-gray-50 text-gray-400 rounded-lg text-[10px] font-semibold opacity-60" title="{{ $badge->description }}">
                                            <span>{{ $badge->icon }}</span>
                                            <span>{{ $badge->name }}</span>
                                        </span>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>

            {{-- Festive Programs --}}
            @if($activeFestivePrograms->count() > 0)
                @foreach($activeFestivePrograms as $program)
                    <div class="bg-gradient-to-br from-amber-500 via-orange-500 to-red-600 rounded-2xl sm:rounded-3xl text-white shadow-lg relative overflow-hidden">
                        @if($program->banner)
                            <img src="{{ asset('storage/' . $program->banner) }}" alt="{{ $program->title }}" class="w-full h-24 sm:h-28 object-cover opacity-60">
                        @endif
                        <div class="p-4 sm:p-5 @if($program->banner) -mt-8 sm:-mt-10 relative z-10 bg-gradient-to-t from-black/70 to-transparent pt-8 sm:pt-10 @endif">
                            <div class="flex items-center space-x-2 mb-2">
                                <span class="text-lg">🎉</span>
                                <h3 class="font-extrabold text-sm">{{ $program->title }}</h3>
                            </div>
                            <p class="text-[11px] text-amber-100 leading-relaxed">{{ $program->description }}</p>
                            <div class="mt-3 flex items-center justify-between text-[10px] text-amber-200">
                                <span>Ends: {{ $program->end_date->format('M d, Y') }}</span>
                                <span class="font-bold uppercase tracking-wider bg-white/10 px-2 py-0.5 rounded-full">Active</span>
                            </div>
                        </div>
                    </div>
                @endforeach
            @endif

        </div>

    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const ctx = document.getElementById('savingsExpenditureChart').getContext('2d');
        
        // Custom gradient fills
        const blueGradient = ctx.createLinearGradient(0, 0, 0, 240);
        blueGradient.addColorStop(0, 'rgba(37, 99, 235, 0.2)');
        blueGradient.addColorStop(1, 'rgba(37, 99, 235, 0.001)');
        
        // Mock historical data mapping
        const labels = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'];
        const earningsData = [24, 28, 25, 29, 26, 28]; // Earnings path matching screenshot curve
        const spendData = [27, 26, 29, 27, 25, 24]; // Spend path matching screenshot curve

        const chart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [
                    {
                        label: 'Earnings',
                        data: earningsData,
                        borderColor: '#2563eb', // Royal Blue
                        borderWidth: 2.5,
                        pointBackgroundColor: '#2563eb',
                        pointBorderColor: '#fff',
                        pointHoverRadius: 6,
                        pointRadius: 0,
                        tension: 0.45,
                        fill: true,
                        backgroundColor: blueGradient
                    },
                    {
                        label: 'Withdrawals',
                        data: spendData,
                        borderColor: '#cbd5e1', // Gray / Slate
                        borderWidth: 2,
                        pointBackgroundColor: '#cbd5e1',
                        pointBorderColor: '#fff',
                        pointHoverRadius: 5,
                        pointRadius: 0,
                        tension: 0.45,
                        fill: false
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false // Using custom legends in HTML
                    },
                    tooltip: {
                        backgroundColor: '#1e3a8a', // Dark blue background matching screenshot
                        titleFont: { size: 11, weight: 'bold', family: 'Inter' },
                        bodyFont: { size: 12, weight: 'bold', family: 'Inter' },
                        padding: 10,
                        cornerRadius: 12,
                        displayColors: false,
                        callbacks: {
                            label: function(context) {
                                let label = context.dataset.label || '';
                                if (label) {
                                    label += ': ';
                                }
                                if (context.parsed.y !== null) {
                                    // Scale to match screenshot billions/millions values
                                    label += '$' + (context.parsed.y * 1000000).toLocaleString('en-US') + '.00';
                                }
                                return label;
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        grid: {
                            display: false
                        },
                        border: {
                            display: false
                        },
                        ticks: {
                            color: '#94a3b8',
                            font: {
                                family: 'Inter',
                                size: 11
                            }
                        }
                    },
                    y: {
                        grid: {
                            color: 'rgba(241, 245, 249, 0.8)',
                            drawTicks: false
                        },
                        border: {
                            display: false
                        },
                        ticks: {
                            color: '#94a3b8',
                            font: {
                                family: 'Inter',
                                size: 11
                            },
                            callback: function(value) {
                                return value;
                            }
                        }
                    }
                }
            }
        });
    });
</script>
@endpush