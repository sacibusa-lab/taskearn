@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="max-w-[1600px] mx-auto px-4 sm:px-6 lg:px-8 py-8">

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
        {{-- Image --}}
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
    <div class="grid grid-cols-1 lg:grid-cols-4 xl:grid-cols-5 gap-8">
        
        {{-- ================= LEFT SIDEBAR (Desktop only) ================= --}}
        <div class="hidden lg:block lg:col-span-1 xl:col-span-1 space-y-6">
            {{-- Total Balance Widget --}}
            <div class="bg-gradient-to-br from-blue-600 via-indigo-600 to-indigo-800 text-white rounded-3xl p-6 shadow-xl relative overflow-hidden">
                <div class="absolute right-0 bottom-0 opacity-10 translate-x-4 translate-y-4">
                    <svg class="w-32 h-32" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 17h-2v-2h2v2zm2.07-7.75l-.9.92C13.45 12.9 13 13.5 13 15h-2v-.5c0-1.1.45-2.1 1.17-2.83l1.24-1.26c.37-.36.59-.86.59-1.41 0-1.1-.9-2-2-2s-2 .9-2 2H7c0-2.76 2.24-5 5-5s5 2.24 5 5c0 1.04-.42 1.99-1.07 2.75z"/></svg>
                </div>
                
                <span class="text-xs font-semibold text-blue-100/80 uppercase tracking-wider">Your Balance</span>
                <h3 class="text-3xl font-extrabold mt-2 tracking-tight">{{ currency($stats['balance']) }}</h3>
                
                {{-- Progress bar --}}
                @if($levelProgress)
                    <div class="mt-6 space-y-2">
                        <div class="flex justify-between text-xs text-blue-200">
                            <span>Level Progress</span>
                            <span>{{ Auth::user()->deposit_amount > 0 ? min(100, round((Auth::user()->deposit_amount / $levelProgress['next']->deposit_amount) * 100)) : 0 }}%</span>
                        </div>
                        <div class="w-full bg-blue-900/50 rounded-full h-1.5 overflow-hidden">
                            <div class="bg-white h-full rounded-full transition-all duration-500" style="width: {{ Auth::user()->deposit_amount > 0 ? min(100, (Auth::user()->deposit_amount / $levelProgress['next']->deposit_amount) * 100) : 0 }}%"></div>
                        </div>
                    </div>
                @else
                    <div class="mt-6 pt-2 border-t border-blue-500/30 flex items-center justify-between text-xs text-blue-200">
                        <span>Max level unlocked</span>
                        <span>100%</span>
                    </div>
                @endif

                <div class="mt-5 pt-4 border-t border-blue-500/30 flex items-center justify-between text-xs text-blue-200">
                    <span>Total Earned</span>
                    <span class="font-bold text-white">{{ currency($stats['total_earned']) }}</span>
                </div>
            </div>
        </div>

        {{-- ================= MAIN PANEL (Middle + Right) ================= --}}
        <div class="col-span-1 lg:col-span-3 xl:col-span-4 grid grid-cols-1 xl:grid-cols-3 gap-8">
            
            {{-- MIDDLE PANEL: Welcome, Metrics, Charts, Recent Activity --}}
            <div class="xl:col-span-2 space-y-8">
                
                {{-- Welcome Banner --}}
                <div class="bg-gradient-to-r from-blue-600 via-indigo-600 to-indigo-700 text-white rounded-3xl p-8 shadow-lg relative overflow-hidden flex items-center justify-between flex-wrap gap-6">
                    <div class="space-y-2 max-w-md z-10">
                        <h1 class="text-2xl md:text-3xl font-extrabold tracking-tight">Welcome, {{ Auth::user()->name }}!</h1>
                        <p class="text-sm text-blue-100/90 leading-relaxed">Track your earnings, complete daily micro-tasks, refer your friends, and grow your digital wallet with ease.</p>
                    </div>
                    <div class="flex items-center space-x-3 z-10">
                        <a href="{{ route('deposits.create') }}" class="px-5 py-3 bg-white text-blue-600 font-bold rounded-2xl hover:bg-blue-50 transition-all text-sm flex items-center space-x-2 shadow-md">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                            <span>Deposit</span>
                        </a>
                        <a href="{{ route('withdrawals.create') }}" class="px-5 py-3 bg-blue-500/20 backdrop-blur border border-blue-400/30 text-white font-bold rounded-2xl hover:bg-blue-500/30 transition-all text-sm flex items-center space-x-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
                            <span>Withdraw</span>
                        </a>
                    </div>
                    {{-- Graphic details --}}
                    <div class="absolute right-0 top-0 opacity-10 -translate-y-8 translate-x-8">
                        <svg class="w-64 h-64" fill="currentColor" viewBox="0 0 24 24"><path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-2 10H7v-2h10v2zm0-4H7V7h10v2zm0 8H7v-2h10v2z"/></svg>
                    </div>
                </div>

                {{-- Probation Notice --}}
                @if(Auth::user()->isOnProbation())
                    <div class="bg-amber-50 border border-amber-200 rounded-3xl p-5 flex items-center space-x-4 shadow-[0_8px_30px_rgb(0,0,0,0.01)]">
                        <div class="w-10 h-10 bg-amber-100 text-amber-700 rounded-xl flex items-center justify-center shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-bold text-amber-800">Account Probation Period</p>
                            <p class="text-xs text-amber-600 mt-0.5">Your trial probation will end in <strong>{{ Auth::user()->probation_ends_at->diffForHumans() }}</strong>. Upgrade your level to unlock higher paying premium tasks immediately.</p>
                        </div>
                    </div>
                @endif

                {{-- Deposit Lock/Unlock Notice --}}
                @if(Auth::user()->isDepositLocked())
                    <div class="bg-red-50 border border-red-200 rounded-3xl p-5 flex items-center space-x-4 shadow-[0_8px_30px_rgb(0,0,0,0.01)]">
                        <div class="w-10 h-10 bg-red-100 text-red-600 rounded-xl flex items-center justify-center shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-bold text-red-800">🔒 Deposit Locked</p>
                            <p class="text-xs text-red-600 mt-0.5">Your deposit is locked until <strong>{{ Auth::user()->deposit_locked_until->format('M d, Y') }}</strong>. You can upgrade or renew after it unlocks.</p>
                        </div>
                    </div>
                @elseif(Auth::user()->hasDeposited())
                    <div class="bg-green-50 border border-green-200 rounded-3xl p-5 flex items-center space-x-4 shadow-[0_8px_30px_rgb(0,0,0,0.01)]">
                        <div class="w-10 h-10 bg-green-100 text-green-600 rounded-xl flex items-center justify-center shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 11V7a4 4 0 118 0m-4 8v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2z"/></svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-bold text-green-800">🔓 Deposit Unlocked</p>
                            <p class="text-xs text-green-600 mt-0.5">You can renew your current level or upgrade to a higher one. Choose your level and deposit now.</p>
                        </div>
                        <a href="{{ route('deposits.create') }}" class="shrink-0 px-4 py-2 bg-green-600 text-white rounded-xl hover:bg-green-700 text-xs font-bold">Deposit Now</a>
                    </div>
                @endif

                {{-- Dashboard Cards Row (3 Cards modeled exactly after Neopay layout) --}}
                <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                    {{-- Total Received Card (White background, subtle text, sparkline metrics) --}}
                    <div class="bg-white rounded-3xl border border-gray-100 p-6 shadow-[0_8px_30px_rgb(0,0,0,0.015)] relative overflow-hidden flex flex-col justify-between">
                        <div>
                            <div class="flex items-center justify-between mb-4">
                                <span class="text-xs font-bold text-gray-400 uppercase tracking-wider">Total Received</span>
                                <div class="w-8 h-8 bg-blue-50 text-blue-500 rounded-full flex items-center justify-center">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"/></svg>
                                </div>
                            </div>
                            <h4 class="text-2xl font-extrabold text-gray-900 tracking-tight">{{ currency($stats['total_earned']) }}</h4>
                        </div>
                        <div class="mt-4 flex items-center text-[10px] text-green-500 font-bold bg-green-50 rounded-full px-2.5 py-1 w-max">
                            <svg class="w-3 h-3 me-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                            <span>+{{ $stats['completed_tasks'] }} Approved Tasks</span>
                        </div>
                    </div>

                    {{-- Total Withdrawn Card (White background, subtle text) --}}
                    <div class="bg-white rounded-3xl border border-gray-100 p-6 shadow-[0_8px_30px_rgb(0,0,0,0.015)] relative overflow-hidden flex flex-col justify-between">
                        <div>
                            <div class="flex items-center justify-between mb-4">
                                <span class="text-xs font-bold text-gray-400 uppercase tracking-wider">Total Withdrawn</span>
                                <div class="w-8 h-8 bg-red-50 text-red-500 rounded-full flex items-center justify-center">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
                                </div>
                            </div>
                            <h4 class="text-2xl font-extrabold text-gray-900 tracking-tight">{{ currency($stats['total_withdrawn']) }}</h4>
                        </div>
                        <div class="mt-4 flex items-center text-[10px] text-amber-600 font-bold bg-amber-50 rounded-full px-2.5 py-1 w-max">
                            <svg class="w-3 h-3 me-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <span>{{ $stats['pending_tasks'] }} Tasks Pending Review</span>
                        </div>
                    </div>

                    {{-- Total Balance Card (Colored background, matching the right balance indicator) --}}
                    <div class="bg-blue-600 text-white rounded-3xl p-6 shadow-[0_8px_30px_rgba(37,99,235,0.15)] relative overflow-hidden flex flex-col justify-between">
                        <div>
                            <div class="flex items-center justify-between mb-4">
                                <span class="text-xs font-bold text-blue-200 uppercase tracking-wider">Total Balance</span>
                                <div class="w-8 h-8 bg-blue-500 text-blue-200 rounded-full flex items-center justify-center">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                                </div>
                            </div>
                            <h4 class="text-2xl font-extrabold tracking-tight">{{ currency($stats['balance']) }}</h4>
                        </div>
                        <div class="mt-4 flex items-center text-[10px] text-blue-100 font-bold bg-blue-700/50 rounded-full px-2.5 py-1 w-max">
                            <svg class="w-3 h-3 me-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            <span>{{ $stats['referrals_count'] }} Active Referrals</span>
                        </div>
                    </div>
                </div>

                {{-- Savings & Expenditure Interactive Chart Card --}}
                <div class="bg-white rounded-3xl border border-gray-100 p-6 shadow-[0_8px_30px_rgb(0,0,0,0.015)]">
                    <div class="flex items-center justify-between mb-6">
                        <div>
                            <h3 class="text-base font-bold text-gray-900">Savings & Expenditure</h3>
                            <p class="text-xs text-gray-400 mt-0.5">Your monthly earnings vs withdrawal flow</p>
                        </div>
                        <div class="flex items-center space-x-4 text-xs font-semibold text-gray-500">
                            <div class="flex items-center space-x-1.5">
                                <span class="w-2.5 h-2.5 rounded-full bg-blue-600"></span>
                                <span>Earnings</span>
                            </div>
                            <div class="flex items-center space-x-1.5">
                                <span class="w-2.5 h-2.5 rounded-full bg-gray-300"></span>
                                <span>Withdrawals</span>
                            </div>
                        </div>
                    </div>
                    
                    {{-- Chart container --}}
                    <div class="relative h-64 w-full">
                        <canvas id="savingsExpenditureChart" class="w-full h-full"></canvas>
                    </div>
                </div>

                {{-- Recent Transactions List View --}}
                <div class="bg-white rounded-3xl border border-gray-100 shadow-[0_8px_30px_rgb(0,0,0,0.015)]">
                    <div class="px-6 py-5 border-b border-gray-50 flex items-center justify-between">
                        <div>
                            <h3 class="text-base font-bold text-gray-900">Recent Transactions</h3>
                            <p class="text-xs text-gray-400 mt-0.5">Summary of your latest wallet operations</p>
                        </div>
                        <a href="{{ route('transactions.index') }}" class="px-4 py-2 text-xs font-semibold text-blue-600 bg-blue-50 hover:bg-blue-100 rounded-xl transition-all duration-200">
                            View All
                        </a>
                    </div>
                    
                    <div class="divide-y divide-gray-50/50">
                        @forelse($stats['recent_transactions'] as $transaction)
                            <div class="px-6 py-4 flex items-center justify-between hover:bg-gray-50/80 transition-colors duration-150">
                                <div class="flex items-center space-x-4 min-w-0">
                                    <div class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0
                                        @if(in_array($transaction->type, ['deposit', 'referral_bonus', 'festive_bonus', 'task_reward'])) bg-green-50 text-green-600
                                        @else bg-red-50 text-red-500 @endif">
                                        @if($transaction->type === 'deposit')
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                        @elseif($transaction->type === 'task_reward')
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                                        @elseif(in_array($transaction->type, ['referral_bonus', 'festive_bonus']))
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        @else
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/></svg>
                                        @endif
                                    </div>
                                    <div class="min-w-0">
                                        <p class="text-sm font-bold text-gray-900 truncate">
                                            {{ ucwords(str_replace('_', ' ', $transaction->type)) }}
                                        </p>
                                        <p class="text-xs text-gray-400 mt-0.5">
                                            {{ $transaction->created_at->format('M d, Y • h:i A') }}
                                        </p>
                                    </div>
                                </div>
                                
                                <div class="text-right shrink-0">
                                    <p class="text-sm font-bold tracking-tight
                                        @if(in_array($transaction->type, ['deposit', 'referral_bonus', 'festive_bonus', 'task_reward'])) text-green-600
                                        @else text-red-500 @endif">
                                        @if(in_array($transaction->type, ['deposit', 'referral_bonus', 'festive_bonus', 'task_reward']))+@endif{{ currency($transaction->amount) }}
                                    </p>
                                    <span class="inline-flex items-center text-[10px] font-bold px-2 py-0.5 rounded-full mt-1.5
                                        @if($transaction->status === 'completed') bg-green-50 text-green-600
                                        @elseif($transaction->status === 'pending') bg-amber-50 text-amber-600
                                        @else bg-red-50 text-red-600 @endif">
                                        {{ ucfirst($transaction->status) }}
                                    </span>
                                </div>
                            </div>
                        @empty
                            <div class="px-6 py-12 text-center">
                                <div class="w-12 h-12 bg-gray-50 text-gray-300 rounded-full flex items-center justify-center mx-auto mb-4">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                </div>
                                <p class="text-sm font-medium text-gray-400">No transactions recorded yet</p>
                                <a href="{{ route('deposits.create') }}" class="text-xs font-bold text-blue-600 hover:text-blue-700 mt-2 inline-block">Make a deposit to begin</a>
                            </div>
                        @endforelse
                    </div>
                </div>

            </div>

            {{-- RIGHT PANEL: Virtual Card, Balance indicator, Contact/Referral Bubbles, Upgrades --}}
            <div class="xl:col-span-1 space-y-8">
                
                {{-- My Referral Card --}}
                <div class="bg-gradient-to-br from-emerald-500 to-green-700 rounded-3xl p-6 shadow-xl relative overflow-hidden text-white">
                    <div class="absolute right-0 bottom-0 opacity-10 translate-x-2 translate-y-2 select-none pointer-events-none">
                        <svg class="w-40 h-40" fill="currentColor" viewBox="0 0 24 24"><path d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    </div>
                    <div class="relative z-10">
                        <h3 class="text-sm font-bold text-emerald-100 uppercase tracking-wider">My Referral</h3>
                        <div class="mt-4 flex items-end space-x-3">
                            <div>
                                <p class="text-3xl font-extrabold">{{ $stats['referrals_count'] }}</p>
                                <p class="text-xs text-emerald-200 mt-0.5">Total Referrals</p>
                            </div>
                            <div class="border-l border-emerald-400/30 pl-3">
                                <p class="text-2xl font-extrabold">{{ currency($stats['referral_earnings']) }}</p>
                                <p class="text-xs text-emerald-200 mt-0.5">Bonus Earned</p>
                            </div>
                        </div>
                        <div class="mt-4 flex items-center text-xs text-emerald-100 bg-emerald-600/50 rounded-full px-3 py-1.5 w-max">
                            <svg class="w-3.5 h-3.5 me-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                            <span>Earn {{ currency(App\Models\AdminSetting::getValue('referral_commission_rate', 10)) }}% per referral deposit</span>
                        </div>
                    </div>
                </div>

                {{-- Card Balance --}}
                <div class="bg-white rounded-3xl border border-gray-100 p-6 shadow-[0_8px_30px_rgb(0,0,0,0.015)] space-y-2">
                    <span class="text-xs font-bold text-gray-400 uppercase tracking-wider">Your Balance</span>
                    <h3 class="text-3xl font-extrabold text-gray-900 tracking-tight">{{ currency($stats['balance']) }}</h3>
                    <div class="pt-2 flex items-center text-xs text-gray-400">
                        <svg class="w-4 h-4 text-blue-500 me-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                        <span>Instant Deposits & Withdrawals enabled</span>
                    </div>
                </div>

                {{-- Referral Contacts (Transaction To) --}}
                <div class="bg-white rounded-3xl border border-gray-100 p-6 shadow-[0_8px_30px_rgb(0,0,0,0.015)]">
                    <h3 class="text-sm font-bold text-gray-900 mb-4">Transaction To</h3>
                    <div class="flex items-center space-x-2 flex-wrap gap-y-3">
                        {{-- Avatars of referrals or system mock contacts --}}
                        @php
                            $colors = ['bg-blue-100 text-blue-600', 'bg-purple-100 text-purple-600', 'bg-emerald-100 text-emerald-600', 'bg-amber-100 text-amber-600', 'bg-pink-100 text-pink-600'];
                            $referralsList = Auth::user()->referrals()->take(4)->get();
                        @endphp
                        
                        @foreach($referralsList as $key => $ref)
                            <div class="w-10 h-10 rounded-full flex items-center justify-center text-xs font-bold border-2 border-white {{ $colors[$key % count($colors)] }} shadow-sm cursor-pointer" title="{{ $ref->name }}">
                                {{ substr($ref->name, 0, 1) }}
                            </div>
                        @endforeach

                        {{-- Mock contacts if no referrals yet --}}
                        @if($referralsList->count() == 0)
                            <div class="w-10 h-10 rounded-full flex items-center justify-center text-xs font-bold border-2 border-white bg-blue-100 text-blue-600 shadow-sm" title="Alex Jones">A</div>
                            <div class="w-10 h-10 rounded-full flex items-center justify-center text-xs font-bold border-2 border-white bg-purple-100 text-purple-600 shadow-sm" title="Sarah Parker">S</div>
                            <div class="w-10 h-10 rounded-full flex items-center justify-center text-xs font-bold border-2 border-white bg-emerald-100 text-emerald-600 shadow-sm" title="David Smith">D</div>
                        @endif

                        <a href="{{ route('referrals.index') }}" class="w-10 h-10 rounded-full bg-gray-50 border-2 border-dashed border-gray-200 hover:border-blue-400 hover:bg-blue-50 text-gray-400 hover:text-blue-500 flex items-center justify-center text-lg font-bold shadow-sm transition-all duration-200">
                            +
                        </a>
                    </div>
                </div>



                {{-- Festive / Active Programs Widget --}}
                @if($activeFestivePrograms->count() > 0)
                    @foreach($activeFestivePrograms as $program)
                        <div class="bg-gradient-to-br from-amber-500 via-orange-500 to-red-600 rounded-3xl text-white shadow-lg relative overflow-hidden">
                            @if($program->banner)
                                <img src="{{ asset('storage/' . $program->banner) }}" alt="{{ $program->title }}" class="w-full h-32 object-cover opacity-60">
                            @endif
                            <div class="p-6 @if($program->banner) -mt-12 relative z-10 bg-gradient-to-t from-black/70 to-transparent pt-14 @endif">
                                <div class="flex items-center space-x-2 mb-3">
                                    <span class="text-lg">🎉</span>
                                    <h3 class="font-extrabold text-sm tracking-tight">{{ $program->title }}</h3>
                                </div>
                                <p class="text-[11px] text-amber-100 leading-relaxed">{{ $program->description }}</p>
                                <div class="pt-2 flex items-center justify-between text-[9px] text-amber-200">
                                    <span>Ends: {{ $program->end_date->format('M d, Y') }}</span>
                                    <span class="font-bold uppercase tracking-wider bg-white/10 px-1.5 py-0.5 rounded">Active</span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @endif

            </div>

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