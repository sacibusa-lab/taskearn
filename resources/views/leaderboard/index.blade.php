@extends('layouts.app')

@section('title', 'Leaderboard')

@section('header')
<div>
    <h1 class="text-2xl font-bold text-gray-900">🏆 Leaderboard</h1>
    <p class="text-sm text-gray-500 mt-1">Top earners and achievers</p>
</div>
@endsection

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    {{-- Period Filter --}}
    <div class="flex items-center space-x-2 mb-6">
        <a href="?period=week" class="px-4 py-2 rounded-xl text-sm font-medium transition-colors {{ $period === 'week' ? 'bg-indigo-600 text-white' : 'bg-white border border-gray-200 text-gray-600 hover:bg-gray-50' }}">This Week</a>
        <a href="?period=month" class="px-4 py-2 rounded-xl text-sm font-medium transition-colors {{ $period === 'month' ? 'bg-indigo-600 text-white' : 'bg-white border border-gray-200 text-gray-600 hover:bg-gray-50' }}">This Month</a>
        <a href="?period=all" class="px-4 py-2 rounded-xl text-sm font-medium transition-colors {{ $period === 'all' ? 'bg-indigo-600 text-white' : 'bg-white border border-gray-200 text-gray-600 hover:bg-gray-50' }}">All Categories</a>
    </div>

    {{-- Top 3 Podium --}}
    @if(count($leaders) >= 3)
        <div class="grid grid-cols-3 gap-4 mb-8">
            @php $top = array_slice($leaders, 0, 3); @endphp
            {{-- 2nd Place --}}
            <div class="text-center pt-6">
                <div class="w-16 h-16 bg-gray-200 rounded-full mx-auto flex items-center justify-center text-2xl font-bold text-gray-600">{{ substr($top[1]['name'], 0, 1) }}</div>
                <p class="text-3xl mt-2">🥈</p>
                <p class="text-sm font-semibold text-gray-900 truncate">{{ $top[1]['name'] }}</p>
                <p class="text-xs text-gray-500">Lvl {{ $top[1]['level'] }}</p>
                <p class="text-sm font-bold text-indigo-600 mt-1">{{ currency($top[1]['amount']) }}</p>
            </div>
            {{-- 1st Place --}}
            <div class="text-center -mt-4">
                <div class="w-20 h-20 bg-yellow-100 rounded-full mx-auto flex items-center justify-center text-3xl font-bold text-yellow-600 ring-4 ring-yellow-300">{{ substr($top[0]['name'], 0, 1) }}</div>
                <p class="text-4xl mt-1">🥇</p>
                <p class="text-sm font-bold text-gray-900 truncate">{{ $top[0]['name'] }}</p>
                <p class="text-xs text-gray-500">Lvl {{ $top[0]['level'] }}</p>
                <p class="text-sm font-bold text-indigo-600 mt-1">{{ currency($top[0]['amount']) }}</p>
                <p class="text-xs text-gray-400 mt-0.5">{{ $top[0]['badges'] }}</p>
            </div>
            {{-- 3rd Place --}}
            <div class="text-center pt-8">
                <div class="w-16 h-16 bg-orange-100 rounded-full mx-auto flex items-center justify-center text-2xl font-bold text-orange-600">{{ substr($top[2]['name'], 0, 1) }}</div>
                <p class="text-3xl mt-2">🥉</p>
                <p class="text-sm font-semibold text-gray-900 truncate">{{ $top[2]['name'] }}</p>
                <p class="text-xs text-gray-500">Lvl {{ $top[2]['level'] }}</p>
                <p class="text-sm font-bold text-indigo-600 mt-1">{{ currency($top[2]['amount']) }}</p>
            </div>
        </div>
    @endif

    {{-- Leaderboard Table --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left text-gray-500 border-b border-gray-100 bg-gray-50">
                        <th class="px-6 py-3 font-medium w-16">Rank</th>
                        <th class="px-6 py-3 font-medium">User</th>
                        <th class="px-6 py-3 font-medium">Level</th>
                        <th class="px-6 py-3 font-medium">Badges</th>
                        <th class="px-6 py-3 font-medium text-right">Earnings</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($leaders as $leader)
                        <tr class="hover:bg-gray-50 {{ $leader['name'] === Auth::user()->display_name ? 'bg-indigo-50' : '' }}">
                            <td class="px-6 py-4">
                                @if($leader['rank'] <= 3)
                                    <span class="text-lg">{{ ['🥇','🥈','🥉'][$leader['rank']-1] }}</span>
                                @else
                                    <span class="text-gray-400 font-bold">#{{ $leader['rank'] }}</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 font-medium text-gray-900">
                                {{ $leader['name'] }}
                            </td>
                            <td class="px-6 py-4 text-gray-500">Level {{ $leader['level'] }}</td>
                            <td class="px-6 py-4 text-sm">{{ $leader['badges'] ?: '—' }}</td>
                            <td class="px-6 py-4 text-right font-bold text-green-600">{{ currency($leader['amount']) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- Your Badges --}}
    <div class="mt-8 bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">🏅 Your Badges</h3>
        @if($userBadges->isEmpty())
            <p class="text-sm text-gray-500">No badges yet. Complete tasks, refer friends, and maintain streaks to earn badges!</p>
        @else
            <div class="flex flex-wrap gap-3">
                @foreach($userBadges as $badge)
                    <div class="inline-flex items-center space-x-2 px-4 py-2 bg-gradient-to-r from-indigo-50 to-purple-50 border border-indigo-100 rounded-xl">
                        <span class="text-xl">{{ $badge->icon }}</span>
                        <div>
                            <p class="text-sm font-bold text-gray-900">{{ $badge->name }}</p>
                            <p class="text-xs text-gray-500">{{ $badge->description }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    {{-- Available Badges --}}
    <div class="mt-6 bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">🎯 Badges to Earn</h3>
        @php
            $categoryLabels = [
                'tasks_completed' => '📋 Tasks',
                'referrals_count' => '🤝 Referrals',
                'deposit_amount' => '💎 Deposits',
                'total_earned' => '💰 Earnings',
                'login_streak' => '🔥 Streaks',
                'early_adopter' => '⏰ Early Adopter',
            ];
        @endphp
        @foreach($allBadges as $type => $badges)
            @php $unearned = $badges->filter(fn($b) => !Auth::user()->badges()->where('badge_id', $b->id)->exists()); @endphp
            @if($unearned->isNotEmpty())
                <div class="mb-4">
                    <h4 class="text-xs font-semibold uppercase tracking-wider text-gray-400 mb-2">{{ $categoryLabels[$type] ?? $type }}</h4>
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                        @foreach($unearned as $badge)
                            <div class="p-3 bg-gray-50 rounded-xl border border-gray-100 opacity-60">
                                <div class="flex items-center space-x-2">
                                    <span class="text-lg">{{ $badge->icon }}</span>
                                    <div>
                                        <p class="text-xs font-semibold text-gray-800">{{ $badge->name }}</p>
                                        <p class="text-[10px] text-gray-400">{{ $badge->description }}</p>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        @endforeach
    </div>
</div>
@endsection
