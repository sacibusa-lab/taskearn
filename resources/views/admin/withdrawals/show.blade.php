@extends('admin.layouts.admin')

@section('title', 'Withdrawal #' . $withdrawal->reference)

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <a href="{{ route('admin.withdrawals.index') }}" class="text-sm text-indigo-600 hover:text-indigo-500 font-medium mb-4 inline-block">&larr; Back to Withdrawals</a>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        {{-- Header --}}
        <div class="px-8 py-6 border-b border-gray-100 flex items-center justify-between">
            <div>
                <h2 class="text-xl font-bold text-gray-900">Withdrawal {{ $withdrawal->reference }}</h2>
                <p class="text-sm text-gray-500">Requested {{ $withdrawal->created_at->format('M d, Y g:i A') }}</p>
            </div>
            <span class="px-3 py-1 rounded-full text-sm font-medium
                @if($withdrawal->status === 'completed') bg-green-100 text-green-700
                @elseif($withdrawal->status === 'approved') bg-blue-100 text-blue-700
                @elseif($withdrawal->status === 'rejected') bg-red-100 text-red-700
                @elseif($withdrawal->status === 'cancelled') bg-gray-100 text-gray-700
                @else bg-amber-100 text-amber-700 @endif">
                {{ ucfirst($withdrawal->status) }}
            </span>
        </div>

        <div class="p-8 grid grid-cols-1 md:grid-cols-2 gap-8">
            {{-- User Info --}}
            <div>
                <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-wider mb-3">User</h3>
                <div class="bg-gray-50 rounded-xl p-4">
                    <p class="font-medium text-gray-900">{{ $withdrawal->user?->name }}</p>
                    <p class="text-sm text-gray-500">{{ $withdrawal->user?->phone }}</p>
                    @if($withdrawal->user?->email)
                        <p class="text-sm text-gray-500">{{ $withdrawal->user->email }}</p>
                    @endif
                </div>
            </div>

            {{-- Amount Info --}}
            <div>
                <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-wider mb-3">Amount</h3>
                <div class="bg-gray-50 rounded-xl p-4 space-y-1">
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">Requested</span>
                        <span class="font-medium">{{ currency($withdrawal->amount) }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">Fee</span>
                        <span class="font-medium">{{ currency($withdrawal->charge) }}</span>
                    </div>
                    <div class="flex justify-between text-sm border-t border-gray-200 pt-1 mt-1">
                        <span class="font-semibold">Net Amount</span>
                        <span class="font-semibold">{{ currency($withdrawal->net_amount) }}</span>
                    </div>
                </div>
            </div>

            {{-- Payout Method & Details --}}
            <div class="md:col-span-2">
                <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-wider mb-3">Payout Details</h3>
                <div class="bg-gray-50 rounded-xl p-4">
                    <p class="text-sm font-medium text-gray-900 capitalize mb-3">{{ str_replace('_', ' ', $withdrawal->payout_method) }}</p>
                    @if($withdrawal->payout_method === 'bank_transfer')
                        <div class="grid grid-cols-3 gap-4 text-sm">
                            <div>
                                <p class="text-gray-500">Bank</p>
                                <p class="font-medium">{{ $withdrawal->account_details['bank_name'] ?? '—' }}</p>
                            </div>
                            <div>
                                <p class="text-gray-500">Account Name</p>
                                <p class="font-medium">{{ $withdrawal->account_details['account_name'] ?? '—' }}</p>
                            </div>
                            <div>
                                <p class="text-gray-500">Account Number</p>
                                <p class="font-medium">{{ $withdrawal->account_details['account_number'] ?? '—' }}</p>
                            </div>
                        </div>
                    @elseif($withdrawal->payout_method === 'crypto')
                        <div class="grid grid-cols-2 gap-4 text-sm">
                            <div>
                                <p class="text-gray-500">Currency</p>
                                <p class="font-medium">{{ $withdrawal->account_details['crypto_currency'] ?? '—' }}</p>
                            </div>
                            <div>
                                <p class="text-gray-500">Wallet Address</p>
                                <p class="font-medium text-xs break-all">{{ $withdrawal->account_details['wallet_address'] ?? '—' }}</p>
                            </div>
                        </div>
                    @elseif($withdrawal->payout_method === 'paypal')
                        <div class="text-sm">
                            <p class="text-gray-500">PayPal Email</p>
                            <p class="font-medium">{{ $withdrawal->account_details['paypal_email'] ?? '—' }}</p>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Admin Notes --}}
            @if($withdrawal->admin_notes)
                <div class="md:col-span-2">
                    <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-wider mb-3">Admin Notes</h3>
                    <div class="bg-gray-50 rounded-xl p-4">
                        <p class="text-sm text-gray-700">{{ $withdrawal->admin_notes }}</p>
                    </div>
                </div>
            @endif

            {{-- Processed Info --}}
            @if($withdrawal->processed_at)
                <div class="md:col-span-2">
                    <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-wider mb-3">Processing</h3>
                    <div class="bg-gray-50 rounded-xl p-4 flex items-center space-x-4 text-sm">
                        <span class="text-gray-500">Processed {{ $withdrawal->processed_at->format('M d, Y g:i A') }}</span>
                        @if($withdrawal->processor)
                            <span class="text-gray-500">by <strong>{{ $withdrawal->processor->name }}</strong></span>
                        @endif
                    </div>
                </div>
            @endif
        </div>

        {{-- Actions --}}
        @if($withdrawal->status === 'pending')
            <div class="px-8 py-4 border-t border-gray-100 bg-gray-50 flex items-center justify-end space-x-3">
                <form action="{{ route('admin.withdrawals.reject', $withdrawal) }}" method="POST" class="inline"
                      onsubmit="return confirm('Reject this withdrawal? The amount will be refunded to the user.')">
                    @csrf
                    <div class="flex items-center space-x-2">
                        <input type="text" name="admin_notes" placeholder="Reason for rejection..." required
                               class="rounded-lg border-gray-300 text-sm w-64">
                        <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 text-sm font-medium">Reject</button>
                    </div>
                </form>
                <form action="{{ route('admin.withdrawals.approve', $withdrawal) }}" method="POST" class="inline"
                      onsubmit="return confirm('Approve this withdrawal?')">
                    @csrf
                    <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm font-medium">Approve</button>
                </form>
            </div>
        @elseif($withdrawal->status === 'approved')
            <div class="px-8 py-4 border-t border-gray-100 bg-gray-50 flex items-center justify-end">
                <form action="{{ route('admin.withdrawals.complete', $withdrawal) }}" method="POST" class="inline"
                      onsubmit="return confirm('Mark this withdrawal as completed?')">
                    @csrf
                    <button type="submit" class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 text-sm font-medium">Mark as Completed</button>
                </form>
            </div>
        @endif
    </div>
</div>
@endsection
