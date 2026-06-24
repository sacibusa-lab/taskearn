@extends('admin.layouts.admin')

@section('title', 'Support Tickets')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left text-gray-500 border-b border-gray-100 bg-gray-50">
                        <th class="px-6 py-3 font-medium">User</th>
                        <th class="px-6 py-3 font-medium">Subject</th>
                        <th class="px-6 py-3 font-medium">Category</th>
                        <th class="px-6 py-3 font-medium">Priority</th>
                        <th class="px-6 py-3 font-medium">Status</th>
                        <th class="px-6 py-3 font-medium">Messages</th>
                        <th class="px-6 py-3 font-medium">Last Activity</th>
                        <th class="px-6 py-3 font-medium">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($tickets as $ticket)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4">
                                <div class="flex items-center space-x-2">
                                    <div class="w-8 h-8 bg-indigo-100 rounded-full flex items-center justify-center text-xs font-bold text-indigo-600">
                                        {{ substr($ticket->user?->name ?? '?', 0, 1) }}
                                    </div>
                                    <div>
                                        <p class="font-medium text-gray-900 text-xs">{{ $ticket->user?->name ?? 'Unknown' }}</p>
                                        <p class="text-xs text-gray-400">{{ $ticket->user?->phone ?? '' }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 font-medium text-gray-900 max-w-[200px] truncate">{{ $ticket->subject }}</td>
                            <td class="px-6 py-4 text-gray-500 text-xs capitalize">{{ str_replace('_', ' ', $ticket->category) }}</td>
                            <td class="px-6 py-4">
                                <span class="text-xs px-2 py-0.5 rounded-full font-medium
                                    @if($ticket->priority === 'high') bg-red-100 text-red-700
                                    @elseif($ticket->priority === 'medium') bg-amber-100 text-amber-700
                                    @else bg-gray-100 text-gray-600 @endif">
                                    {{ ucfirst($ticket->priority) }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-xs px-2 py-0.5 rounded-full font-medium
                                    @if($ticket->status === 'open') bg-blue-100 text-blue-700
                                    @elseif($ticket->status === 'replied') bg-green-100 text-green-700
                                    @else bg-gray-100 text-gray-600 @endif">
                                    {{ ucfirst($ticket->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-gray-500 text-xs">{{ $ticket->messages ? count($ticket->messages) : 0 }}</td>
                            <td class="px-6 py-4 text-gray-500 text-xs">{{ $ticket->updated_at->diffForHumans() }}</td>
                            <td class="px-6 py-4">
                                <a href="{{ route('admin.support.show', $ticket) }}" class="text-indigo-600 hover:text-indigo-500 text-sm font-medium">View</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @if($tickets->hasPages())
            <div class="px-6 py-3 border-t border-gray-100">
                {{ $tickets->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
