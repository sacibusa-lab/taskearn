@extends('layouts.app')

@section('title', 'Tasks')

@section('header')
<div class="flex items-center justify-between">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Available Tasks</h1>
        <p class="text-sm text-gray-500 mt-1">Complete tasks and earn rewards</p>
    </div>
    @if(!Auth::user()->canPerformTasks())
        <div class="bg-amber-50 border border-amber-200 rounded-lg px-4 py-2">
            <p class="text-sm text-amber-700">@if(Auth::user()->isOnProbation()) Complete probation first @else Make a deposit to start @endif</p>
        </div>
    @endif
</div>
@endsection

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <div class="lg:col-span-2">
            @if($tasks->isEmpty())
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-12 text-center">
                    <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">No tasks available</h3>
                    <p class="text-gray-500">Check back later for new tasks</p>
                </div>
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    @foreach($tasks as $task)
                        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 hover:shadow-md transition-shadow">
                            <div class="flex items-start justify-between mb-3">
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-900">{{ $task->title }}</h3>
                                    <div class="flex items-center space-x-2 mt-1">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-600">{{ $task->task_type_label }}</span>
                                        @if($task->level)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-indigo-100 text-indigo-800">Level {{ $task->level->level }}+</span>
                                        @endif
                                    </div>
                                </div>
                                <span class="text-lg font-bold text-green-600">+{{ currency($task->reward) }}</span>
                            </div>
                            <p class="text-sm text-gray-600 mb-4">{{ Str::limit($task->description, 100) }}</p>
                            <div class="flex items-center justify-between text-sm text-gray-500 mb-4">
                                <span class="flex items-center">
                                    <svg class="w-4 h-4 me-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    ~{{ $task->estimated_minutes }} min
                                </span>
                                @if($task->total_slots > 0)
                                    <span>{{ $task->remaining_slots }}/{{ $task->total_slots }} slots</span>
                                @endif
                            </div>
                            <a href="{{ route('tasks.show', $task) }}" class="block w-full text-center px-4 py-2 bg-indigo-600 text-white rounded-xl hover:bg-indigo-700 text-sm font-medium transition-colors">
                                @if(Auth::user()->taskSubmissions()->where('task_id', $task->id)->exists()) View Submission @else Start Task @endif
                            </a>
                        </div>
                    @endforeach
                </div>
                <div class="mt-6">
                    {{ $tasks->links() }}
                </div>
            @endif
        </div>

        <!-- My Submissions Sidebar -->
        <div>
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">My Submissions</h2>
                @if($mySubmissions->isEmpty())
                    <p class="text-sm text-gray-500 text-center py-4">No submissions yet</p>
                @else
                    <div class="space-y-3">
                        @foreach($mySubmissions as $submission)
                            <div class="border border-gray-100 rounded-xl p-3">
                                <p class="text-sm font-medium text-gray-900">{{ $submission->task?->title }}</p>
                                <div class="flex items-center justify-between mt-2">
                                    <span class="text-xs px-2 py-0.5 rounded-full font-medium
                                        @if($submission->status === 'approved') bg-green-100 text-green-700
                                        @elseif($submission->status === 'rejected') bg-red-100 text-red-700
                                        @else bg-amber-100 text-amber-700 @endif">
                                        {{ ucfirst($submission->status) }}
                                    </span>
                                    <span class="text-xs text-gray-500">{{ $submission->created_at->diffForHumans() }}</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
