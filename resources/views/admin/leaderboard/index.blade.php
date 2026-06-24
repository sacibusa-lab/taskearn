@extends('admin.layouts.admin')

@section('title', 'Leaderboard')

@section('content')
<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    {{-- Reward Banner --}}
    <div class="bg-gradient-to-r from-amber-500 to-orange-600 text-white rounded-2xl p-5 mb-6 flex items-center justify-between shadow-lg">
        <div>
            <h3 class="font-bold text-lg">🏆 Distribute Leaderboard Rewards</h3>
            <p class="text-xs text-amber-100 mt-0.5">Top earner gets 10% bonus of their period earnings</p>
        </div>
        <div class="flex items-center space-x-3">
            <form action="{{ route('admin.leaderboard.reward', 'week') }}" method="POST" onsubmit="return confirm('Award weekly bonus to the top earner?')">
                @csrf
                <button class="px-4 py-2 bg-black text-yellow-400 rounded-xl hover:bg-gray-800 text-sm font-bold border border-yellow-500">🏅 Weekly Bonus</button>
            </form>
            <form action="{{ route('admin.leaderboard.reward', 'month') }}" method="POST" onsubmit="return confirm('Award monthly bonus to the top earner?')">
                @csrf
                <button class="px-4 py-2 bg-yellow-400 text-black rounded-xl hover:bg-yellow-300 text-sm font-bold border border-black">🏆 Monthly Bonus</button>
            </form>
        </div>
    </div>

    {{-- Header --}}
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold text-gray-900">🏆 Leaderboard</h1>
    </div>

    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-800 rounded-xl p-4 mb-6 text-sm">{{ session('success') }}</div>
    @endif

    {{-- Period Filter --}}
    <div class="flex items-center space-x-2 mb-6">
        <a href="?period=week" class="px-4 py-2 rounded-xl text-sm font-medium transition-colors {{ $period === 'week' ? 'bg-indigo-600 text-white' : 'bg-white border border-gray-200 text-gray-600 hover:bg-gray-50' }}">This Week</a>
        <a href="?period=month" class="px-4 py-2 rounded-xl text-sm font-medium transition-colors {{ $period === 'month' ? 'bg-indigo-600 text-white' : 'bg-white border border-gray-200 text-gray-600 hover:bg-gray-50' }}">This Month</a>
        <a href="?period=all" class="px-4 py-2 rounded-xl text-sm font-medium transition-colors {{ $period === 'all' ? 'bg-indigo-600 text-white' : 'bg-white border border-gray-200 text-gray-600 hover:bg-gray-50' }}">All Categories</a>
    </div>

    {{-- Top 3 Podium --}}
    @if(count($leaders) >= 3)
        @php $top = array_slice($leaders, 0, 3); @endphp
        <div class="grid grid-cols-3 gap-4 mb-8">
            {{-- 2nd Place --}}
            <div class="text-center pt-6">
                <div class="w-16 h-16 bg-gray-200 rounded-full mx-auto flex items-center justify-center text-2xl font-bold text-gray-600">{{ substr($top[1]['name'], 1, 1) }}</div>
                <p class="text-3xl mt-2">🥈</p>
                <p class="text-sm font-semibold text-gray-900 truncate">{{ $top[1]['name'] }}</p>
                <p class="text-xs text-gray-500">Lvl {{ $top[1]['level'] }}</p>
                <p class="text-sm font-bold text-indigo-600 mt-1">{{ currency($top[1]['amount']) }}</p>
            </div>
            {{-- 1st Place --}}
            <div class="text-center -mt-4">
                <div class="w-20 h-20 bg-yellow-100 rounded-full mx-auto flex items-center justify-center text-3xl font-bold text-yellow-600 ring-4 ring-yellow-300">{{ substr($top[0]['name'], 1, 1) }}</div>
                <p class="text-4xl mt-1">🥇</p>
                <p class="text-sm font-bold text-gray-900 truncate">{{ $top[0]['name'] }}</p>
                <p class="text-xs text-gray-500">Lvl {{ $top[0]['level'] }}</p>
                <p class="text-sm font-bold text-indigo-600 mt-1">{{ currency($top[0]['amount']) }}</p>
                <p class="text-xs text-gray-400 mt-0.5">{{ $top[0]['badges'] }}</p>
            </div>
            {{-- 3rd Place --}}
            <div class="text-center pt-8">
                <div class="w-16 h-16 bg-orange-100 rounded-full mx-auto flex items-center justify-center text-2xl font-bold text-orange-600">{{ substr($top[2]['name'], 1, 1) }}</div>
                <p class="text-3xl mt-2">🥉</p>
                <p class="text-sm font-semibold text-gray-900 truncate">{{ $top[2]['name'] }}</p>
                <p class="text-xs text-gray-500">Lvl {{ $top[2]['level'] }}</p>
                <p class="text-sm font-bold text-indigo-600 mt-1">{{ currency($top[2]['amount']) }}</p>
            </div>
        </div>
    @endif

    {{-- Leaderboard Table --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left text-gray-500 border-b border-gray-100 bg-gray-50">
                        <th class="px-6 py-3 font-medium w-16">Rank</th>
                        <th class="px-6 py-3 font-medium">User</th>
                        <th class="px-6 py-3 font-medium">Level</th>
                        <th class="px-6 py-3 font-medium">Badges</th>
                        <th class="px-6 py-3 font-medium text-right">Earnings</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($leaders as $leader)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4">
                                @if($leader['rank'] <= 3)
                                    <span class="text-lg">{{ ['🥇','🥈','🥉'][$leader['rank']-1] }}</span>
                                @else
                                    <span class="text-gray-400 font-bold">#{{ $leader['rank'] }}</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 font-medium text-gray-900">{{ $leader['name'] }}</td>
                            <td class="px-6 py-4 text-gray-500">Level {{ $leader['level'] }}</td>
                            <td class="px-6 py-4 text-sm">{{ $leader['badges'] ?: '—' }}</td>
                            <td class="px-6 py-4 text-right font-bold text-green-600">{{ currency($leader['amount']) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    @if(count($leaders) === 0)
        <div class="text-center py-16 text-gray-400">
            <p class="text-4xl mb-2">🏆</p>
            <p class="text-lg font-medium">No leaderboard data yet</p>
            <p class="text-sm">Earnings will appear here as users complete tasks.</p>
        </div>
    @endif
</div>
@endsection
