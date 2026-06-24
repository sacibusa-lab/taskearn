@extends('layouts.app')

@section('title', 'Ticket: ' . $ticket->subject)

@section('header')
<div>
    <a href="{{ route('support.index') }}" class="text-sm text-indigo-600 hover:text-indigo-500 font-medium mb-2 inline-block">&larr; Back to Tickets</a>
    <h1 class="text-2xl font-bold text-gray-900">{{ $ticket->subject }}</h1>
</div>
@endsection

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    {{-- Ticket Info Bar --}}
    <div class="bg-white rounded-2xl border border-gray-100 p-5 mb-6 shadow-sm flex items-center justify-between flex-wrap gap-4">
        <div class="flex items-center space-x-4 text-sm">
            <div>
                <span class="text-gray-400 text-xs">Category</span>
                <p class="font-medium text-gray-900">{{ \App\Models\SupportTicket::categories()[$ticket->category] ?? $ticket->category }}</p>
            </div>
            <div class="w-px h-8 bg-gray-100"></div>
            <div>
                <span class="text-gray-400 text-xs">Priority</span>
                <p class="font-medium 
                    @if($ticket->priority === 'high') text-red-600
                    @elseif($ticket->priority === 'low') text-gray-500
                    @else text-amber-600 @endif">
                    {{ ucfirst($ticket->priority) }}
                </p>
            </div>
            <div class="w-px h-8 bg-gray-100"></div>
            <div>
                <span class="text-gray-400 text-xs">Status</span>
                <p class="font-medium 
                    @if($ticket->status === 'open') text-blue-600
                    @elseif($ticket->status === 'replied') text-green-600
                    @else text-gray-600 @endif">
                    {{ ucfirst($ticket->status) }}
                </p>
            </div>
            <div class="w-px h-8 bg-gray-100"></div>
            <div>
                <span class="text-gray-400 text-xs">Created</span>
                <p class="font-medium text-gray-900">{{ $ticket->created_at->format('M d, Y h:i A') }}</p>
            </div>
        </div>
        @if($ticket->status !== 'closed')
            <form action="{{ route('support.close', $ticket) }}" method="POST" onsubmit="return confirm('Close this ticket?')">
                @csrf
                <button type="submit" class="text-sm text-gray-500 hover:text-red-600 font-medium">Close Ticket</button>
            </form>
        @endif
    </div>

    {{-- Chat Messages --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="p-6 space-y-6 max-h-[600px] overflow-y-auto" id="chat-messages">
            @forelse(($ticket->messages ?? []) as $msg)
                <div class="flex {{ $msg['sender_type'] === 'user' ? 'justify-end' : 'justify-start' }}">
                    <div class="max-w-[80%] {{ $msg['sender_type'] === 'user' 
                        ? 'bg-indigo-600 text-white rounded-2xl rounded-br-sm' 
                        : 'bg-gray-100 text-gray-900 rounded-2xl rounded-bl-sm' }} 
                        px-5 py-3.5">
                        <div class="flex items-center space-x-2 mb-1">
                            <span class="text-xs font-bold opacity-70">
                                {{ $msg['sender_type'] === 'user' ? 'You' : 'Support Team' }}
                            </span>
                            <span class="text-xs opacity-50">
                                {{ isset($msg['created_at']) ? \Carbon\Carbon::parse($msg['created_at'])->diffForHumans() : '' }}
                            </span>
                        </div>
                        <p class="text-sm leading-relaxed whitespace-pre-wrap">{{ $msg['message'] }}</p>
                    </div>
                </div>
            @empty
                <div class="text-center py-8 text-gray-400">
                    <p>No messages yet.</p>
                </div>
            @endforelse
        </div>

        {{-- Reply Form --}}
        @if($ticket->status !== 'closed')
            <div class="border-t border-gray-100 p-6">
                <form action="{{ route('support.reply', $ticket) }}" method="POST">
                    @csrf
                    <div class="flex items-end space-x-3">
                        <div class="flex-1">
                            <textarea name="message" rows="3" required class="w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 resize-none" placeholder="Type your reply..."></textarea>
                        </div>
                        <button type="submit" class="px-5 py-3 bg-indigo-600 text-white rounded-xl hover:bg-indigo-700 text-sm font-medium shrink-0 flex items-center space-x-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                            <span>Send</span>
                        </button>
                    </div>
                </form>
            </div>
        @else
            <div class="border-t border-gray-100 p-6 bg-gray-50 text-center">
                <p class="text-sm text-gray-500">This ticket is closed.</p>
            </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Auto-scroll to bottom of chat
    document.addEventListener('DOMContentLoaded', function() {
        const container = document.getElementById('chat-messages');
        if (container) {
            container.scrollTop = container.scrollHeight;
        }
    });
</script>
@endpush
