@extends('admin.layouts.admin')

@section('title', 'Users')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100">
        <div class="px-6 py-4 border-b border-gray-100">
            <form method="GET" class="flex flex-wrap items-center gap-4">
                <input type="text" name="search" placeholder="Search users..." value="{{ request('search') }}" class="rounded-lg border-gray-300 text-sm flex-1 min-w-[200px]">
                <select name="status" class="rounded-lg border-gray-300 text-sm">
                    <option value="">All Status</option>
                    <option value="active" @if(request('status') === 'active') selected @endif>Active</option>
                    <option value="suspended" @if(request('status') === 'suspended') selected @endif>Suspended</option>
                    <option value="banned" @if(request('status') === 'banned') selected @endif>Banned</option>
                </select>
                <select name="level" class="rounded-lg border-gray-300 text-sm">
                    <option value="">All Levels</option>
                    @foreach($levels as $level)
                        <option value="{{ $level->level }}" @if(request('level') == $level->level) selected @endif>Level {{ $level->level }}</option>
                    @endforeach
                </select>
                <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 text-sm font-medium">Filter</button>
            </form>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left text-gray-500 border-b border-gray-100 bg-gray-50">
                        <th class="px-6 py-3 font-medium">User</th>
                        <th class="px-6 py-3 font-medium">Level</th>
                        <th class="px-6 py-3 font-medium">Balance</th>
                        <th class="px-6 py-3 font-medium">Deposit</th>
                        <th class="px-6 py-3 font-medium">Deposit Lock</th>
                        <th class="px-6 py-3 font-medium">Status</th>
                        <th class="px-6 py-3 font-medium">Probation</th>
                        <th class="px-6 py-3 font-medium">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($users as $user)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4">
                                <div class="flex items-center space-x-3">
                                    <div class="w-8 h-8 bg-gray-100 rounded-full flex items-center justify-center">
                                        <span class="text-xs font-semibold text-gray-600">{{ substr($user->name, 0, 1) }}</span>
                                    </div>
                                    <div>
                                        <a href="{{ route('admin.users.show', $user) }}" class="font-medium text-gray-900 hover:text-indigo-600">{{ $user->name }}</a>
                                        <p class="text-xs text-gray-500">{{ $user->phone }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">@if($user->level) Level {{ $user->level->level }} @else - @endif</td>
                            <td class="px-6 py-4 font-medium">{{ currency($user->balance) }}</td>
                            <td class="px-6 py-4">{{ currency($user->deposit_amount) }}</td>
                            <td class="px-6 py-4">
                                @if($user->isDepositLocked())
                                    <span class="text-xs text-red-600 font-medium">🔒 {{ $user->deposit_locked_until->format('M d') }}</span>
                                @elseif($user->hasDeposited())
                                    <span class="text-xs text-green-600 font-medium">🔓 Unlocked</span>
                                @else
                                    <span class="text-xs text-gray-400">-</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-0.5 rounded-full text-xs font-medium
                                    @if($user->status === 'active') bg-green-100 text-green-700
                                    @elseif($user->status === 'suspended') bg-amber-100 text-amber-700
                                    @else bg-red-100 text-red-700 @endif">
                                    {{ ucfirst($user->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                @if($user->is_probation)
                                    <span class="text-xs text-amber-600">Until {{ $user->probation_ends_at?->format('M d') }}</span>
                                @else
                                    <span class="text-xs text-green-600">Completed</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center space-x-3">
                                    <a href="{{ route('admin.users.show', $user) }}" class="text-indigo-600 hover:text-indigo-500 text-sm font-medium">View</a>
                                    <a href="{{ route('admin.users.edit', $user) }}" class="text-gray-600 hover:text-gray-500 text-sm font-medium">Edit</a>
                                    @if($user->isDepositLocked())
                                        <form action="{{ route('admin.users.unlock-deposit', $user) }}" method="POST" class="inline" onsubmit="return confirm('Unlock {{ $user->name }}\'s deposit?')">
                                            @csrf
                                            <button type="submit" class="text-green-600 hover:text-green-500 text-sm font-medium">🔓 Unlock</button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @if($users->hasPages())
            <div class="px-6 py-3 border-t border-gray-100">
                {{ $users->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
