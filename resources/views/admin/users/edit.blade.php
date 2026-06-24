@extends('admin.layouts.admin')

@section('title', 'Edit User')

@section('content')
<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <a href="{{ route('admin.users.index') }}" class="text-sm text-indigo-600 hover:text-indigo-500 font-medium mb-4 inline-block">&larr; Back to Users</a>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8">
        <h2 class="text-xl font-bold text-gray-900 mb-6">Edit User: {{ $user->name }}</h2>

        {{-- Deposit Lock Status --}}
        @if($user->hasDeposited())
            <div class="mb-6 p-4 rounded-xl border @if($user->isDepositLocked()) bg-red-50 border-red-200 @else bg-green-50 border-green-200 @endif">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-bold @if($user->isDepositLocked()) text-red-800 @else text-green-800 @endif">
                            @if($user->isDepositLocked())
                                🔒 Deposit Locked until {{ $user->deposit_locked_until->format('M d, Y') }}
                            @else
                                🔓 Deposit Unlocked
                            @endif
                        </p>
                        <p class="text-xs @if($user->isDepositLocked()) text-red-600 @else text-green-600 @endif mt-0.5">
                            @if($user->isDepositLocked())
                                User cannot deposit until this date.
                            @else
                                User can deposit freely.
                            @endif
                        </p>
                    </div>
                    @if($user->isDepositLocked())
                        <form action="{{ route('admin.users.unlock-deposit', $user) }}" method="POST" onsubmit="return confirm('Unlock this user\'s deposit?')">
                            @csrf
                            <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-xl hover:bg-green-700 text-sm font-medium">🔓 Unlock Now</button>
                        </form>
                    @endif
                </div>
            </div>
        @endif

        <form action="{{ route('admin.users.update', $user) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Name</label>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}" required class="w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Phone Number</label>
                    <input type="tel" name="phone" value="{{ old('phone', $user->phone) }}" required class="w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Level</label>
                    <select name="level_id" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">No Level</option>
                        @foreach($levels as $level)
                            <option value="{{ $level->id }}" @if($user->level_id == $level->id) selected @endif>Level {{ $level->level }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                    <select name="status" required class="w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="active" @if($user->status === 'active') selected @endif>Active</option>
                        <option value="suspended" @if($user->status === 'suspended') selected @endif>Suspended</option>
                        <option value="banned" @if($user->status === 'banned') selected @endif>Banned</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Balance</label>
                    <input type="number" name="balance" value="{{ old('balance', $user->balance) }}" step="0.01" required class="w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>
                <div class="flex items-center">
                    <label class="inline-flex items-center">
                        <input type="checkbox" name="is_admin" value="1" @if($user->is_admin) checked @endif class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                        <span class="ms-2 text-sm text-gray-700">Admin User</span>
                    </label>
                </div>
            </div>

            <div class="flex justify-end space-x-3 pt-4 border-t border-gray-100">
                <a href="{{ route('admin.users.index') }}" class="px-4 py-2 border border-gray-300 rounded-xl text-sm font-medium text-gray-700 hover:bg-gray-50">Cancel</a>
                <button type="submit" class="px-6 py-2 bg-indigo-600 text-white rounded-xl hover:bg-indigo-700 text-sm font-medium">Update User</button>
            </div>
        </form>
    </div>
</div>
@endsection
