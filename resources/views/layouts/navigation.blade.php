{{-- Mobile Top Bar (visible on small screens only) --}}
<div x-data="{ mobileOpen: false }" class="md:hidden">
    <div class="bg-white border-b border-gray-200 shadow-sm px-4 py-3 flex items-center justify-between">
        <div class="flex items-center space-x-3">
            <button @click="mobileOpen = !mobileOpen" class="p-1.5 rounded-lg text-gray-500 hover:bg-gray-100">
                <svg class="w-6 h-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                    <path :class="{'hidden': mobileOpen, 'inline-flex': !mobileOpen}" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    <path :class="{'hidden': !mobileOpen, 'inline-flex': mobileOpen}" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
            <a href="{{ route('dashboard') }}" class="flex items-center space-x-2">
                <div class="w-7 h-7 bg-indigo-600 rounded-lg flex items-center justify-center">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                    </svg>
                </div>
                <span class="text-lg font-bold text-gray-900">{{ App\Models\AdminSetting::getValue('site_name', 'TaskEarn') }}</span>
            </a>
        </div>
        <div class="flex items-center space-x-2">
            <a href="{{ route('notifications.index') }}" class="relative p-1.5 text-gray-500">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                @php $unreadN = Auth::user()->unreadNotificationsCount(); @endphp
                @if($unreadN > 0)
                    <span class="absolute -top-0.5 -right-0.5 w-4 h-4 bg-red-500 text-white text-[9px] font-bold rounded-full flex items-center justify-center">{{ $unreadN > 9 ? '9+' : $unreadN }}</span>
                @endif
            </a>
        </div>
    </div>

    {{-- Mobile drawer overlay --}}
    <template x-teleport="body">
        <div x-show="mobileOpen" x-cloak class="fixed inset-0 z-50 md:hidden">
            <div class="absolute inset-0 bg-black/40" @click="mobileOpen = false"></div>
            <div class="absolute left-0 top-0 bottom-0 w-72 bg-white shadow-2xl overflow-y-auto">
                {{-- Mobile sidebar content --}}
                <div class="p-4 border-b border-gray-100">
                    <div class="flex items-center justify-between">
                        <a href="{{ route('dashboard') }}" class="flex items-center space-x-2">
                            <div class="w-8 h-8 bg-indigo-600 rounded-lg flex items-center justify-center">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                            </div>
                            <span class="text-lg font-bold text-gray-900">{{ App\Models\AdminSetting::getValue('site_name', 'TaskEarn') }}</span>
                        </a>
                        <button @click="mobileOpen = false" class="p-1 rounded-lg text-gray-400 hover:bg-gray-100">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>
                    <div class="mt-3 flex items-center space-x-3 p-3 bg-gray-50 rounded-xl">
                        <div class="w-9 h-9 bg-indigo-100 rounded-full flex items-center justify-center text-indigo-600 font-bold text-sm">{{ substr(Auth::user()->name, 0, 1) }}</div>
                        <div>
                            <p class="text-sm font-medium text-gray-900">{{ Auth::user()->name }}</p>
                            <p class="text-xs text-gray-500">
                                @if(Auth::user()->level) Level {{ Auth::user()->level->level }} · @endif
                                Balance: {{ currency(Auth::user()->balance) }}
                            </p>
                        </div>
                    </div>
                </div>
                <div class="p-3 space-y-1">
                    <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                        <svg class="w-5 h-5 me-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2H6a2 2 0 01-2-2v-4zM14 16a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2h-2a2 2 0 01-2-2v-4z"/></svg>
                        {{ __('Dashboard') }}
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('tasks.index')" :active="request()->routeIs('tasks.*')">
                        <svg class="w-5 h-5 me-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                        {{ __('Tasks') }}
                        @php
                            $user = Auth::user();
                            $availableTasksMobile = \App\Models\Task::active()->available()
                                ->whereDoesntHave('submissions', fn($q) => $q->where('user_id', $user->id))
                                ->where(function ($q) use ($user) {
                                if ($user->level) {
                                    $q->whereNull('level_id')->orWhere('level_id', '<=', $user->level->id);
                                } else {
                                    $q->whereNull('level_id');
                                }
                            })->count();
                        @endphp
                        @if($availableTasksMobile > 0)
                            <span class="ms-auto bg-indigo-500 text-white text-xs px-2 py-0.5 rounded-full">{{ $availableTasksMobile }}</span>
                        @endif
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('deposits.create')" :active="request()->routeIs('deposits.*')">
                        <svg class="w-5 h-5 me-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        {{ __('Deposit') }}
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('withdrawals.index')" :active="request()->routeIs('withdrawals.*')">
                        <svg class="w-5 h-5 me-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
                        {{ __('Withdraw') }}
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('leaderboard.index')" :active="request()->routeIs('leaderboard.*')">
                        <svg class="w-5 h-5 me-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                        {{ __('Leaderboard') }}
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('referrals.index')" :active="request()->routeIs('referrals.*')">
                        <svg class="w-5 h-5 me-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        {{ __('Referrals') }}
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('support.index')" :active="request()->routeIs('support.*')">
                        <svg class="w-5 h-5 me-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        {{ __('Support') }}
                        @php $repliedTickets = \App\Models\SupportTicket::forUser(Auth::id())->where('status', 'replied')->count(); @endphp
                        @if($repliedTickets > 0)
                            <span class="ms-auto bg-red-500 text-white text-xs px-2 py-0.5 rounded-full">{{ $repliedTickets }}</span>
                        @endif
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('notifications.index')" :active="request()->routeIs('notifications.*')">
                        <svg class="w-5 h-5 me-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                        {{ __('Notifications') }}
                        @php $unreadN = Auth::user()->unreadNotificationsCount(); @endphp
                        @if($unreadN > 0)
                            <span class="ms-auto bg-red-500 text-white text-xs px-2 py-0.5 rounded-full notification-count">{{ $unreadN }}</span>
                        @else
                            <span class="hidden ms-auto bg-red-500 text-white text-xs px-2 py-0.5 rounded-full notification-count"></span>
                        @endif
                    </x-responsive-nav-link>
                    @if(Auth::user()->is_admin)
                        <div class="border-t border-gray-100 my-2"></div>
                        <x-responsive-nav-link :href="route('admin.dashboard')" :active="request()->routeIs('admin.*')">
                            <svg class="w-5 h-5 me-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            {{ __('Admin Panel') }}
                        </x-responsive-nav-link>
                    @endif
                    <div class="border-t border-gray-100 my-2"></div>
                    <x-responsive-nav-link :href="route('profile.edit')">
                        <svg class="w-5 h-5 me-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        {{ __('Profile') }}
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('transactions.index')">
                        <svg class="w-5 h-5 me-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                        {{ __('Transactions') }}
                    </x-responsive-nav-link>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <x-responsive-nav-link :href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();">
                            <svg class="w-5 h-5 me-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                            {{ __('Log Out') }}
                        </x-responsive-nav-link>
                    </form>
                </div>
            </div>
        </div>
    </template>
</div>

{{-- Desktop Left Sidebar --}}
<div class="hidden md:flex md:w-64 md:flex-col md:fixed md:inset-y-0 z-30">
    <div class="flex-1 flex flex-col min-h-0 bg-white border-r border-gray-200 shadow-sm">
        {{-- Brand --}}
        <div class="flex items-center h-16 shrink-0 px-5 border-b border-gray-100">
            <a href="{{ route('dashboard') }}" class="flex items-center space-x-2.5">
                <div class="w-9 h-9 bg-indigo-600 rounded-xl flex items-center justify-center shadow-md">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                    </svg>
                </div>
                <span class="text-lg font-extrabold text-gray-900">{{ App\Models\AdminSetting::getValue('site_name', 'TaskEarn') }}</span>
            </a>
        </div>

        {{-- User info card --}}
        <div class="px-4 pt-4 pb-2">
            <div class="flex items-center space-x-3 p-3 bg-gradient-to-r from-indigo-50 to-blue-50 rounded-2xl">
                <div class="w-10 h-10 bg-indigo-600 rounded-full flex items-center justify-center text-white font-bold text-sm shadow-sm">
                    {{ substr(Auth::user()->name, 0, 1) }}
                </div>
                <div class="min-w-0 flex-1">
                    <p class="text-sm font-semibold text-gray-900 truncate">{{ Auth::user()->name }}</p>
                    <p class="text-xs text-gray-500">
                        @if(Auth::user()->level) Level {{ Auth::user()->level->level }} · @endif
                        <span class="text-indigo-600 font-semibold">{{ currency(Auth::user()->balance) }}</span>
                    </p>
                </div>
            </div>
        </div>

        {{-- Navigation --}}
        <div class="flex-1 overflow-y-auto px-3 py-4 space-y-1">
            {{-- Main --}}
            <div class="px-3 pb-1 pt-1">
                <p class="text-[10px] font-bold uppercase tracking-wider text-gray-400">Main</p>
            </div>

            <a href="{{ route('dashboard') }}" class="flex items-center space-x-3 px-4 py-2.5 text-sm font-semibold rounded-2xl transition-all duration-200 {{ request()->routeIs('dashboard') ? 'bg-indigo-50 text-indigo-600' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2H6a2 2 0 01-2-2v-4zM14 16a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2h-2a2 2 0 01-2-2v-4z"/></svg>
                <span>Dashboard</span>
            </a>

            <a href="{{ route('tasks.index') }}" class="flex items-center space-x-3 px-4 py-2.5 text-sm font-semibold rounded-2xl transition-all duration-200 {{ request()->routeIs('tasks.*') ? 'bg-indigo-50 text-indigo-600' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                <span>Tasks</span>
                @php
                    $user = Auth::user();
                    $availableTasksDesktop = \App\Models\Task::active()->available()
                        ->whereDoesntHave('submissions', fn($q) => $q->where('user_id', $user->id))
                        ->where(function ($q) use ($user) {
                        if ($user->level) {
                            $q->whereNull('level_id')->orWhere('level_id', '<=', $user->level->id);
                        } else {
                            $q->whereNull('level_id');
                        }
                    })->count();
                @endphp
                @if($availableTasksDesktop > 0)
                    <span class="ms-auto inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-700">{{ $availableTasksDesktop }}</span>
                @endif
            </a>

            <a href="{{ route('leaderboard.index') }}" class="flex items-center space-x-3 px-4 py-2.5 text-sm font-semibold rounded-2xl transition-all duration-200 {{ request()->routeIs('leaderboard.*') ? 'bg-indigo-50 text-indigo-600' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                <span>Leaderboard</span>
            </a>

            <a href="{{ route('referrals.index') }}" class="flex items-center space-x-3 px-4 py-2.5 text-sm font-semibold rounded-2xl transition-all duration-200 {{ request()->routeIs('referrals.*') ? 'bg-indigo-50 text-indigo-600' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                <span>Referrals</span>
            </a>

            {{-- Finance --}}
            <div class="px-3 pb-1 pt-5">
                <p class="text-[10px] font-bold uppercase tracking-wider text-gray-400">Finance</p>
            </div>

            <a href="{{ route('deposits.create') }}" class="flex items-center space-x-3 px-4 py-2.5 text-sm font-semibold rounded-2xl transition-all duration-200 {{ request()->routeIs('deposits.*') ? 'bg-indigo-50 text-indigo-600' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                <span>Deposit</span>
            </a>

            <a href="{{ route('withdrawals.index') }}" class="flex items-center space-x-3 px-4 py-2.5 text-sm font-semibold rounded-2xl transition-all duration-200 {{ request()->routeIs('withdrawals.*') ? 'bg-indigo-50 text-indigo-600' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
                <span>Withdraw</span>
            </a>

            <a href="{{ route('transactions.index') }}" class="flex items-center space-x-3 px-4 py-2.5 text-sm font-semibold rounded-2xl transition-all duration-200 {{ request()->routeIs('transactions.*') ? 'bg-indigo-50 text-indigo-600' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                <span>Transactions</span>
            </a>

            {{-- Support --}}
            <div class="px-3 pb-1 pt-5">
                <p class="text-[10px] font-bold uppercase tracking-wider text-gray-400">Support</p>
            </div>

            <a href="{{ route('support.index') }}" class="flex items-center space-x-3 px-4 py-2.5 text-sm font-semibold rounded-2xl transition-all duration-200 {{ request()->routeIs('support.*') ? 'bg-indigo-50 text-indigo-600' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span>Support</span>
                @php $repliedTickets = \App\Models\SupportTicket::forUser(Auth::id())->where('status', 'replied')->count(); @endphp
                @if($repliedTickets > 0)
                    <span class="ms-auto inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-700">{{ $repliedTickets }}</span>
                @endif
            </a>

            <a href="{{ route('notifications.index') }}" class="flex items-center space-x-3 px-4 py-2.5 text-sm font-semibold rounded-2xl transition-all duration-200 {{ request()->routeIs('notifications.*') ? 'bg-indigo-50 text-indigo-600' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                <span>Notifications</span>
                @php $unreadNav = Auth::user()->unreadNotificationsCount(); @endphp
                @if($unreadNav > 0)
                    <span class="ms-auto inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-700 notification-count">{{ $unreadNav }}</span>
                @else
                    <span class="hidden ms-auto inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-700 notification-count"></span>
                @endif
            </a>
        </div>

        {{-- Bottom section --}}
        <div class="shrink-0 border-t border-gray-100 p-3 space-y-1">
            <a href="{{ route('profile.edit') }}" class="flex items-center space-x-3 px-4 py-2.5 text-sm font-semibold rounded-2xl transition-all duration-200 {{ request()->routeIs('profile.*') ? 'bg-indigo-50 text-indigo-600' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-900' }}">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                <span>Settings</span>
            </a>

            @if(Auth::user()->is_admin)
                <a href="{{ route('admin.dashboard') }}" class="flex items-center space-x-3 px-4 py-2.5 text-sm font-semibold rounded-2xl transition-all duration-200 {{ request()->routeIs('admin.*') ? 'bg-indigo-50 text-indigo-600' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-900' }}">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    <span>Admin Panel</span>
                </a>
            @endif

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="w-full flex items-center space-x-3 px-4 py-2.5 text-sm font-semibold rounded-2xl text-gray-500 hover:bg-red-50 hover:text-red-600 transition-all duration-200 text-left">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                    <span>Logout</span>
                </button>
            </form>
        </div>
    </div>
</div>
