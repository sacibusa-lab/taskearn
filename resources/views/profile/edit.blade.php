@extends('layouts.app')

@section('title', 'Profile')

@section('header')
<div>
    <h1 class="text-2xl font-bold text-gray-900">👤 Profile Settings</h1>
    <p class="text-sm text-gray-500 mt-1">Manage your account information</p>
</div>
@endsection

@section('content')
<div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">

    {{-- Profile Information --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-1">Profile Information</h2>
        <p class="text-sm text-gray-500 mb-6">Update your name and phone number.</p>

        <form method="POST" action="{{ route('profile.update') }}" class="space-y-4">
            @csrf @method('PATCH')

            <div>
                <label for="name" class="block text-sm font-medium text-gray-700">Name</label>
                <input id="name" name="name" type="text" value="{{ old('name', $user->name) }}" required autofocus class="mt-1 w-full rounded-xl border-gray-200 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                @error('name')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
            </div>

            <div>
                <label for="phone" class="block text-sm font-medium text-gray-700">Phone Number</label>
                <input id="phone" name="phone" type="tel" value="{{ old('phone', $user->phone) }}" required class="mt-1 w-full rounded-xl border-gray-200 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                <p class="mt-1 text-xs text-gray-400">Used for login and notifications</p>
                @error('phone')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
            </div>

            <div>
                <label for="username" class="block text-sm font-medium text-gray-700">@Username</label>
                <input id="username" name="username" type="text" value="{{ old('username', $user->username) }}" class="mt-1 w-full rounded-xl border-gray-200 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                <p class="mt-1 text-xs text-gray-400">Your public display name on leaderboards</p>
                @error('username')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
            </div>

            <button type="submit" class="px-6 py-2.5 bg-indigo-600 text-white rounded-xl hover:bg-indigo-700 text-sm font-semibold transition-colors">Save Profile</button>

            @if(session('status') === 'profile-updated')
                <span x-data="{show:true}" x-show="show" x-init="setTimeout(() => show = false, 2000)" class="text-sm text-green-600 ml-3">Saved!</span>
            @endif
        </form>
    </div>

    {{-- Bank Details --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-1">🏦 Bank Details</h2>
        <p class="text-sm text-gray-500 mb-6">Save your bank account for faster withdrawals.</p>

        <form method="POST" action="{{ route('profile.bank') }}" class="space-y-4" x-data="bankForm()">
            @csrf

            <div>
                <label for="bank_code" class="block text-sm font-medium text-gray-700">Bank Name</label>
                <select id="bank_code" name="bank_code" x-model="bankCode" @change="resolve()" class="mt-1 w-full rounded-xl border-gray-200 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="">Select a bank</option>
                    @foreach($banks as $bank)
                        <option value="{{ $bank['code'] }}" {{ old('bank_code', $user->bank_code) == $bank['code'] ? 'selected' : '' }}>{{ $bank['name'] }}</option>
                    @endforeach
                </select>
                @error('bank_code')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
            </div>

            <div>
                <label for="bank_account_number" class="block text-sm font-medium text-gray-700">Account Number</label>
                <input id="bank_account_number" name="bank_account_number" type="text" x-model="accountNumber" @input.debounce.500ms="resolve()" value="{{ old('bank_account_number', $user->bank_account_number) }}" maxlength="10" placeholder="10-digit NUBAN" class="mt-1 w-full rounded-xl border-gray-200 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                @error('bank_account_number')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
            </div>

            <div x-show="accountName" x-cloak>
                <label class="block text-sm font-medium text-gray-700">Account Name</label>
                <p class="mt-1 text-sm font-semibold text-green-700 bg-green-50 rounded-xl px-4 py-2" x-text="accountName"></p>
            </div>

            <div>
                <button type="submit" class="px-6 py-2.5 bg-indigo-600 text-white rounded-xl hover:bg-indigo-700 text-sm font-semibold transition-colors">Save Bank Details</button>
            </div>

            @if(session('status') === 'bank-updated')
                <span x-data="{show:true}" x-show="show" x-init="setTimeout(() => show = false, 2000)" class="text-sm text-green-600">Bank details saved!</span>
            @endif
        </form>
    </div>

    {{-- Change Password --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-1">🔒 Change Password</h2>
        <p class="text-sm text-gray-500 mb-6">Keep your account secure.</p>

        <form method="POST" action="{{ route('password.update') }}" class="space-y-4">
            @csrf @method('PUT')

            <div>
                <label for="current_password" class="block text-sm font-medium text-gray-700">Current Password</label>
                <input id="current_password" name="current_password" type="password" required autocomplete="current-password" class="mt-1 w-full rounded-xl border-gray-200 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                @error('current_password', 'updatePassword')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
            </div>

            <div>
                <label for="password" class="block text-sm font-medium text-gray-700">New Password</label>
                <input id="password" name="password" type="password" required autocomplete="new-password" class="mt-1 w-full rounded-xl border-gray-200 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                @error('password', 'updatePassword')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
            </div>

            <div>
                <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Confirm Password</label>
                <input id="password_confirmation" name="password_confirmation" type="password" required autocomplete="new-password" class="mt-1 w-full rounded-xl border-gray-200 text-sm focus:border-indigo-500 focus:ring-indigo-500">
            </div>

            <button type="submit" class="px-6 py-2.5 bg-indigo-600 text-white rounded-xl hover:bg-indigo-700 text-sm font-semibold transition-colors">Update Password</button>

            @if(session('status') === 'password-updated')
                <span x-data="{show:true}" x-show="show" x-init="setTimeout(() => show = false, 2000)" class="text-sm text-green-600 ml-3">Password updated!</span>
            @endif
        </form>
    </div>

    {{-- Delete Account --}}
    <div class="bg-white rounded-2xl shadow-sm border border-red-100 p-6">
        <h2 class="text-lg font-semibold text-red-700 mb-1">⚠️ Delete Account</h2>
        <p class="text-sm text-gray-500 mb-6">Permanently delete your account and all data.</p>

        <form method="POST" action="{{ route('profile.destroy') }}" onsubmit="return confirm('Are you sure? This cannot be undone.')" class="space-y-4">
            @csrf @method('DELETE')

            <div>
                <label for="delete_password" class="block text-sm font-medium text-gray-700">Enter your password to confirm</label>
                <input id="delete_password" name="password" type="password" required class="mt-1 w-full rounded-xl border-red-200 text-sm focus:border-red-500 focus:ring-red-500">
                @error('password', 'userDeletion')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
            </div>

            <button type="submit" class="px-6 py-2.5 bg-red-600 text-white rounded-xl hover:bg-red-700 text-sm font-semibold transition-colors">Delete Account</button>
        </form>
    </div>

</div>
@endsection

@push('scripts')
<script>
function bankForm() {
    return {
        bankCode: '{{ old('bank_code', $user->bank_code) }}',
        accountNumber: '{{ old('bank_account_number', $user->bank_account_number) }}',
        accountName: '{{ old('bank_account_name', $user->bank_account_name) }}',
        async resolve() {
            if (this.bankCode && this.accountNumber && this.accountNumber.length === 10) {
                try {
                    const res = await fetch('{{ route('api.verify-account') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                        },
                        body: JSON.stringify({ bank_code: this.bankCode, account_number: this.accountNumber }),
                    });
                    const data = await res.json();
                    if (data.status && data.data) {
                        this.accountName = data.data.account_name;
                    } else {
                        this.accountName = '';
                    }
                } catch (e) {
                    this.accountName = '';
                }
            }
        }
    }
}
</script>
@endpush
