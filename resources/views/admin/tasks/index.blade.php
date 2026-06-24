@extends('admin.layouts.admin')

@section('title', 'Tasks')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="flex justify-between items-center mb-6">
        <div></div>
        <a href="{{ route('admin.tasks.create') }}" class="px-4 py-2 bg-indigo-600 text-white rounded-xl hover:bg-indigo-700 text-sm font-medium">+ Create Task</a>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left text-gray-500 border-b border-gray-100 bg-gray-50">
                        <th class="px-6 py-3 font-medium">Title</th>
                        <th class="px-6 py-3 font-medium">Type</th>
                        <th class="px-6 py-3 font-medium">Level</th>
                        <th class="px-6 py-3 font-medium">Reward</th>
                        <th class="px-6 py-3 font-medium">Slots</th>
                        <th class="px-6 py-3 font-medium">Status</th>
                        <th class="px-6 py-3 font-medium">Submissions</th>
                        <th class="px-6 py-3 font-medium">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($tasks as $task)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 font-medium text-gray-900">{{ $task->title }}</td>
                            <td class="px-6 py-4">
                                <span class="text-xs px-2 py-0.5 rounded-full bg-gray-100 text-gray-700 font-medium">{{ $task->task_type_label }}</span>
                            </td>
                            <td class="px-6 py-4">@if($task->level) Level {{ $task->level->level }} @else All @endif</td>
                            <td class="px-6 py-4 text-green-600 font-medium">{{ currency($task->reward) }}</td>
                            <td class="px-6 py-4">{{ $task->total_slots > 0 ? $task->remaining_slots.'/'.$task->total_slots : 'Unlimited' }}</td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-0.5 rounded-full text-xs font-medium
                                    @if($task->status === 'active') bg-green-100 text-green-700
                                    @else bg-gray-100 text-gray-700 @endif">
                                    {{ ucfirst($task->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4">{{ $task->submissions->count() }}</td>
                            <td class="px-6 py-4">
                                <div class="flex items-center space-x-3">
                                    <a href="{{ route('admin.tasks.edit', $task) }}" class="text-indigo-600 hover:text-indigo-500 text-sm font-medium">Edit</a>
                                    <form action="{{ route('admin.tasks.destroy', $task) }}" method="POST" onsubmit="return confirm('Delete this task? This cannot be undone.')">
                                        @csrf @method('DELETE')
                                        <button class="text-red-500 hover:text-red-700 text-sm font-medium">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @if($tasks->hasPages())
            <div class="px-6 py-3 border-t border-gray-100">
                {{ $tasks->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
