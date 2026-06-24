@extends('admin.layouts.admin')

@section('title', 'Festive Programs')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="flex justify-between items-center mb-6">
        <div></div>
        <a href="{{ route('admin.festive-programs.create') }}" class="px-4 py-2 bg-indigo-600 text-white rounded-xl hover:bg-indigo-700 text-sm font-medium">+ Create Program</a>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left text-gray-500 border-b border-gray-100 bg-gray-50">
                        <th class="px-6 py-3 font-medium">Banner</th>
                        <th class="px-6 py-3 font-medium">Title</th>
                        <th class="px-6 py-3 font-medium">Period</th>
                        <th class="px-6 py-3 font-medium">Bonus</th>
                        <th class="px-6 py-3 font-medium">Status</th>
                        <th class="px-6 py-3 font-medium">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($programs as $program)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4">
                                @if($program->banner)
                                    <img src="{{ asset('storage/' . $program->banner) }}" alt="{{ $program->title }}" class="w-16 h-10 object-cover rounded-lg">
                                @else
                                    <div class="w-16 h-10 bg-gray-100 rounded-lg flex items-center justify-center text-xs text-gray-400">No</div>
                                @endif
                            </td>
                            <td class="px-6 py-4 font-medium text-gray-900">{{ $program->title }}</td>
                            <td class="px-6 py-4 text-gray-500">{{ $program->start_date->format('M d') }} - {{ $program->end_date->format('M d, Y') }}</td>
                            <td class="px-6 py-4">
                                @if($program->bonus_type === 'percentage')
                                    {{ $program->bonus_value }}%
                                @else
                                    {{ currency($program->bonus_value) }}
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-0.5 rounded-full text-xs font-medium
                                    @if($program->status === 'active') bg-green-100 text-green-700
                                    @elseif($program->status === 'upcoming') bg-blue-100 text-blue-700
                                    @elseif($program->status === 'completed') bg-gray-100 text-gray-700
                                    @else bg-red-100 text-red-700 @endif">
                                    {{ ucfirst($program->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center space-x-2">
                                    <a href="{{ route('admin.festive-programs.edit', $program) }}" class="text-indigo-600 hover:text-indigo-500 text-sm font-medium">Edit</a>
                                    @if($program->status === 'active')
                                        <form action="{{ route('admin.festive-programs.distribute', $program) }}" method="POST" class="inline" onsubmit="return confirm('Distribute rewards to all active users?')">
                                            @csrf
                                            <button type="submit" class="text-green-600 hover:text-green-500 text-sm font-medium">Distribute</button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @if($programs->hasPages())
            <div class="px-6 py-3 border-t border-gray-100">
                {{ $programs->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
