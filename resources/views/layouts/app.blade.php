<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'TaskEarn') }} @hasSection('title') - @yield('title') @endif</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:300,400,500,600,700,800&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    {{-- Custom CSS --}}
    @php $customCss = App\Models\AdminSetting::getValue('custom_css'); @endphp
    @if($customCss)<style>{{ $customCss }}</style>@endif
    @stack('styles')
</head>
<body class="h-full font-sans antialiased bg-gray-50 text-gray-900">
    <div class="min-h-full">
        @auth
            @include('layouts.navigation')
        @endauth

        <div class="@auth md:pl-64 @endauth flex flex-col min-h-screen">
            <!-- Page Heading -->
            @hasSection('header')
                <header class="bg-white border-b border-gray-200 shadow-sm">
                    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
                        @yield('header')
                    </div>
                </header>
            @endif

            <!-- Page Content -->
            <main class="flex-1">
                @if(session('success') || session('error'))
                    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-6">
                        @if(session('success'))
                            <div class="p-4 bg-green-50 border border-green-200 text-green-700 rounded-xl flex items-center space-x-2">
                                <svg class="w-5 h-5 shrink-0 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                <span class="text-sm font-medium">{{ session('success') }}</span>
                            </div>
                        @endif

                        @if(session('error'))
                            <div class="p-4 bg-red-50 border border-red-200 text-red-700 rounded-xl flex items-center space-x-2">
                                <svg class="w-5 h-5 shrink-0 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                <span class="text-sm font-medium">{{ session('error') }}</span>
                            </div>
                        @endif
                    </div>
                @endif

                @yield('content')
            </main>

            @hasSection('footer')
                <footer class="bg-white border-t border-gray-200">
                    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
                        @yield('footer')
                    </div>
                </footer>
            @endif
        </div>
    </div>

    @stack('scripts')

    {{-- Custom JS --}}
    @php $customJs = App\Models\AdminSetting::getValue('custom_js'); @endphp
    @if($customJs)<script>{!! $customJs !!}</script>@endif

    {{-- Real-time Notification Polling --}}
    @auth
    <div id="notification-toast" class="fixed bottom-5 right-5 z-50 max-w-sm w-full transform translate-y-20 opacity-0 transition-all duration-300 pointer-events-none">
        <div class="bg-white rounded-2xl shadow-2xl border border-gray-100 p-4 flex items-start space-x-3">
            <div class="w-10 h-10 bg-indigo-100 rounded-xl flex items-center justify-center shrink-0" id="toast-icon"></div>
            <div class="flex-1 min-w-0">
                <p class="text-sm font-bold text-gray-900" id="toast-title"></p>
                <p class="text-xs text-gray-500 mt-0.5" id="toast-message"></p>
            </div>
            <button onclick="this.parentElement.parentElement.classList.add('translate-y-20','opacity-0')" class="text-gray-400 hover:text-gray-600 shrink-0">&times;</button>
        </div>
    </div>

    <script>
        (function() {
            var lastCheck = null;
            var lastCount = {{ Auth::user()->unreadNotificationsCount() }};
            var toastTimer = null;

            function updateBadge(count) {
                // Desktop sidebar badge
                var badges = document.querySelectorAll('#unread-badge, [x-data] .rounded-full');
                // Find the notification count span in the sidebar
                var navLinks = document.querySelectorAll('a[href*="notifications"]');
                navLinks.forEach(function(link) {
                    var existing = link.querySelector('.notification-count');
                    if (existing) {
                        if (count > 0) {
                            existing.textContent = count > 99 ? '99+' : count;
                            existing.classList.remove('hidden');
                        } else {
                            existing.classList.add('hidden');
                        }
                    }
                });

                // Mobile drawer badge
                var mobileLinks = document.querySelectorAll('[x-data] a[href*="notifications"]');
                mobileLinks.forEach(function(link) {
                    var existing = link.querySelector('.notification-count');
                    if (existing) {
                        if (count > 0) {
                            existing.textContent = count > 99 ? '99+' : count;
                            existing.classList.remove('hidden');
                        } else {
                            existing.classList.add('hidden');
                        }
                    }
                });
            }

            function showToast(notif) {
                var toast = document.getElementById('notification-toast');
                if (!toast) return;

                var title = document.getElementById('toast-title');
                var message = document.getElementById('toast-message');
                var icon = document.getElementById('toast-icon');

                var icons = {
                    'task_approved': '✅',
                    'task_rejected': '❌',
                    'deposit_received': '💰',
                    'referral_bonus': '🤝',
                    'withdrawal_status': '🏦',
                    'festive_reward': '🎉',
                    'festive_status': '🎊',
                };

                if (title) title.textContent = notif.title;
                if (message) message.textContent = notif.message || '';
                if (icon) icon.textContent = icons[notif.type] || '🔔';

                toast.classList.remove('translate-y-20', 'opacity-0');

                clearTimeout(toastTimer);
                toastTimer = setTimeout(function() {
                    toast.classList.add('translate-y-20', 'opacity-0');
                }, 5000);

                // Click to go to notification
                toast.onclick = function() {
                    if (notif.action_url) {
                        window.location.href = notif.action_url;
                    } else {
                        window.location.href = '{{ route('notifications.index') }}';
                    }
                };
            }

            function poll() {
                var url = '{{ route('notifications.poll') }}';
                if (lastCheck) url += '?since=' + encodeURIComponent(lastCheck);

                fetch(url, { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } })
                    .then(function(r) { return r.json(); })
                    .then(function(data) {
                        // Update badge
                        if (data.unread_count !== lastCount) {
                            lastCount = data.unread_count;
                            updateBadge(data.unread_count);

                            // Show toast for new notifications
                            if (data.notifications && data.notifications.length > 0) {
                                var newest = data.notifications[0];
                                if (!lastCheck) return; // Skip on first load
                                showToast(newest);
                            }
                        }
                        lastCheck = data.server_time;
                    })
                    .catch(function() { /* ignore errors */ });
            }

            // Poll every 15 seconds
            setInterval(poll, 15000);

            // Initial poll after 5 seconds
            setTimeout(poll, 5000);
        })();
    </script>
    @endauth
</body>
</html>
