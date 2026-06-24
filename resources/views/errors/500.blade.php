@extends('layouts.guest')

@section('title', '500 - Server Error')

@section('content')
<div class="min-h-full flex items-center justify-center px-4 py-16">
    <div class="text-center">
        <p class="text-8xl font-extrabold text-red-500">500</p>
        <h1 class="mt-4 text-2xl font-bold text-gray-900">Server Error</h1>
        <p class="mt-2 text-gray-500">Something went wrong on our end. Please try again later.</p>
        <div class="mt-8">
            <a href="/" class="px-6 py-2.5 bg-indigo-600 text-white rounded-xl hover:bg-indigo-700 text-sm font-semibold">Go Home</a>
        </div>
    </div>
</div>
@endsection
