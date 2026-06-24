@extends('layouts.app')

@section('title', 'Referrals')

@section('header')
<div>
    <h1 class="text-2xl font-bold text-gray-900">Refer & Earn</h1>
    <p class="text-sm text-gray-500 mt-1">Invite friends and earn commissions on their deposits</p>
</div>
@endsection

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Referral Link -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8 mb-8">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h2 class="text-xl font-bold text-gray-900">Your Referral Link</h2>
                <p class="text-sm text-gray-500 mt-1">Share this link with friends to earn commission</p>
            </div>
            <div class="bg-green-50 border border-green-200 rounded-xl px-4 py-3 text-center">
                <p class="text-sm text-green-700">Total Earned from Referrals</p>
                <p class="text-xl font-bold text-green-600">{{ currency($stats['total_commissions']) }}</p>
            </div>
        </div>

        <div class="flex items-center space-x-3">
            <input type="text" readonly value="{{ $stats['referral_url'] }}" id="referralUrl" class="flex-1 rounded-xl border-gray-300 bg-gray-50 text-gray-700 text-sm">
            <button onclick="copyReferralUrl()" class="px-4 py-2.5 bg-indigo-600 text-white rounded-xl hover:bg-indigo-700 font-medium text-sm transition-colors flex items-center space-x-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                <span id="copyBtnText">Copy Link</span>
            </button>
        </div>

        {{-- Share Buttons --}}
        <div class="mt-4 flex items-center space-x-3">
            <span class="text-xs text-gray-500">Share via:</span>
            @php $url = urlencode($stats['referral_url']); $text = urlencode('Join me on ' . config('app.name', 'TaskEarn') . ' and start earning! Use my referral link:'); @endphp
            <a href="https://wa.me/?text={{ $text }}%20{{ $url }}" target="_blank" rel="noopener" class="inline-flex items-center space-x-1.5 px-3 py-1.5 bg-green-500 text-white rounded-lg hover:bg-green-600 text-xs font-medium transition-colors">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                <span>WhatsApp</span>
            </a>
            <a href="https://t.me/share/url?url={{ $url }}&text={{ $text }}" target="_blank" rel="noopener" class="inline-flex items-center space-x-1.5 px-3 py-1.5 bg-blue-400 text-white rounded-lg hover:bg-blue-500 text-xs font-medium transition-colors">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M11.944 0A12 12 0 000 12a12 12 0 0012 12 12 12 0 0012-12A12 12 0 0012 0a12 12 0 00-.056 0zm4.962 7.224c.1-.002.321.023.465.14a.506.506 0 01.171.325c.016.093.036.306.02.472-.18 1.898-.962 6.502-1.36 8.627-.168.9-.499 1.201-.82 1.23-.696.065-1.225-.46-1.9-.902-1.056-.693-1.653-1.124-2.678-1.8-1.185-.78-.417-1.21.258-1.91.177-.184 3.247-2.977 3.307-3.23.007-.032.014-.15-.056-.212s-.174-.041-.249-.024c-.106.024-1.793 1.14-5.061 3.345-.48.33-.913.49-1.302.48-.428-.008-1.252-.241-1.865-.44-.752-.245-1.349-.374-1.297-.789.027-.216.325-.437.893-.663 3.498-1.524 5.83-2.529 6.998-3.014 3.332-1.386 4.025-1.627 4.476-1.635z"/></svg>
                <span>Telegram</span>
            </a>
            <a href="https://twitter.com/intent/tweet?text={{ $text }}&url={{ $url }}" target="_blank" rel="noopener" class="inline-flex items-center space-x-1.5 px-3 py-1.5 bg-black text-white rounded-lg hover:bg-gray-800 text-xs font-medium transition-colors">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
                <span>X</span>
            </a>
            <a href="https://www.facebook.com/sharer/sharer.php?u={{ $url }}" target="_blank" rel="noopener" class="inline-flex items-center space-x-1.5 px-3 py-1.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-xs font-medium transition-colors">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                <span>Facebook</span>
            </a>
        </div>

        <div class="mt-4">
            <p class="text-sm text-gray-500">Or share your referral code: <strong class="text-gray-900">{{ $stats['referral_code'] }}</strong></p>
        </div>
    </div>

    {{-- How Commission Works --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-8">
        <h2 class="text-lg font-bold text-gray-900 mb-4">📊 How You Earn</h2>
        <div class="flex flex-col md:flex-row items-center justify-center gap-4 text-sm">
            <div class="bg-indigo-50 border border-indigo-200 rounded-xl px-5 py-3 text-center">
                <p class="font-extrabold text-indigo-600 text-lg">{{ $referralSettings['l1_rate'] }}%</p>
                <p class="text-xs text-gray-600 font-medium">Level 1</p>
                <p class="text-[10px] text-gray-400">Your direct referrals</p>
            </div>
            <svg class="w-6 h-6 text-gray-300 hidden md:block" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
            @if($referralSettings['max_levels'] >= 2)
            <div class="bg-purple-50 border border-purple-200 rounded-xl px-5 py-3 text-center">
                <p class="font-extrabold text-purple-600 text-lg">{{ $referralSettings['l2_rate'] }}%</p>
                <p class="text-xs text-gray-600 font-medium">Level 2</p>
                <p class="text-[10px] text-gray-400">Their referrals</p>
            </div>
            <svg class="w-6 h-6 text-gray-300 hidden md:block" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
            @endif
            @if($referralSettings['max_levels'] >= 3)
            <div class="bg-amber-50 border border-amber-200 rounded-xl px-5 py-3 text-center">
                <p class="font-extrabold text-amber-600 text-lg">{{ $referralSettings['l3_rate'] }}%</p>
                <p class="text-xs text-gray-600 font-medium">Level 3</p>
                <p class="text-[10px] text-gray-400">Deeper network</p>
            </div>
            @endif
        </div>
        <p class="text-xs text-gray-400 text-center mt-4">You earn commission every time someone in your network makes a deposit.</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- My Referrals -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100">
            <div class="px-6 py-4 border-b border-gray-100">
                <h2 class="text-lg font-semibold text-gray-900">My Referrals ({{ $stats['total_referrals'] }})</h2>
            </div>
            <div class="divide-y divide-gray-100">
                @forelse($referrals as $referral)
                    <div class="px-6 py-4 flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 bg-gray-100 rounded-full flex items-center justify-center">
                                <span class="text-sm font-semibold text-gray-600">{{ substr($referral->name, 0, 1) }}</span>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-900">{{ $referral->name }}</p>
                                <p class="text-xs text-gray-500">{{ $referral->created_at->format('M d, Y') }}</p>
                            </div>
                        </div>
                        <div class="text-right">
                            @if($referral->level)
                                <p class="text-sm font-medium text-indigo-600">Level {{ $referral->level->level }}</p>
                            @endif
                            <p class="text-xs text-gray-500">{{ currency($referral->deposit_amount) }} deposited</p>
                        </div>
                    </div>
                @empty
                    <div class="px-6 py-8 text-center text-gray-500">
                        <p>No referrals yet</p>
                        <p class="text-xs mt-1">Share your referral link to start earning</p>
                    </div>
                @endforelse
            </div>
            @if($referrals->hasPages())
                <div class="px-6 py-3 border-t border-gray-100">
                    {{ $referrals->links() }}
                </div>
            @endif
        </div>

        <!-- Commission History -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100">
            <div class="px-6 py-4 border-b border-gray-100">
                <h2 class="text-lg font-semibold text-gray-900">Commission History</h2>
            </div>
            <div class="divide-y divide-gray-100">
                @forelse($commissions as $commission)
                    <div class="px-6 py-4 flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-900">
                                {{ $commission->referredUser?->display_name ?? $commission->referredUser?->name }}
                                @if($commission->level)
                                    <span class="text-xs px-1.5 py-0.5 rounded-full font-bold
                                        @if($commission->level == 1) bg-indigo-100 text-indigo-700
                                        @elseif($commission->level == 2) bg-purple-100 text-purple-700
                                        @else bg-amber-100 text-amber-700 @endif">
                                        L{{ $commission->level }}
                                    </span>
                                @endif
                            </p>
                            <p class="text-xs text-gray-500">{{ $commission->created_at->format('M d, Y') }} · {{ $commission->rate }}% rate</p>
                        </div>
                        <span class="text-sm font-semibold text-green-600">+{{ currency($commission->amount) }}</span>
                    </div>
                @empty
                    <div class="px-6 py-8 text-center text-gray-500">
                        <p>No commissions earned yet</p>
                    </div>
                @endforelse
            </div>
            @if($commissions->hasPages())
                <div class="px-6 py-3 border-t border-gray-100">
                    {{ $commissions->links() }}
                </div>
            @endif
        </div>
    </div>

    {{-- MLM Level Breakdown --}}
    <div class="mt-8 grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 text-center">
            <div class="w-12 h-12 bg-indigo-100 rounded-xl flex items-center justify-center mx-auto mb-3">
                <span class="text-xl font-bold text-indigo-600">L1</span>
            </div>
            <p class="text-sm text-gray-500">Direct Referrals</p>
            <p class="text-3xl font-extrabold text-gray-900 mt-1">{{ $mlmStats['level1_count'] }}</p>
            <p class="text-xs text-green-600 mt-2 font-bold">{{ $referralSettings['l1_rate'] }}% commission</p>
            <p class="text-sm font-bold text-gray-900 mt-1">{{ currency($mlmStats['level1_earnings']) }} earned</p>
        </div>
        @if($referralSettings['max_levels'] >= 2)
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 text-center">
            <div class="w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center mx-auto mb-3">
                <span class="text-xl font-bold text-purple-600">L2</span>
            </div>
            <p class="text-sm text-gray-500">Their Referrals</p>
            <p class="text-3xl font-extrabold text-gray-900 mt-1">{{ $mlmStats['level2_count'] }}</p>
            <p class="text-xs text-green-600 mt-2 font-bold">{{ $referralSettings['l2_rate'] }}% commission</p>
            <p class="text-sm font-bold text-gray-900 mt-1">{{ currency($mlmStats['level2_earnings']) }} earned</p>
        </div>
        @endif
        @if($referralSettings['max_levels'] >= 3)
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 text-center">
            <div class="w-12 h-12 bg-amber-100 rounded-xl flex items-center justify-center mx-auto mb-3">
                <span class="text-xl font-bold text-amber-600">L3</span>
            </div>
            <p class="text-sm text-gray-500">Deeper Network</p>
            <p class="text-3xl font-extrabold text-gray-900 mt-1">{{ $mlmStats['level3_count'] }}</p>
            <p class="text-xs text-green-600 mt-2 font-bold">{{ $referralSettings['l3_rate'] }}% commission</p>
            <p class="text-sm font-bold text-gray-900 mt-1">{{ currency($mlmStats['level3_earnings']) }} earned</p>
        </div>
        @endif
    </div>

    {{-- Referral Tree --}}
    @if(count($referralTree) > 0)
    <div class="mt-8 bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <h2 class="text-lg font-bold text-gray-900 mb-6">🌳 Your Referral Tree</h2>
        <div class="overflow-x-auto">
            <div class="min-w-[600px]">
                <div class="flex justify-center mb-4">
                    <div class="bg-gradient-to-br from-indigo-600 to-indigo-800 text-white rounded-2xl px-6 py-3 shadow-lg">
                        <p class="text-sm font-extrabold">{{ Auth::user()->display_name }}</p>
                        <p class="text-xs text-indigo-200">Level {{ Auth::user()->level?->level ?? 0 }}</p>
                    </div>
                </div>
                @if(count($referralTree) > 0)
                <div class="flex justify-center"><div class="w-px h-6 bg-gray-300"></div></div>
                <div class="flex justify-center flex-wrap gap-4 mb-4">
                    @foreach($referralTree as $l1)
                        <div class="text-center">
                            <div class="w-px h-6 bg-gray-300 mx-auto"></div>
                            <div class="bg-white border-2 border-indigo-200 rounded-xl px-4 py-2 shadow-sm">
                                <p class="text-xs font-bold text-gray-900">{{ $l1['name'] }}</p>
                                <p class="text-[10px] text-gray-400">Lvl {{ $l1['level_num'] }} · {{ currency($l1['deposit']) }}</p>
                            </div>
                            @if(!empty($l1['children']))
                            <div class="flex justify-center space-x-3 mt-2">
                                @foreach($l1['children'] as $l2)
                                    <div class="text-center">
                                        <div class="w-px h-4 bg-gray-200 mx-auto"></div>
                                        <div class="bg-gray-50 border border-gray-200 rounded-lg px-3 py-1.5 text-xs">
                                            <p class="font-medium text-gray-700">{{ $l2['name'] }}</p>
                                            <p class="text-[10px] text-gray-400">{{ currency($l2['deposit']) }}</p>
                                        </div>
                                        @if(!empty($l2['children']))
                                        <div class="flex justify-center space-x-2 mt-1">
                                            @foreach($l2['children'] as $l3)
                                                <div class="bg-gray-50 border border-dashed border-gray-200 rounded-lg px-2 py-1 text-[10px]">
                                                    <p class="font-medium text-gray-500">{{ $l3['name'] }}</p>
                                                </div>
                                            @endforeach
                                        </div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                            @endif
                        </div>
                    @endforeach
                </div>
                @endif
            </div>
        </div>
    </div>
    @endif
</div>

@push('scripts')
<script>
function copyReferralUrl() {
    const input = document.getElementById('referralUrl');
    input.select();
    navigator.clipboard.writeText(input.value);
    alert('Referral link copied!');
}
</script>
@endpush
@endsection
