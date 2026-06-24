@extends('layouts.app')

@section('title', 'Transaction History')

@section('header')
<div>
    <h1 class="text-2xl font-bold text-gray-900">Transaction History</h1>
    <p class="text-sm text-gray-500 mt-1">View all your financial activity</p>
</div>
@endsection

@section('content')
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100">
        <!-- Filters -->
        <div class="px-6 py-4 border-b border-gray-100">
            <form method="GET" class="flex items-center space-x-4">
                <select name="type" class="rounded-lg border-gray-300 text-sm">
                    <option value="">All Types</option>
                    <option value="deposit" @if(request('type') === 'deposit') selected @endif>Deposit</option>
                    <option value="payout" @if(request('type') === 'payout') selected @endif>Payout</option>
                    <option value="task_reward" @if(request('type') === 'task_reward') selected @endif>Task Reward</option>
                    <option value="referral_bonus" @if(request('type') === 'referral_bonus') selected @endif>Referral Bonus</option>
                    <option value="festive_bonus" @if(request('type') === 'festive_bonus') selected @endif>Festive Bonus</option>
                </select>
                <select name="status" class="rounded-lg border-gray-300 text-sm">
                    <option value="">All Status</option>
                    <option value="completed" @if(request('status') === 'completed') selected @endif>Completed</option>
                    <option value="pending" @if(request('status') === 'pending') selected @endif>Pending</option>
                    <option value="failed" @if(request('status') === 'failed') selected @endif>Failed</option>
                </select>
                <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 text-sm font-medium">Filter</button>
                @if(request()->hasAny(['type', 'status']))
                    <a href="{{ route('transactions.index') }}" class="text-sm text-gray-500 hover:text-gray-700">Clear</a>
                @endif
            </form>
        </div>

        <div class="divide-y divide-gray-100">
            @forelse($transactions as $transaction)
                <div class="px-6 py-4 flex items-center justify-between hover:bg-gray-50">
                    <div class="flex items-center space-x-4">
                        <div class="w-10 h-10 rounded-xl flex items-center justify-center
                            @if(in_array($transaction->type, ['deposit', 'referral_bonus', 'festive_bonus', 'task_reward'])) bg-green-100
                            @else bg-red-100 @endif">
                            @if($transaction->type === 'deposit')
                                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                            @elseif($transaction->type === 'task_reward')
                                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            @elseif($transaction->type === 'referral_bonus' || $transaction->type === 'festive_bonus')
                                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            @else
                                <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/></svg>
                            @endif
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-900">{{ ucwords(str_replace('_', ' ', $transaction->type)) }}</p>
                            <p class="text-xs text-gray-500">{{ $transaction->created_at->format('M d, Y g:i A') }}</p>
                            @if($transaction->description)
                                <p class="text-xs text-gray-400 mt-0.5">{{ Str::limit($transaction->description, 60) }}</p>
                            @endif
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="text-sm font-semibold @if(in_array($transaction->type, ['deposit', 'referral_bonus', 'festive_bonus', 'task_reward'])) text-green-600 @else text-red-600 @endif">
                            @if(in_array($transaction->type, ['deposit', 'referral_bonus', 'festive_bonus', 'task_reward']))+@endif{{ currency($transaction->amount) }}
                        </p>
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium
                            @if($transaction->status === 'completed') bg-green-100 text-green-700
                            @elseif($transaction->status === 'pending') bg-amber-100 text-amber-700
                            @else bg-red-100 text-red-700 @endif">
                            {{ ucfirst($transaction->status) }}
                        </span>
                    </div>
                </div>
            @empty
                <div class="px-6 py-12 text-center">
                    <svg class="w-12 h-12 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                    <p class="text-gray-500">No transactions found</p>
                </div>
            @endforelse
        </div>

        @if($transactions->hasPages())
            <div class="px-6 py-3 border-t border-gray-100">
                {{ $transactions->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
