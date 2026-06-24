@extends('layouts.app')

@section('title', 'Support Tickets')

@section('header')
<div class="flex items-center justify-between">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Support Tickets</h1>
        <p class="text-sm text-gray-500 mt-1">Get help from our support team</p>
    </div>
    <a href="{{ route('support.create') }}" class="px-4 py-2 bg-indigo-600 text-white rounded-xl hover:bg-indigo-700 text-sm font-medium flex items-center space-x-2">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        <span>New Ticket</span>
    </a>
</div>
@endsection

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    @if($tickets->isEmpty())
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-12 text-center">
            <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <h3 class="text-lg font-medium text-gray-900 mb-2">No support tickets</h3>
            <p class="text-gray-500 mb-6">Have an issue? Create a ticket and we'll help you out.</p>
            <a href="{{ route('support.create') }}" class="px-6 py-2 bg-indigo-600 text-white rounded-xl hover:bg-indigo-700 text-sm font-medium">Create Ticket</a>
        </div>
    @else
        <div class="space-y-4">
            @foreach($tickets as $ticket)
                <a href="{{ route('support.show', $ticket) }}" class="block bg-white rounded-2xl border border-gray-100 p-5 hover:shadow-md transition-shadow">
                    <div class="flex items-start justify-between">
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center space-x-2">
                                <h3 class="text-base font-semibold text-gray-900 truncate">{{ $ticket->subject }}</h3>
                                <span class="text-xs px-2 py-0.5 rounded-full font-medium
                                    @if($ticket->status === 'open') bg-blue-100 text-blue-700
                                    @elseif($ticket->status === 'replied') bg-green-100 text-green-700
                                    @else bg-gray-100 text-gray-600 @endif">
                                    {{ ucfirst($ticket->status) }}
                                </span>
                            </div>
                            <div class="flex items-center space-x-3 mt-2 text-xs text-gray-500">
                                <span class="px-2 py-0.5 bg-gray-50 rounded">{{ \App\Models\SupportTicket::categories()[$ticket->category] ?? $ticket->category }}</span>
                                <span class="flex items-center">
                                    <svg class="w-3 h-3 me-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    {{ $ticket->created_at->diffForHumans() }}
                                </span>
                                @if($ticket->messages)
                                    <span>{{ count($ticket->messages) }} messages</span>
                                @endif
                            </div>
                            @if($ticket->lastMessage())
                                <p class="text-sm text-gray-400 mt-2 truncate">{{ $ticket->lastMessagePreview(100) }}</p>
                            @endif
                        </div>
                        <div class="ml-4 shrink-0">
                            @if($ticket->priority === 'high')
                                <span class="text-xs font-bold text-red-600 bg-red-50 px-2 py-0.5 rounded-full">High</span>
                            @elseif($ticket->priority === 'low')
                                <span class="text-xs text-gray-400 bg-gray-50 px-2 py-0.5 rounded-full">Low</span>
                            @endif
                        </div>
                    </div>
                </a>
            @endforeach
        </div>
        <div class="mt-6">
            {{ $tickets->links() }}
        </div>
    @endif
</div>
@endsection
