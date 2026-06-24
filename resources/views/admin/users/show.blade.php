@extends('admin.layouts.admin')

@section('title', 'User: ' . $user->display_name)

@section('content')
<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    {{-- Header --}}
    <div class="flex items-center justify-between mb-6">
        <div class="flex items-center space-x-4">
            <a href="{{ route('admin.users.index') }}" class="text-gray-400 hover:text-gray-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            </a>
            <div class="w-12 h-12 bg-indigo-100 rounded-full flex items-center justify-center">
                <span class="text-xl font-bold text-indigo-600">{{ substr($user->name, 0, 1) }}</span>
            </div>
            <div>
                <h1 class="text-xl font-bold text-gray-900">{{ $user->name }}</h1>
                <p class="text-sm text-gray-500">{{ $user->display_name }} · {{ $user->phone }}</p>
            </div>
            @if($user->is_admin)
                <span class="px-2 py-0.5 rounded-full text-xs font-bold bg-red-100 text-red-700">ADMIN</span>
            @endif
        </div>
        <a href="{{ route('admin.users.edit', $user) }}" class="px-4 py-2 bg-indigo-600 text-white rounded-xl hover:bg-indigo-700 text-sm font-medium">Edit User</a>
    </div>

    {{-- Status Badges Row --}}
    <div class="flex flex-wrap items-center gap-3 mb-6">
        <span class="px-3 py-1 rounded-full text-xs font-medium
            @if($user->status === 'active') bg-green-100 text-green-700
            @elseif($user->status === 'suspended') bg-amber-100 text-amber-700
            @else bg-red-100 text-red-700 @endif">
            {{ ucfirst($user->status) }}
        </span>
        @if($user->is_probation)
            <span class="px-3 py-1 rounded-full text-xs font-medium bg-amber-100 text-amber-700">⏳ Probation until {{ $user->probation_ends_at?->format('M d, Y') }}</span>
        @else
            <span class="px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-700">✅ Probation Complete</span>
        @endif
        @if($user->hasDeposited())
            @if($user->isDepositLocked())
                <span class="px-3 py-1 rounded-full text-xs font-medium bg-red-100 text-red-700">🔒 Deposit locked until {{ $user->deposit_locked_until?->format('M d, Y') }}</span>
            @else
                <span class="px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-700">🔓 Deposit unlocked</span>
            @endif
        @else
            <span class="px-3 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-600">No deposit yet</span>
        @endif
    </div>

    {{-- Stats Grid --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
            <p class="text-xs text-gray-500">Balance</p>
            <p class="text-lg font-bold text-gray-900">{{ currency($user->balance) }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
            <p class="text-xs text-gray-500">Total Earned</p>
            <p class="text-lg font-bold text-green-600">{{ currency($stats['total_earned']) }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
            <p class="text-xs text-gray-500">Total Withdrawn</p>
            <p class="text-lg font-bold text-amber-600">{{ currency($stats['total_withdrawn']) }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
            <p class="text-xs text-gray-500">Deposit Amount</p>
            <p class="text-lg font-bold text-indigo-600">{{ currency($user->deposit_amount) }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        {{-- Left Column --}}
        <div class="lg:col-span-2 space-y-8">
            {{-- User Info --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">📋 Account Details</h2>
                <dl class="grid grid-cols-2 gap-4 text-sm">
                    <div>
                        <dt class="text-gray-500">Full Name</dt>
                        <dd class="font-medium text-gray-900">{{ $user->name }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500">@Username</dt>
                        <dd class="font-medium text-gray-900">{{ $user->username }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500">Phone</dt>
                        <dd class="font-medium text-gray-900">{{ $user->phone }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500">Level</dt>
                        <dd class="font-medium text-gray-900">{{ $user->level ? 'Level ' . $user->level->level : 'None' }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500">Joined</dt>
                        <dd class="font-medium text-gray-900">{{ $user->created_at->format('M d, Y') }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500">Login Streak</dt>
                        <dd class="font-medium text-gray-900">🔥 {{ $stats['login_streak'] }} days</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500">Tasks Completed</dt>
                        <dd class="font-medium text-gray-900">{{ $stats['tasks_completed'] }} approved / {{ $stats['tasks_pending'] }} pending</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500">Referred By</dt>
                        <dd class="font-medium text-gray-900">{{ $user->referrer?->display_name ?? '—' }}</dd>
                    </div>
                </dl>
            </div>

            {{-- Bank Details --}}
            @if($user->bank_name || $user->bank_account_number)
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">🏦 Bank Details</h2>
                <dl class="grid grid-cols-2 gap-4 text-sm">
                    <div>
                        <dt class="text-gray-500">Bank</dt>
                        <dd class="font-medium text-gray-900">{{ $user->bank_name }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500">Account Number</dt>
                        <dd class="font-medium text-gray-900">{{ $user->bank_account_number }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500">Account Name</dt>
                        <dd class="font-medium text-gray-900">{{ $user->bank_account_name }}</dd>
                    </div>
                </dl>
            </div>
            @endif

            {{-- Recent Transactions --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">💰 Recent Transactions</h2>
                @if($recentTransactions->isEmpty())
                    <p class="text-sm text-gray-500">No transactions yet.</p>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="text-left text-gray-500 border-b border-gray-100">
                                    <th class="pb-2 font-medium">Date</th>
                                    <th class="pb-2 font-medium">Type</th>
                                    <th class="pb-2 font-medium text-right">Amount</th>
                                    <th class="pb-2 font-medium text-right">Balance</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                @foreach($recentTransactions as $tx)
                                    <tr>
                                        <td class="py-2 text-gray-500">{{ $tx->created_at->format('M d, H:i') }}</td>
                                        <td class="py-2">
                                            <span class="px-2 py-0.5 rounded-full text-xs font-medium
                                                @if(in_array($tx->type, ['deposit', 'task_reward', 'referral_bonus', 'festive_bonus', 'daily_bonus', 'leaderboard_bonus'])) bg-green-100 text-green-700
                                                @elseif($tx->type === 'payout') bg-amber-100 text-amber-700
                                                @else bg-gray-100 text-gray-600 @endif">
                                                {{ ucwords(str_replace('_', ' ', $tx->type)) }}
                                            </span>
                                        </td>
                                        <td class="py-2 text-right font-medium {{ in_array($tx->type, ['deposit', 'task_reward', 'referral_bonus', 'festive_bonus', 'daily_bonus', 'leaderboard_bonus']) ? 'text-green-600' : 'text-red-500' }}">
                                            {{ in_array($tx->type, ['deposit', 'task_reward', 'referral_bonus', 'festive_bonus', 'daily_bonus', 'leaderboard_bonus']) ? '+' : '-' }}{{ currency($tx->amount) }}
                                        </td>
                                        <td class="py-2 text-right text-gray-500">{{ currency($tx->balance_after) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>

        {{-- Right Column --}}
        <div class="space-y-8">
            {{-- Referral Stats --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">🤝 Referrals</h2>
                <div class="grid grid-cols-2 gap-3 mb-4">
                    <div class="bg-indigo-50 rounded-xl p-3 text-center">
                        <p class="text-2xl font-bold text-indigo-600">{{ $stats['referral_count'] }}</p>
                        <p class="text-xs text-gray-500">Referred</p>
                    </div>
                    <div class="bg-green-50 rounded-xl p-3 text-center">
                        <p class="text-2xl font-bold text-green-600">{{ currency($stats['commission_earned']) }}</p>
                        <p class="text-xs text-gray-500">Commissions</p>
                    </div>
                </div>
                @if($referrals->isNotEmpty())
                    <p class="text-xs font-medium text-gray-500 mb-2">Referred Users</p>
                    <div class="space-y-2">
                        @foreach($referrals as $ref)
                            <div class="flex items-center justify-between text-sm">
                                <span class="text-gray-900">{{ $ref->display_name }}</span>
                                <span class="text-xs text-gray-500">{{ $ref->level ? 'Lvl ' . $ref->level->level : '—' }}</span>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- Badges --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">🏅 Badges</h2>
                @if($user->badges->isEmpty())
                    <p class="text-sm text-gray-500">No badges earned yet.</p>
                @else
                    <div class="space-y-2">
                        @foreach($user->badges as $badge)
                            <div class="flex items-center space-x-2 text-sm">
                                <span class="text-lg">{{ $badge->icon }}</span>
                                <div>
                                    <p class="font-medium text-gray-900">{{ $badge->name }}</p>
                                    <p class="text-xs text-gray-500">{{ $badge->description }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- Referral Code --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-2">🔗 Referral Code</h2>
                <p class="text-sm font-mono text-gray-900 bg-gray-50 rounded-lg px-3 py-2">{{ $user->referral_code }}</p>
            </div>
        </div>
    </div>
</div>
@endsection
