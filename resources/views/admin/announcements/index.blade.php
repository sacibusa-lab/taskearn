@extends('admin.layouts.admin')
@section('title', 'Announcements')
@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="flex justify-between items-center mb-6">
        <div></div>
        <a href="{{ route('admin.announcements.create') }}" class="px-4 py-2 bg-indigo-600 text-white rounded-xl hover:bg-indigo-700 text-sm font-medium">+ New Announcement</a>
    </div>
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead><tr class="text-left text-gray-500 border-b border-gray-100 bg-gray-50">
                    <th class="px-6 py-3 font-medium">Title</th>
                    <th class="px-6 py-3 font-medium">Type</th>
                    <th class="px-6 py-3 font-medium">Dates</th>
                    <th class="px-6 py-3 font-medium">Status</th>
                    <th class="px-6 py-3 font-medium">Actions</th>
                </tr></thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($announcements as $a)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 font-medium text-gray-900">{{ $a->title }}</td>
                            <td class="px-6 py-4">
                                <span class="text-xs px-2 py-0.5 rounded-full font-medium
                                    @if($a->type === 'info') bg-blue-100 text-blue-700
                                    @elseif($a->type === 'warning') bg-amber-100 text-amber-700
                                    @elseif($a->type === 'success') bg-green-100 text-green-700
                                    @elseif($a->type === 'danger') bg-red-100 text-red-700
                                    @else bg-purple-100 text-purple-700 @endif">
                                    {{ ucfirst($a->type) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-xs text-gray-500">
                                {{ $a->starts_at?->format('M d') ?? 'Now' }} — {{ $a->ends_at?->format('M d') ?? 'Forever' }}
                            </td>
                            <td class="px-6 py-4">
                                <form action="{{ route('admin.announcements.toggle', $a) }}" method="POST">@csrf
                                    <button class="text-xs font-bold {{ $a->is_active ? 'text-green-600' : 'text-gray-400' }}">{{ $a->is_active ? 'Active' : 'Inactive' }}</button>
                                </form>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center space-x-3">
                                    <a href="{{ route('admin.announcements.edit', $a) }}" class="text-indigo-600 text-sm">Edit</a>
                                    <form action="{{ route('admin.announcements.destroy', $a) }}" method="POST" onsubmit="return confirm('Delete?')">@csrf @method('DELETE')
                                        <button class="text-red-500 text-sm">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @if($announcements->hasPages())<div class="px-6 py-3 border-t">{{ $announcements->links() }}</div>@endif
    </div>
</div>
@endsection
