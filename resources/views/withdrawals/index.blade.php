@extends('layouts.app')

@section('title', 'Withdrawals')

@section('header')
<div class="flex items-center justify-between">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Withdrawals</h1>
        <p class="text-sm text-gray-500 mt-1">Request and track your payouts</p>
    </div>
    <a href="{{ route('withdrawals.create') }}" class="px-4 py-2 bg-indigo-600 text-white rounded-xl hover:bg-indigo-700 text-sm font-medium transition-colors">
        + New Withdrawal
    </a>
</div>
@endsection

@section('content')
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    {{-- Stats --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
            <p class="text-xs text-gray-500">Balance</p>
            <p class="text-lg font-bold text-gray-900">{{ currency($stats['balance']) }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
            <p class="text-xs text-gray-500">Total Withdrawn</p>
            <p class="text-lg font-bold text-gray-900">{{ currency($stats['total_withdrawn']) }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
            <p class="text-xs text-gray-500">Min / Max</p>
            <p class="text-lg font-bold text-gray-900">{{ currency($stats['min_withdrawal']) }} - {{ currency($stats['max_withdrawal']) }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
            <p class="text-xs text-gray-500">Pending Requests</p>
            <p class="text-lg font-bold text-amber-600">{{ $stats['pending_count'] }}</p>
        </div>
    </div>

    {{-- Withdrawals List --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100">
        <div class="px-6 py-4 border-b border-gray-100">
            <h2 class="text-lg font-semibold text-gray-900">Withdrawal History</h2>
        </div>

        <div class="divide-y divide-gray-100">
            @forelse($withdrawals as $withdrawal)
                <div class="px-6 py-4 flex items-center justify-between hover:bg-gray-50">
                    <div class="flex items-center space-x-4">
                        <div class="w-10 h-10 rounded-xl flex items-center justify-center
                            @if($withdrawal->status === 'completed') bg-green-100
                            @elseif($withdrawal->status === 'rejected') bg-red-100
                            @elseif($withdrawal->status === 'cancelled') bg-gray-100
                            @else bg-amber-100 @endif">
                            <svg class="w-5 h-5
                                @if($withdrawal->status === 'completed') text-green-600
                                @elseif($withdrawal->status === 'rejected') text-red-600
                                @elseif($withdrawal->status === 'cancelled') text-gray-600
                                @else text-amber-600 @endif" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-900">
                                {{ ucwords(str_replace('_', ' ', $withdrawal->payout_method)) }}
                            </p>
                            <p class="text-xs text-gray-500">
                                {{ $withdrawal->created_at->format('M d, Y g:i A') }}
                                @if($withdrawal->reference)
                                    &middot; {{ $withdrawal->reference }}
                                @endif
                            </p>
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="text-sm font-semibold text-red-600">-{{ currency($withdrawal->amount) }}</p>
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium
                            @if($withdrawal->status === 'completed') bg-green-100 text-green-700
                            @elseif($withdrawal->status === 'approved') bg-blue-100 text-blue-700
                            @elseif($withdrawal->status === 'rejected') bg-red-100 text-red-700
                            @elseif($withdrawal->status === 'cancelled') bg-gray-100 text-gray-700
                            @else bg-amber-100 text-amber-700 @endif">
                            {{ ucfirst($withdrawal->status) }}
                        </span>
                    </div>
                </div>
            @empty
                <div class="px-6 py-12 text-center">
                    <svg class="w-12 h-12 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                    </svg>
                    <p class="text-gray-500">No withdrawals yet</p>
                    <p class="text-xs text-gray-400 mt-1">Your withdrawal requests will appear here</p>
                </div>
            @endforelse
        </div>

        @if($withdrawals->hasPages())
            <div class="px-6 py-3 border-t border-gray-100">
                {{ $withdrawals->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
