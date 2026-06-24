@extends('layouts.app')

@section('title', 'Notifications')

@section('header')
<div class="flex items-center justify-between">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Notifications</h1>
        <p class="text-sm text-gray-500 mt-1">Stay updated with your activity</p>
    </div>
    @if($unreadCount > 0)
        <form action="{{ route('notifications.markAllRead') }}" method="POST">
            @csrf
            <button type="submit" class="text-sm text-indigo-600 hover:text-indigo-500 font-medium">Mark all as read</button>
        </form>
    @endif
</div>
@endsection

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100">
        <div class="divide-y divide-gray-100">
            @forelse($notifications as $notification)
                <div class="px-6 py-4 flex items-start space-x-4 hover:bg-gray-50 {{ !$notification->is_read ? 'bg-indigo-50/30' : '' }}">
                    <div class="shrink-0 mt-0.5">
                        {!! $notification->icon !!}
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-start justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-900">{{ $notification->title }}</p>
                                @if($notification->message)
                                    <p class="text-sm text-gray-600 mt-0.5">{{ $notification->message }}</p>
                                @endif
                            </div>
                            @if(!$notification->is_read)
                                <span class="w-2 h-2 bg-indigo-500 rounded-full shrink-0 mt-2"></span>
                            @endif
                        </div>
                        <div class="flex items-center space-x-3 mt-2">
                            <span class="text-xs text-gray-400">{{ $notification->created_at->diffForHumans() }}</span>
                            @if($notification->action_url)
                                <a href="{{ route('notifications.markRead', $notification) }}" class="text-xs text-indigo-600 hover:text-indigo-500 font-medium">View</a>
                            @endif
                            @if(!$notification->is_read)
                                <a href="{{ route('notifications.markRead', $notification) }}" class="text-xs text-gray-500 hover:text-gray-700">Mark read</a>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="px-6 py-16 text-center">
                    <svg class="w-12 h-12 text-gray-200 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                    </svg>
                    <p class="text-gray-500">No notifications yet</p>
                    <p class="text-xs text-gray-400 mt-1">You'll see updates here when something happens</p>
                </div>
            @endforelse
        </div>

        @if($notifications->hasPages())
            <div class="px-6 py-3 border-t border-gray-100">
                {{ $notifications->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
