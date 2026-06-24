@extends('admin.layouts.admin')

@section('title', 'Withdrawals')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    {{-- Stats --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
            <p class="text-xs text-gray-500">Pending</p>
            <p class="text-2xl font-bold text-amber-600">{{ $stats['pending'] }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
            <p class="text-xs text-gray-500">Approved</p>
            <p class="text-2xl font-bold text-blue-600">{{ $stats['approved'] }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
            <p class="text-xs text-gray-500">Paid Out</p>
            <p class="text-2xl font-bold text-green-600">{{ currency($stats['completed']) }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
            <p class="text-xs text-gray-500">Total Requested</p>
            <p class="text-2xl font-bold text-gray-900">{{ currency($stats['total_requested']) }}</p>
        </div>
    </div>

    {{-- Filters --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100">
        <div class="px-6 py-4 border-b border-gray-100">
            <form method="GET" class="flex items-center space-x-4">
                <select name="status" class="rounded-lg border-gray-300 text-sm" onchange="this.form.submit()">
                    <option value="">All Status</option>
                    <option value="pending" @if(request('status') === 'pending') selected @endif>Pending</option>
                    <option value="approved" @if(request('status') === 'approved') selected @endif>Approved</option>
                    <option value="rejected" @if(request('status') === 'rejected') selected @endif>Rejected</option>
                    <option value="completed" @if(request('status') === 'completed') selected @endif>Completed</option>
                    <option value="cancelled" @if(request('status') === 'cancelled') selected @endif>Cancelled</option>
                </select>
                @if(request('status'))
                    <a href="{{ route('admin.withdrawals.index') }}" class="text-sm text-gray-500 hover:text-gray-700">Clear</a>
                @endif
            </form>
        </div>

        <div class="divide-y divide-gray-100">
            @forelse($withdrawals as $withdrawal)
                <div class="px-6 py-4 flex items-center justify-between hover:bg-gray-50">
                    <div class="flex items-center space-x-4 flex-1">
                        <div class="w-10 h-10 rounded-full bg-gray-100 flex items-center justify-center">
                            <span class="text-sm font-semibold text-gray-600">{{ substr($withdrawal->user?->name ?? '?', 0, 1) }}</span>
                        </div>
                        <div class="flex-1">
                            <div class="flex items-center space-x-2">
                                <p class="text-sm font-medium text-gray-900">{{ $withdrawal->user?->name }}</p>
                                <span class="text-xs text-gray-500">{{ $withdrawal->user?->phone }}</span>
                            </div>
                            <div class="flex items-center space-x-3 text-xs text-gray-500 mt-0.5">
                                <span>{{ $withdrawal->reference }}</span>
                                <span>&middot;</span>
                                <span class="capitalize">{{ str_replace('_', ' ', $withdrawal->payout_method) }}</span>
                                <span>&middot;</span>
                                <span>{{ $withdrawal->created_at->format('M d, Y') }}</span>
                                @if($withdrawal->account_details)
                                    <span>&middot;</span>
                                    <span class="text-gray-400">{{ $withdrawal->account_summary }}</span>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="text-right flex items-center space-x-4">
                        <div>
                            <p class="text-sm font-bold text-red-600">-{{ currency($withdrawal->amount) }}</p>
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium
                                @if($withdrawal->status === 'completed') bg-green-100 text-green-700
                                @elseif($withdrawal->status === 'approved') bg-blue-100 text-blue-700
                                @elseif($withdrawal->status === 'rejected') bg-red-100 text-red-700
                                @elseif($withdrawal->status === 'cancelled') bg-gray-100 text-gray-700
                                @else bg-amber-100 text-amber-700 @endif">
                                {{ ucfirst($withdrawal->status) }}
                            </span>
                        </div>
                        <a href="{{ route('admin.withdrawals.show', $withdrawal) }}" class="text-indigo-600 hover:text-indigo-500 text-sm font-medium">Review</a>
                    </div>
                </div>
            @empty
                <div class="px-6 py-12 text-center text-gray-500">
                    <p>No withdrawals found</p>
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
