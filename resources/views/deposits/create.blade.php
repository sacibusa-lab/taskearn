@extends('layouts.app')

@section('title', 'Make a Deposit')

@section('header')
<div>
    <h1 class="text-2xl font-bold text-gray-900">Make a Deposit</h1>
    <p class="text-sm text-gray-500 mt-1">Deposit to unlock higher levels and earn more</p>
</div>
@endsection

@section('content')
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="grid grid-cols-1 lg:grid-cols-5 gap-8">
        <div class="lg:col-span-3">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8">
                @if(!$paystackConfigured)
                    <div class="bg-amber-50 border border-amber-200 rounded-xl p-4 mb-6">
                        <p class="text-sm text-amber-700">Paystack is not configured. Please contact support.</p>
                    </div>
                @endif

                <form action="{{ route('deposits.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="payment_method" value="paystack">
                    
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-3">Select Deposit Amount</label>
                        <div class="grid grid-cols-2 gap-3">
                            @foreach($levels->where('level', '>', 0) as $level)
                                <label class="relative block cursor-pointer">
                                    <input type="radio" name="amount" value="{{ $level->deposit_amount }}" class="peer sr-only" @if($loop->first) checked @endif>
                                    <div class="p-4 border-2 border-gray-200 rounded-xl peer-checked:border-indigo-500 peer-checked:bg-indigo-50 hover:border-indigo-300 transition-all">
                                        <p class="text-lg font-bold text-gray-900">{{ currency_raw($level->deposit_amount) }}</p>
                                        <p class="text-xs text-gray-500">Level {{ $level->level }}</p>
                                        <p class="text-xs text-green-600 font-medium mt-1">{{ currency($level->weekly_payout) }}/wk</p>
                                    </div>
                                </label>
                            @endforeach
                        </div>
                        @error('amount')
                            <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Deposit Lock Notice --}}
                    @if($user->isDepositLocked())
                        <div class="mb-6 bg-amber-50 border border-amber-200 rounded-xl p-4">
                            <p class="text-sm font-semibold text-amber-800 flex items-center">
                                <svg class="w-5 h-5 me-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                                Deposit Locked — Unlocks {{ $user->deposit_locked_until->diffForHumans() }}
                            </p>
                            <p class="text-xs text-amber-600 mt-1">You can only deposit once per month. After unlocking, you can renew your level or upgrade.</p>
                        </div>
                    @endif

                    <button type="submit" class="w-full py-3.5 px-6 bg-indigo-600 text-white rounded-xl hover:bg-indigo-700 font-semibold transition-colors text-sm flex items-center justify-center space-x-2 {{ $user->isDepositLocked() ? 'opacity-50 cursor-not-allowed' : '' }}" {{ $user->isDepositLocked() ? 'disabled' : '' }}>
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 11c0-1.933-1.567-3.5-3.5-3.5S5 9.067 5 11c0 1.933 1.567 3.5 3.5 3.5S12 12.933 12 11zm7 0c0-1.933-1.567-3.5-3.5-3.5S12 9.067 12 11c0 1.933 1.567 3.5 3.5 3.5S19 12.933 19 11zm-3.5 9.5c1.933 0 3.5-1.567 3.5-3.5s-1.567-3.5-3.5-3.5S12 15.067 12 17s1.567 3.5 3.5 3.5z"/></svg>
                        <span>Pay with Paystack</span>
                    </button>
                </form>
            </div>
        </div>

        <div class="lg:col-span-2">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Level Benefits</h3>
                <div class="space-y-4">
                    @foreach($levels as $level)
                        <div class="flex items-center justify-between p-3 rounded-xl @if($user->level && $user->level->level == $level->level) bg-indigo-50 border border-indigo-200 @else bg-gray-50 @endif">
                            <div class="flex items-center space-x-3">
                                <div class="w-10 h-10 rounded-xl flex items-center justify-center text-sm font-bold @if($user->level && $user->level->level == $level->level) bg-indigo-200 text-indigo-700 @else bg-gray-200 text-gray-600 @endif">
                                    {{ $level->level }}
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-900">Level {{ $level->level }}</p>
                                    @if($level->level > 0)
                                        <p class="text-xs text-gray-500">{{ currency_raw($level->deposit_amount) }} deposit</p>
                                    @else
                                        <p class="text-xs text-gray-500">Free</p>
                                    @endif
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="text-sm font-bold text-green-600">{{ currency($level->weekly_payout) }}</p>
                                <p class="text-xs text-gray-500">/week</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Current Status -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mt-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-3">Your Status</h3>
                @if($user->level)
                    <div class="text-center">
                        <div class="w-16 h-16 bg-indigo-100 rounded-2xl flex items-center justify-center mx-auto">
                            <span class="text-2xl font-bold text-indigo-600">{{ $user->level->level }}</span>
                        </div>
                        <p class="mt-2 font-semibold text-gray-900">Level {{ $user->level->level }}</p>
                        <p class="text-sm text-gray-500">{{ currency($user->deposit_amount) }} deposited</p>
                    </div>
                @else
                    <p class="text-sm text-gray-500 text-center">No level assigned yet</p>
                @endif
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Clear selections when switching between amounts
    document.querySelectorAll('input[name="amount"]').forEach(radio => {
        radio.addEventListener('change', function() {
            if (this.checked) {
                document.querySelectorAll('input[name="amount"]').forEach(r => {
                    if (r !== this) r.checked = false;
                });
            }
        });
    });
</script>
@endpush
@endsection
