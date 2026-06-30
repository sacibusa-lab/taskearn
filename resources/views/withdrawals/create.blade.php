@extends('layouts.app')

@section('title', 'Request Withdrawal')

@section('header')
<div>
    <a href="{{ route('withdrawals.index') }}" class="text-sm text-indigo-600 hover:text-indigo-500 font-medium mb-2 inline-block">&larr; Back to Withdrawals</a>
    <h1 class="text-2xl font-bold text-gray-900">Request Withdrawal</h1>
    <p class="text-sm text-gray-500 mt-1">Min: {{ currency($min) }} — Max: {{ currency($max) }}</p>
</div>
@endsection

@section('content')
<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8">
        <form action="{{ route('withdrawals.store') }}" method="POST" x-data="withdrawalForm()">
            @csrf

            {{-- Amount --}}
            <div class="mb-6">
                <label for="amount" class="block text-sm font-medium text-gray-700 mb-2">Withdrawal Amount</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-gray-500 font-semibold">{{ currency_symbol() }}</span>
                    <input type="number" name="amount" id="amount" value="{{ old('amount') }}"
                           min="{{ $min }}" max="{{ $max }}" step="0.01" required
                           class="pl-8 w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-lg font-bold"
                           placeholder="0.00">
                </div>
                @error('amount')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
                <div class="flex items-center space-x-4 mt-2 text-xs text-gray-500">
                    <span>Available: <strong>{{ currency(Auth::user()->balance) }}</strong></span>
                    <span>Min: <strong>{{ currency($min) }}</strong></span>
                    <span>Max: <strong>{{ currency($max) }}</strong></span>
                </div>
            </div>

            <input type="hidden" name="payout_method" value="bank_transfer">

            {{-- Bank Account Details --}}
            <div class="mb-6 p-4 bg-gray-50 rounded-xl space-y-4">
                <h4 class="text-sm font-semibold text-gray-900">Bank Account Details</h4>
                <p class="text-xs text-gray-500">Your account will be verified instantly via Paystack.</p>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Bank</label>
                        <div class="relative">
                            <input type="text" x-model="search" @input="open = true" @focus="open = true" @click.away="open = false"
                                   placeholder="Search for your bank..."
                                   class="w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <input type="hidden" name="bank_code" x-model="bankCode">
                            <div x-show="open && filteredBanks.length > 0" x-cloak
                                 class="absolute z-50 mt-1 w-full bg-white border border-gray-200 rounded-xl shadow-lg max-h-60 overflow-y-auto">
                                <template x-for="bank in filteredBanks" :key="bank.code">
                                    <button type="button" @click="selectBank(bank)"
                                            class="w-full text-left px-4 py-2.5 text-sm hover:bg-indigo-50 hover:text-indigo-700 transition-colors border-b border-gray-50 last:border-0"
                                            :class="bankCode === bank.code ? 'bg-indigo-50 text-indigo-700 font-semibold' : 'text-gray-700'">
                                        <span x-text="bank.name"></span>
                                    </button>
                                </template>
                            </div>
                            <div x-show="open && search && filteredBanks.length === 0" x-cloak
                                 class="absolute z-50 mt-1 w-full bg-white border border-gray-200 rounded-xl shadow-lg p-4 text-sm text-gray-400 text-center">
                                No banks found
                            </div>
                        </div>
                        <template x-if="selectedBankName">
                            <p class="text-xs text-green-600 mt-1.5 flex items-center">
                                <svg class="w-3.5 h-3.5 me-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                <span x-text="selectedBankName"></span>
                            </p>
                        </template>
                        @error('bank_code') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Account Number</label>
                        <input type="text" name="account_number" id="account_number" x-model="accountNumber"
                               @input.debounce.500ms="resolveAccount()"
                               value="{{ old('account_number') }}" maxlength="10"
                               class="w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                               placeholder="0123456789">
                        @error('account_number') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Account Name</label>
                        <input type="text" name="account_name" id="account_name" x-model="accountName"
                               readonly
                               class="w-full rounded-xl border-gray-300 bg-gray-100 shadow-sm text-gray-700"
                               placeholder="Auto-verified by Paystack">
                        @error('account_name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                {{-- Verification status --}}
                <div x-show="verifying" class="text-xs text-amber-600 flex items-center">
                    <svg class="w-4 h-4 me-1 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                    Verifying account...
                </div>
                <div x-show="verified" class="text-xs text-green-600 flex items-center">
                    <svg class="w-4 h-4 me-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Account verified: <strong x-text="accountName"></strong>
                </div>
                <div x-show="errorMsg" class="text-xs text-red-600 flex items-center">
                    <svg class="w-4 h-4 me-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span x-text="errorMsg"></span>
                </div>
            </div>

            {{-- Summary --}}
            <div class="bg-indigo-50 rounded-xl p-4 mb-6">
                <h4 class="text-sm font-semibold text-gray-900 mb-2">Withdrawal Summary</h4>
                <div class="space-y-1 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Amount</span>
                        <span class="font-medium">{{ currency(0) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Fee</span>
                        <span class="font-medium">{{ currency(0) }}</span>
                    </div>
                    <div class="flex justify-between border-t border-indigo-200 pt-1 mt-1">
                        <span class="font-semibold text-gray-900">You'll Receive</span>
                        <span class="font-semibold text-gray-900">{{ currency(0) }}</span>
                    </div>
                </div>
            </div>

            <button type="submit" class="w-full py-3 px-6 bg-indigo-600 text-white rounded-xl hover:bg-indigo-700 font-semibold transition-colors">
                Submit Withdrawal Request
            </button>

            <p class="text-center text-xs text-gray-400 mt-4">
                ⏱ Withdrawals are typically processed within <strong>1–2 business days</strong> after admin approval.
            </p>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function withdrawalForm() {
        const allBanks = @json($banks);
        const user = @json(['bank_code' => Auth::user()->bank_code, 'bank_account_number' => Auth::user()->bank_account_number, 'bank_account_name' => Auth::user()->bank_account_name]);
        const savedCode = '{{ old('bank_code') }}' || user.bank_code || '';
        const savedBank = allBanks.find(b => b.code === savedCode);
        const instance = {
            // Bank search
            search: savedBank ? savedBank.name : '',
            bankCode: savedCode,
            open: false,
            selectedBankName: savedBank ? savedBank.name : '',
            get filteredBanks() {
                if (!this.search) return allBanks;
                const q = this.search.toLowerCase();
                return allBanks.filter(b => b.name.toLowerCase().includes(q));
            },
            selectBank(bank) {
                this.bankCode = bank.code;
                this.search = bank.name;
                this.selectedBankName = bank.name;
                this.open = false;
                this.resolveAccount();
            },
            // Withdrawal form
            accountNumber: '{{ old('account_number') }}' || user.bank_account_number || '',
            accountName: '{{ old('account_name') }}' || user.bank_account_name || '',
            verifying: false,
            verified: {{ Auth::user()->bank_account_name ? 'true' : 'false' }},
            errorMsg: '',

            async resolveAccount() {
                if (this.accountNumber.length < 10 || !this.bankCode) {
                    this.verified = false;
                    this.errorMsg = '';
                    this.accountName = '';
                    return;
                }
                this.verifying = true;
                this.errorMsg = '';
                this.verified = false;
                try {
                    const res = await fetch('/api/verify-account', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({
                            account_number: this.accountNumber,
                            bank_code: this.bankCode
                        })
                    });
                    const data = await res.json();
                    if (data.success) {
                        this.accountName = data.account_name;
                        this.verified = true;
                    } else {
                        this.errorMsg = data.message || 'Could not verify account.';
                    }
                } catch (e) {
                    this.errorMsg = 'Verification failed. Check your connection.';
                }
                this.verifying = false;
            }
        };
        window.withdrawalFormInstance = instance;
        return instance;
    }
</script>
@endpush
