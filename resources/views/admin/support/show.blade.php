@extends('admin.layouts.admin')

@section('title', 'Ticket: ' . $ticket->subject)

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <a href="{{ route('admin.support.index') }}" class="text-sm text-indigo-600 hover:text-indigo-500 font-medium mb-4 inline-block">&larr; Back to Tickets</a>

    {{-- Ticket Info --}}
    <div class="bg-white rounded-2xl border border-gray-100 p-6 mb-6 shadow-sm">
        <div class="flex items-start justify-between mb-4">
            <div>
                <h2 class="text-xl font-bold text-gray-900">{{ $ticket->subject }}</h2>
                <p class="text-sm text-gray-500 mt-1">
                    by {{ $ticket->user?->name ?? 'Unknown' }} ({{ $ticket->user?->phone ?? 'N/A' }})
                </p>
            </div>
            <div class="flex items-center space-x-2">
                <span class="text-xs px-2 py-0.5 rounded-full font-medium
                    @if($ticket->status === 'open') bg-blue-100 text-blue-700
                    @elseif($ticket->status === 'replied') bg-green-100 text-green-700
                    @else bg-gray-100 text-gray-600 @endif">
                    {{ ucfirst($ticket->status) }}
                </span>
                <span class="text-xs px-2 py-0.5 rounded-full font-medium
                    @if($ticket->priority === 'high') bg-red-100 text-red-700
                    @elseif($ticket->priority === 'medium') bg-amber-100 text-amber-700
                    @else bg-gray-100 text-gray-600 @endif">
                    {{ ucfirst($ticket->priority) }} priority
                </span>
            </div>
        </div>
        <div class="grid grid-cols-3 gap-4 text-sm">
            <div>
                <span class="text-gray-400 text-xs">Category</span>
                <p class="font-medium text-gray-700">{{ \App\Models\SupportTicket::categories()[$ticket->category] ?? $ticket->category }}</p>
            </div>
            <div>
                <span class="text-gray-400 text-xs">Created</span>
                <p class="font-medium text-gray-700">{{ $ticket->created_at->format('M d, Y h:i A') }}</p>
            </div>
            <div>
                <span class="text-gray-400 text-xs">Messages</span>
                <p class="font-medium text-gray-700">{{ $ticket->messages ? count($ticket->messages) : 0 }}</p>
            </div>
        </div>
    </div>

    {{-- Chat Messages --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="p-6 space-y-6 max-h-[600px] overflow-y-auto" id="chat-messages">
            @forelse(($ticket->messages ?? []) as $msg)
                <div class="flex {{ $msg['sender_type'] === 'admin' ? 'justify-end' : 'justify-start' }}">
                    <div class="max-w-[80%] {{ $msg['sender_type'] === 'admin' 
                        ? 'bg-indigo-600 text-white rounded-2xl rounded-br-sm' 
                        : 'bg-gray-100 text-gray-900 rounded-2xl rounded-bl-sm' }} 
                        px-5 py-3.5">
                        <div class="flex items-center space-x-2 mb-1">
                            <span class="text-xs font-bold opacity-70">
                                {{ $msg['sender_type'] === 'admin' ? 'You (Admin)' : $ticket->user?->name ?? 'User' }}
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

        {{-- Admin Reply --}}
        @if($ticket->status !== 'closed')
            <div class="border-t border-gray-100 p-6">
                <form action="{{ route('admin.support.reply', $ticket) }}" method="POST">
                    @csrf
                    <div class="flex items-end space-x-3">
                        <div class="flex-1">
                            <textarea name="message" rows="3" required class="w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 resize-none" placeholder="Type your reply..."></textarea>
                        </div>
                        <button type="submit" class="px-5 py-3 bg-indigo-600 text-white rounded-xl hover:bg-indigo-700 text-sm font-medium shrink-0 flex items-center space-x-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                            <span>Reply</span>
                        </button>
                    </div>
                </form>
            </div>
        @endif

        {{-- Actions --}}
        <div class="border-t border-gray-100 p-4 bg-gray-50 flex items-center justify-end space-x-3">
            @if($ticket->status !== 'closed')
                <form action="{{ route('admin.support.close', $ticket) }}" method="POST" onsubmit="return confirm('Close this ticket?')">
                    @csrf
                    <button type="submit" class="px-4 py-2 border border-gray-300 rounded-xl text-sm font-medium text-gray-700 hover:bg-gray-100">Close Ticket</button>
                </form>
            @else
                <form action="{{ route('admin.support.reopen', $ticket) }}" method="POST">
                    @csrf
                    <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-xl text-sm font-medium hover:bg-green-700">Reopen Ticket</button>
                </form>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const container = document.getElementById('chat-messages');
        if (container) {
            container.scrollTop = container.scrollHeight;
        }
    });
</script>
@endpush
