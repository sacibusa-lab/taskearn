@extends('layouts.guest')

@section('title', '404 - Page Not Found')

@section('content')
<div class="min-h-full flex items-center justify-center px-4 py-16">
    <div class="text-center">
        <p class="text-8xl font-extrabold text-indigo-600">404</p>
        <h1 class="mt-4 text-2xl font-bold text-gray-900">Page Not Found</h1>
        <p class="mt-2 text-gray-500">Sorry, the page you're looking for doesn't exist or has been moved.</p>
        <div class="mt-8 flex items-center justify-center space-x-4">
            <a href="/" class="px-6 py-2.5 bg-indigo-600 text-white rounded-xl hover:bg-indigo-700 text-sm font-semibold">Go Home</a>
            <a href="javascript:history.back()" class="px-6 py-2.5 border border-gray-300 text-gray-700 rounded-xl hover:bg-gray-50 text-sm font-medium">Go Back</a>
        </div>
    </div>
</div>
@endsection
