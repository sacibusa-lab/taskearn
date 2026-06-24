{{-- 🏠 Overview --}}
<div class="px-3 pt-2 pb-1">
    <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">Overview</p>
</div>
<a href="{{ route('admin.dashboard') }}" class="group flex items-center px-3 py-2.5 text-sm font-medium rounded-xl transition-colors {{ request()->routeIs('admin.dashboard') ? 'bg-indigo-600 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
    <svg class="w-5 h-5 me-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
    Dashboard
</a>

{{-- 👥 User Management --}}
<div class="px-3 pt-5 pb-1">
    <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">User Management</p>
</div>
<a href="{{ route('admin.users.index') }}" class="group flex items-center px-3 py-2.5 text-sm font-medium rounded-xl transition-colors {{ request()->routeIs('admin.users.*') ? 'bg-indigo-600 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
    <svg class="w-5 h-5 me-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z"/></svg>
    All Users
</a>
<a href="{{ route('admin.levels.index') }}" class="group flex items-center px-3 py-2.5 text-sm font-medium rounded-xl transition-colors {{ request()->routeIs('admin.levels.*') ? 'bg-indigo-600 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
    <svg class="w-5 h-5 me-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
    Levels & Pricing
</a>
<a href="{{ route('admin.referrals.index') }}" class="group flex items-center px-3 py-2.5 text-sm font-medium rounded-xl transition-colors {{ request()->routeIs('admin.referrals.*') ? 'bg-indigo-600 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
    <svg class="w-5 h-5 me-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
    Referrals
</a>

{{-- 📋 Task Management --}}
<div class="px-3 pt-5 pb-1">
    <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">Task Management</p>
</div>
<a href="{{ route('admin.tasks.index') }}" class="group flex items-center px-3 py-2.5 text-sm font-medium rounded-xl transition-colors {{ request()->routeIs('admin.tasks.*') && !request()->routeIs('admin.submissions') ? 'bg-indigo-600 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
    <svg class="w-5 h-5 me-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
    Manage Tasks
</a>
<a href="{{ route('admin.submissions') }}" class="group flex items-center px-3 py-2.5 text-sm font-medium rounded-xl transition-colors {{ request()->routeIs('admin.submissions') ? 'bg-indigo-600 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
    <svg class="w-5 h-5 me-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
    Submissions
    @php $pendingCount = \App\Models\TaskSubmission::pending()->count(); @endphp
    @if($pendingCount > 0)
        <span class="ms-auto bg-red-500 text-white text-xs px-2 py-0.5 rounded-full">{{ $pendingCount }}</span>
    @endif
</a>

{{-- 🎉 Promotions & Rewards --}}
<div class="px-3 pt-5 pb-1">
    <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">Promotions</p>
</div>
<a href="{{ route('admin.withdrawals.index') }}" class="group flex items-center px-3 py-2.5 text-sm font-medium rounded-xl transition-colors {{ request()->routeIs('admin.withdrawals.*') ? 'bg-indigo-600 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
    <svg class="w-5 h-5 me-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
    Withdrawals
    @php $pendingW = \App\Models\Withdrawal::pending()->count(); @endphp
    @if($pendingW > 0)
        <span class="ms-auto bg-red-500 text-white text-xs px-2 py-0.5 rounded-full">{{ $pendingW }}</span>
    @endif
</a>
<a href="{{ route('admin.festive-programs.index') }}" class="group flex items-center px-3 py-2.5 text-sm font-medium rounded-xl transition-colors {{ request()->routeIs('admin.festive-programs.*') ? 'bg-indigo-600 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
    <svg class="w-5 h-5 me-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/></svg>
    Festive Programs
</a>
<a href="{{ route('admin.announcements.index') }}" class="group flex items-center px-3 py-2.5 text-sm font-medium rounded-xl transition-colors {{ request()->routeIs('admin.announcements.*') ? 'bg-indigo-600 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
    <svg class="w-5 h-5 me-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/></svg>
    Announcements
</a>

{{-- 🎫 Support --}}
<div class="px-3 pt-5 pb-1">
    <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">Support</p>
</div>
<a href="{{ route('admin.support.index') }}" class="group flex items-center px-3 py-2.5 text-sm font-medium rounded-xl transition-colors {{ request()->routeIs('admin.support.*') ? 'bg-indigo-600 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
    <svg class="w-5 h-5 me-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
    Support Tickets
    @php $openTickets = \App\Models\SupportTicket::open()->count(); @endphp
    @if($openTickets > 0)
        <span class="ms-auto bg-red-500 text-white text-xs px-2 py-0.5 rounded-full">{{ $openTickets }}</span>
    @endif
</a>

{{-- 🏆 Leaderboard --}}
<div class="px-3 pt-5 pb-1">
    <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">Engagement</p>
</div>
<a href="{{ route('admin.leaderboard.index') }}" class="group flex items-center px-3 py-2.5 text-sm font-medium rounded-xl transition-colors {{ request()->routeIs('admin.leaderboard.*') ? 'bg-indigo-600 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
    <svg class="w-5 h-5 me-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
    Leaderboard
</a>

{{-- ⚙️ Configuration --}}
<div class="px-3 pt-5 pb-1">
    <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">Configuration</p>
</div>
<a href="{{ route('admin.settings.index') }}" class="group flex items-center px-3 py-2.5 text-sm font-medium rounded-xl transition-colors {{ request()->routeIs('admin.settings.*') ? 'bg-indigo-600 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
    <svg class="w-5 h-5 me-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
    System Settings
</a>
