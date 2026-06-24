@extends('layouts.guest')

@section('title', 'Terms of Service')

@section('content')
<div class="min-h-full flex flex-col items-center pt-6 px-4 pb-12">
    <div class="w-full max-w-3xl">
        <div class="text-center mb-8">
            <a href="/" class="inline-flex items-center space-x-2">
                <div class="w-10 h-10 bg-indigo-600 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                </div>
                <span class="text-2xl font-bold text-gray-900">{{ App\Models\AdminSetting::getValue('site_name', 'TaskEarn') }}</span>
            </a>
        </div>

        <div class="bg-white p-8 rounded-2xl shadow-lg border border-gray-100">
            <h1 class="text-2xl font-bold text-gray-900 mb-1">Terms of Service</h1>
            <p class="text-sm text-gray-500 mb-6">Last updated: {{ date('F d, Y') }}</p>

            <div class="prose prose-sm max-w-none text-gray-700">
                {!! App\Models\AdminSetting::getValue('terms_of_service', '<p>Terms of Service content has not been set yet.</p>') !!}
            </div>

            <div class="mt-8 pt-6 border-t border-gray-100 text-center">
                <a href="{{ route('register') }}" class="inline-flex items-center px-6 py-2.5 bg-indigo-600 text-white rounded-xl hover:bg-indigo-700 text-sm font-semibold transition-colors">Back to Registration</a>
            </div>
        </div>
    </div>
</div>
@endsection
