@extends('admin.layouts.admin')

@section('title', 'Referrals')

@section('content')
<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <h1 class="text-2xl font-bold text-gray-900 mb-6">🤝 Referral Management</h1>

    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-800 rounded-xl p-4 mb-6 text-sm">{{ session('success') }}</div>
    @endif

    {{-- Stats Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <p class="text-sm text-gray-500">Total Referred Users</p>
            <p class="text-2xl font-bold text-gray-900 mt-1">{{ $stats['total_referrals'] }}</p>
        </div>
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <p class="text-sm text-gray-500">Total Commissions Paid</p>
            <p class="text-2xl font-bold text-green-600 mt-1">{{ currency($stats['total_commissions']) }}</p>
        </div>
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <p class="text-sm text-gray-500">Commission Rate (L1)</p>
            <p class="text-2xl font-bold text-indigo-600 mt-1">{{ $settings['referral_commission_rate'] }}%</p>
            <p class="text-xs text-gray-400 mt-1">
                @if($settings['max_referral_levels'] >= 2) L2: {{ $settings['referral_level2_rate'] }}% @endif
                @if($settings['max_referral_levels'] >= 3) / L3: {{ $settings['referral_level3_rate'] }}% @endif
            </p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
        {{-- Referral Settings --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6" x-data="{ levels: {{ $settings['max_referral_levels'] }} }">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">⚙️ Commission Settings</h2>
            <form action="{{ route('admin.referrals.update') }}" method="POST">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Level 1 Rate (%)</label>
                        <input type="number" step="0.1" name="referral_commission_rate" value="{{ $settings['referral_commission_rate'] }}" class="w-full rounded-xl border-gray-200 text-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                        <p class="text-xs text-gray-400 mt-0.5">Direct referral commission</p>
                    </div>
                    <div x-show="levels >= 2" x-cloak>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Level 2 Rate (%)</label>
                        <input type="number" step="0.1" name="referral_level2_rate" value="{{ $settings['referral_level2_rate'] }}" class="w-full rounded-xl border-gray-200 text-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                        <p class="text-xs text-gray-400 mt-0.5">Referral's referral commission</p>
                    </div>
                    <div x-show="levels >= 3" x-cloak>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Level 3 Rate (%)</label>
                        <input type="number" step="0.1" name="referral_level3_rate" value="{{ $settings['referral_level3_rate'] }}" class="w-full rounded-xl border-gray-200 text-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Max MLM Levels</label>
                        <input type="number" x-model.number="levels" name="max_referral_levels" value="{{ $settings['max_referral_levels'] }}" class="w-full rounded-xl border-gray-200 text-sm focus:border-indigo-500 focus:ring-indigo-500" required min="1" max="10">
                        <p class="text-xs text-gray-400 mt-0.5">How deep commissions go (1-10). Changing this shows/hides rate fields above.</p>
                    </div>
                    <button type="submit" class="w-full px-4 py-2 bg-indigo-600 text-white rounded-xl hover:bg-indigo-700 text-sm font-bold transition-colors">Save Settings</button>
                </div>
            </form>
        </div>

        {{-- Top Referrers --}}
        <div class="lg:col-span-2 bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">🏅 Top Referrers</h2>
            @if($stats['top_referrers']->isEmpty())
                <p class="text-sm text-gray-500">No referrals yet.</p>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="text-left text-gray-500 border-b border-gray-100 bg-gray-50">
                                <th class="px-4 py-3 font-medium">#</th>
                                <th class="px-4 py-3 font-medium">User</th>
                                <th class="px-4 py-3 font-medium">Referrals</th>
                                <th class="px-4 py-3 font-medium text-right">Total Earned</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @foreach($stats['top_referrers'] as $i => $referrer)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-3 font-bold text-gray-400">{{ $i + 1 }}</td>
                                    <td class="px-4 py-3 font-medium text-gray-900">{{ $referrer->display_name }}</td>
                                    <td class="px-4 py-3">{{ $referrer->referrals_count }}</td>
                                    <td class="px-4 py-3 text-right font-bold text-green-600">{{ currency($referrer->total_earned) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>

    {{-- Recent Commissions --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">💰 Recent Commissions</h2>
        @if($stats['recent_commissions']->isEmpty())
            <p class="text-sm text-gray-500">No commissions paid yet.</p>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="text-left text-gray-500 border-b border-gray-100 bg-gray-50">
                            <th class="px-4 py-3 font-medium">Date</th>
                            <th class="px-4 py-3 font-medium">Referrer</th>
                            <th class="px-4 py-3 font-medium">Referred</th>
                            <th class="px-4 py-3 font-medium">Level</th>
                            <th class="px-4 py-3 font-medium text-right">Amount</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @foreach($stats['recent_commissions'] as $commission)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 text-gray-500">{{ $commission->created_at->format('M d, H:i') }}</td>
                                <td class="px-4 py-3 font-medium text-gray-900">{{ $commission->referrer?->display_name ?? 'N/A' }}</td>
                                <td class="px-4 py-3">{{ $commission->referredUser?->display_name ?? 'N/A' }}</td>
                                <td class="px-4 py-3">
                                    <span class="px-2 py-0.5 rounded-full text-xs font-medium
                                        @if($commission->level == 1) bg-indigo-100 text-indigo-700
                                        @elseif($commission->level == 2) bg-amber-100 text-amber-700
                                        @else bg-gray-100 text-gray-600 @endif">
                                        L{{ $commission->level }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-right font-bold text-green-600">{{ currency($commission->amount) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>
@endsection
