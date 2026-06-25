<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'TaskEarn') }} - Earn by Completing Tasks</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:300,400,500,600,700,800&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full font-sans antialiased bg-gradient-to-br from-indigo-50 via-white to-purple-50">
    <div class="min-h-full flex flex-col">
        <nav class="px-4 sm:px-6 lg:px-8 py-4">
            <div class="max-w-7xl mx-auto flex items-center justify-between">
                <a href="/" class="flex items-center space-x-2">
                    <div class="w-8 h-8 bg-indigo-600 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                        </svg>
                    </div>
                    <span class="text-xl font-bold text-gray-900">{{ config('app.name', 'TaskEarn') }}</span>
                </a>
                @if (Route::has('login'))
                    <div class="flex items-center space-x-4">
                        @auth
                            <a href="{{ url('/dashboard') }}" class="px-4 py-2 bg-indigo-600 text-white rounded-xl hover:bg-indigo-700 text-sm font-semibold transition-colors">Dashboard</a>
                        @else
                            <a href="{{ route('login') }}" class="text-sm font-medium text-gray-600 hover:text-gray-900">Sign in</a>
                            @if (Route::has('register'))
                                <a href="{{ route('register') }}" class="px-4 py-2 bg-indigo-600 text-white rounded-xl hover:bg-indigo-700 text-sm font-semibold transition-colors">Get Started</a>
                            @endif
                        @endauth
                    </div>
                @endif
            </div>
        </nav>

        <main class="flex-1">
            <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 lg:py-24">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                    <div>
                        <div class="inline-flex items-center space-x-2 bg-indigo-100 text-indigo-700 px-4 py-1.5 rounded-full text-sm font-medium mb-6">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <span>Trusted by 3000+ users</span>
                        </div>
                        <h1 class="text-4xl lg:text-6xl font-extrabold text-gray-900 leading-tight">
                            Earn Money<br>
                            <span class="text-indigo-600">Completing Tasks</span>
                        </h1>
                        <p class="mt-6 text-lg text-gray-600 leading-relaxed max-w-lg">
                            Join the platform where you get paid for performing simple tasks. Make an initial deposit, complete your probation, and start earning weekly payouts.
                        </p>
                        <div class="mt-8 flex flex-col sm:flex-row items-center space-y-4 sm:space-y-0 sm:space-x-4">
                            @guest
                                <a href="{{ route('register') }}" class="w-full sm:w-auto px-8 py-3.5 bg-indigo-600 text-white rounded-xl hover:bg-indigo-700 font-semibold text-center transition-colors shadow-lg shadow-indigo-200">
                                    Start Earning Now
                                </a>
                                <a href="{{ route('login') }}" class="w-full sm:w-auto px-8 py-3.5 border border-gray-300 text-gray-700 rounded-xl hover:bg-gray-50 font-medium text-center transition-colors">
                                    Sign In
                                </a>
                            @endguest
                        </div>
                    </div>
                    <div class="relative">
                        @php
                            $level1 = \App\Models\Level::where('level', 1)->first();
                            $topLevel = \App\Models\Level::orderByDesc('level')->first();
                            $refBonus = \App\Models\AdminSetting::getValue('referral_commission_rate', 10);
                        @endphp
                        <div class="bg-white rounded-3xl shadow-2xl border border-gray-100 p-8">
                            <div class="space-y-4">
                                <div class="flex items-center justify-between p-4 bg-green-50 rounded-xl">
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">Level 1 Deposit</p>
                                        <p class="text-xs text-gray-500">Start with a small deposit</p>
                                    </div>
                                    <span class="text-lg font-bold text-green-600">{{ $level1 ? currency($level1->deposit_amount) : currency_raw(100) }}</span>
                                </div>
                                <div class="flex items-center justify-between p-4 bg-indigo-50 rounded-xl">
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">Weekly Payout</p>
                                        <p class="text-xs text-gray-500">Earn every week</p>
                                    </div>
                                    <span class="text-lg font-bold text-indigo-600">{{ $level1 ? currency($level1->weekly_payout) : currency_raw(10) }}</span>
                                </div>
                                <div class="flex items-center justify-between p-4 bg-purple-50 rounded-xl">
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">Top Level Deposit</p>
                                        <p class="text-xs text-gray-500">Maximum earning potential</p>
                                    </div>
                                    <span class="text-lg font-bold text-purple-600">{{ $topLevel ? currency($topLevel->deposit_amount) : currency_raw(2500) }}</span>
                                </div>
                                <div class="flex items-center justify-between p-4 bg-amber-50 rounded-xl">
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">Top Weekly Payout</p>
                                        <p class="text-xs text-gray-500">Maximum weekly earnings</p>
                                    </div>
                                    <span class="text-lg font-bold text-amber-600">{{ $topLevel ? currency($topLevel->weekly_payout) : currency_raw(375) }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <section class="bg-white border-t border-gray-100 py-16">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="text-center mb-12">
                        <h2 class="text-3xl font-bold text-gray-900">How It Works</h2>
                        <p class="mt-4 text-gray-500 max-w-lg mx-auto">Start earning in just a few simple steps</p>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                        <div class="text-center">
                            <div class="w-16 h-16 bg-indigo-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                                <span class="text-2xl font-bold text-indigo-600">1</span>
                            </div>
                            <h3 class="text-lg font-semibold text-gray-900">Create Account</h3>
                            <p class="mt-2 text-sm text-gray-500">Sign up and make your initial deposit. Choose from multiple levels.</p>
                        </div>
                        <div class="text-center">
                            <div class="w-16 h-16 bg-indigo-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                                <span class="text-2xl font-bold text-indigo-600">2</span>
                            </div>
                            <h3 class="text-lg font-semibold text-gray-900">3-Day Probation</h3>
                            <p class="mt-2 text-sm text-gray-500">Wait for the probation period. During this time, refer others and earn commission!</p>
                        </div>
                        <div class="text-center">
                            <div class="w-16 h-16 bg-indigo-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                                <span class="text-2xl font-bold text-indigo-600">3</span>
                            </div>
                            <h3 class="text-lg font-semibold text-gray-900">Start Earning</h3>
                            <p class="mt-2 text-sm text-gray-500">Complete tasks, earn rewards, and get weekly payouts based on your level.</p>
                        </div>
                    </div>
                </div>
            </section>

            <section class="py-16">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="bg-gradient-to-r from-indigo-500 to-purple-600 rounded-3xl p-8 lg:p-12 text-white">
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 items-center">
                            <div>
                                <h2 class="text-3xl font-bold">Refer & Earn More</h2>
                                <p class="mt-4 text-indigo-100">Invite your friends and earn a commission on every deposit they make.</p>
                                <ul class="mt-6 space-y-3">
                                    <li class="flex items-center space-x-3">
                                        <svg class="w-5 h-5 text-indigo-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                        <span>Earn commission on referred users' deposits</span>
                                    </li>
                                    <li class="flex items-center space-x-3">
                                        <svg class="w-5 h-5 text-indigo-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                        <span>Commission rate set by admin</span>
                                    </li>
                                    <li class="flex items-center space-x-3">
                                        <svg class="w-5 h-5 text-indigo-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                        <span>Special festive rewards & bonuses</span>
                                    </li>
                                </ul>
                            </div>
                            <div class="bg-white/10 rounded-2xl p-6 text-center">
                                <p class="text-5xl font-bold">{{ $refBonus }}%</p>
                                <p class="mt-2 text-indigo-200">Referral commission rate</p>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <footer class="bg-white border-t border-gray-100 py-8">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center text-sm text-gray-500 space-y-2">
                    <div class="flex items-center justify-center space-x-4">
                        <a href="{{ route('terms') }}" class="hover:text-gray-700">Terms of Service</a>
                        <a href="{{ route('privacy') }}" class="hover:text-gray-700">Privacy Policy</a>
                    </div>
                    <p>&copy; {{ date('Y') }} {{ config('app.name', 'TaskEarn') }}. All rights reserved.</p>
                </div>
            </footer>
        </main>
    </div>
</body>
</html>
