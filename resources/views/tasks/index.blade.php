@extends('layouts.app')

@section('title', 'Tasks')

@section('header')
<div class="flex items-center justify-between">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Tasks</h1>
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
        <div class="lg:col-span-2 space-y-8">

            {{-- Featured Tasks --}}
            @if($featuredTasks->isNotEmpty())
                <div>
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-lg font-bold text-gray-900">⭐ Featured Tasks</h2>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        @foreach($featuredTasks as $task)
                            <div class="bg-gradient-to-br from-amber-50 to-yellow-50 border-2 border-amber-200 rounded-2xl p-5 hover:shadow-lg transition-all relative overflow-hidden">
                                <div class="absolute top-2 right-2">
                                    <span class="px-2 py-0.5 bg-amber-500 text-white text-[10px] font-bold rounded-full">Featured</span>
                                </div>
                                <div class="flex items-start justify-between mb-2">
                                    <h3 class="font-bold text-gray-900">{{ $task->title }}</h3>
                                    <span class="text-lg font-bold text-green-600 shrink-0 ml-2">+{{ currency($task->reward) }}</span>
                                </div>
                                <p class="text-xs text-gray-500 mb-3">{{ Str::limit($task->description, 80) }}</p>
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center space-x-2 text-[11px] text-gray-400">
                                        <span>{{ $task->task_type_label }}</span>
                                        @if($task->level)
                                            <span class="text-indigo-500">Lv{{ $task->level->level }}+</span>
                                        @endif
                                        <span>~{{ $task->estimated_minutes }}m</span>
                                    </div>
                                    <a href="{{ route('tasks.show', $task) }}" class="px-3 py-1.5 bg-amber-600 text-white text-xs font-bold rounded-xl hover:bg-amber-700 transition-colors">Start</a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Tab Navigation --}}
            @php
                $activeTab = request('tab', 'daily');
            @endphp
            <div x-data="{ tab: '{{ $activeTab }}' }">
                <div class="border-b border-gray-200 mb-6">
                    <nav class="flex space-x-6 -mb-px" role="tablist">
                        <button @click="tab = 'daily'" :class="tab === 'daily' ? 'border-indigo-600 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'" class="pb-3 text-sm font-medium border-b-2 transition-colors flex items-center space-x-1.5">
                            <span>🔥</span>
                            <span>Daily</span>
                            @if($dailyTasks->isNotEmpty())<span class="text-xs bg-indigo-100 text-indigo-600 px-1.5 py-0.5 rounded-full">{{ $dailyTasks->count() }}</span>@endif
                        </button>
                        <button @click="tab = 'premium'" :class="tab === 'premium' ? 'border-indigo-600 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'" class="pb-3 text-sm font-medium border-b-2 transition-colors flex items-center space-x-1.5">
                            <span>💎</span>
                            <span>Premium</span>
                            @if($premiumTasks->isNotEmpty())<span class="text-xs bg-indigo-100 text-indigo-600 px-1.5 py-0.5 rounded-full">{{ $premiumTasks->count() }}</span>@endif
                        </button>
                        <button @click="tab = 'all'" :class="tab === 'all' ? 'border-indigo-600 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'" class="pb-3 text-sm font-medium border-b-2 transition-colors flex items-center space-x-1.5">
                            <span>📋</span>
                            <span>All Tasks</span>
                        </button>
                    </nav>
                </div>

                {{-- Daily Tasks Tab --}}
                <div x-show="tab === 'daily'">
                    @if($dailyTasks->isEmpty())
                        <div class="bg-white rounded-2xl border border-gray-100 p-10 text-center">
                            <span class="text-4xl">🔥</span>
                            <h3 class="text-lg font-medium text-gray-900 mt-3 mb-1">No Daily Tasks Available</h3>
                            <p class="text-sm text-gray-500">Check back tomorrow for new daily tasks!</p>
                        </div>
                    @else
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            @foreach($dailyTasks as $task)
                                <div class="bg-white rounded-2xl border border-gray-100 p-5 hover:shadow-md transition-all">
                                    <div class="flex items-start justify-between mb-2">
                                        <div class="flex items-center space-x-2">
                                            <span class="text-sm">🔥</span>
                                            <h3 class="font-semibold text-gray-900 text-sm">{{ $task->title }}</h3>
                                        </div>
                                        <span class="text-base font-bold text-green-600 shrink-0">+{{ currency($task->reward) }}</span>
                                    </div>
                                    <p class="text-xs text-gray-500 mb-3 ml-7">{{ Str::limit($task->description, 80) }}</p>
                                    <div class="flex items-center justify-between ml-7">
                                        <div class="flex items-center space-x-2 text-[11px] text-gray-400">
                                            <span>~{{ $task->estimated_minutes }}m</span>
                                            @if($task->level)
                                                <span class="text-indigo-500">Lv{{ $task->level->level }}+</span>
                                            @endif
                                        </div>
                                        <a href="{{ route('tasks.show', $task) }}" class="px-3 py-1.5 bg-indigo-600 text-white text-xs font-bold rounded-xl hover:bg-indigo-700 transition-colors">Start</a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                {{-- Premium Tasks Tab --}}
                <div x-show="tab === 'premium'">
                    @if($premiumTasks->isEmpty())
                        <div class="bg-white rounded-2xl border border-gray-100 p-10 text-center">
                            <span class="text-4xl">💎</span>
                            <h3 class="text-lg font-medium text-gray-900 mt-3 mb-1">No Premium Tasks Available</h3>
                            <p class="text-sm text-gray-500">Upgrade your level to unlock premium tasks.</p>
                        </div>
                    @else
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            @foreach($premiumTasks as $task)
                                <div class="bg-gradient-to-br from-indigo-50 to-purple-50 border border-indigo-200 rounded-2xl p-5 hover:shadow-md transition-all">
                                    <div class="flex items-start justify-between mb-2">
                                        <div>
                                            <div class="flex items-center space-x-2">
                                                <span class="text-sm">💎</span>
                                                <h3 class="font-semibold text-gray-900 text-sm">{{ $task->title }}</h3>
                                            </div>
                                            @if($task->level)
                                                <span class="inline-block mt-1 text-[10px] font-bold text-indigo-600 bg-indigo-100 px-2 py-0.5 rounded-full">Level {{ $task->level->level }}+</span>
                                            @endif
                                        </div>
                                        <span class="text-base font-bold text-green-600 shrink-0">+{{ currency($task->reward) }}</span>
                                    </div>
                                    <p class="text-xs text-gray-500 mb-3 ml-7">{{ Str::limit($task->description, 80) }}</p>
                                    <div class="flex items-center justify-between ml-7">
                                        <div class="flex items-center space-x-2 text-[11px] text-gray-400">
                                            <span>~{{ $task->estimated_minutes }}m</span>
                                            @if($task->total_slots > 0)
                                                <span>{{ $task->remaining_slots }} slots left</span>
                                            @endif
                                        </div>
                                        <a href="{{ route('tasks.show', $task) }}" class="px-3 py-1.5 bg-indigo-600 text-white text-xs font-bold rounded-xl hover:bg-indigo-700 transition-colors">Start</a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                {{-- All Tasks Tab --}}
                <div x-show="tab === 'all'">
                    @if($generalTasks->isEmpty())
                        <div class="bg-white rounded-2xl border border-gray-100 p-10 text-center">
                            <svg class="w-12 h-12 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                            <h3 class="text-lg font-medium text-gray-900 mb-1">No Tasks Available</h3>
                            <p class="text-sm text-gray-500">Check back later for new tasks</p>
                        </div>
                    @else
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            @foreach($generalTasks as $task)
                                <div class="bg-white rounded-2xl border border-gray-100 p-5 hover:shadow-md transition-all">
                                    <div class="flex items-start justify-between mb-2">
                                        <div>
                                            <h3 class="font-semibold text-gray-900 text-sm">{{ $task->title }}</h3>
                                            <div class="flex items-center space-x-2 mt-1">
                                                <span class="text-[10px] font-medium text-gray-500 bg-gray-100 px-2 py-0.5 rounded-full">{{ $task->task_type_label }}</span>
                                                @if($task->level)
                                                    <span class="text-[10px] font-medium text-indigo-600 bg-indigo-100 px-2 py-0.5 rounded-full">Lv{{ $task->level->level }}+</span>
                                                @endif
                                            </div>
                                        </div>
                                        <span class="text-base font-bold text-green-600 shrink-0">+{{ currency($task->reward) }}</span>
                                    </div>
                                    <p class="text-xs text-gray-500 mb-3">{{ Str::limit($task->description, 80) }}</p>
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center space-x-2 text-[11px] text-gray-400">
                                            <span>~{{ $task->estimated_minutes }}m</span>
                                            @if($task->total_slots > 0)
                                                <span>{{ $task->remaining_slots }}/{{ $task->total_slots }}</span>
                                            @endif
                                        </div>
                                        <a href="{{ route('tasks.show', $task) }}" class="px-3 py-1.5 bg-indigo-600 text-white text-xs font-bold rounded-xl hover:bg-indigo-700 transition-colors">
                                            @if($task->isCompletedBy(Auth::user())) View @else Start @endif
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <div class="mt-6">{{ $generalTasks->links() }}</div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Right Sidebar: Progress + Submissions --}}
        <div class="space-y-6">
            {{-- Task Progress Card --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <h2 class="text-sm font-bold text-gray-900 mb-4">📊 My Progress</h2>
                @if($progress['total_submitted'] > 0)
                    <div class="text-center mb-4">
                        <p class="text-3xl font-extrabold text-indigo-600">{{ $progress['completion_rate'] }}%</p>
                        <p class="text-xs text-gray-500 mt-1">Completion Rate</p>
                    </div>
                    <div class="w-full bg-gray-100 rounded-full h-2 mb-4">
                        <div class="bg-indigo-600 h-full rounded-full transition-all" style="width: {{ $progress['completion_rate'] }}%"></div>
                    </div>
                    <div class="grid grid-cols-3 gap-2 text-center text-xs">
                        <div class="bg-green-50 rounded-xl p-2">
                            <p class="text-lg font-bold text-green-600">{{ $progress['completed'] }}</p>
                            <p class="text-gray-500">Done</p>
                        </div>
                        <div class="bg-amber-50 rounded-xl p-2">
                            <p class="text-lg font-bold text-amber-600">{{ $progress['pending'] }}</p>
                            <p class="text-gray-500">Pending</p>
                        </div>
                        <div class="bg-red-50 rounded-xl p-2">
                            <p class="text-lg font-bold text-red-500">{{ $progress['rejected'] }}</p>
                            <p class="text-gray-500">Rejected</p>
                        </div>
                    </div>
                @else
                    <div class="text-center py-4">
                        <span class="text-3xl">🚀</span>
                        <p class="text-sm text-gray-500 mt-2">Start completing tasks to see your progress!</p>
                    </div>
                @endif
            </div>

            {{-- My Submissions --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <h2 class="text-sm font-bold text-gray-900 mb-4">📝 Recent Submissions</h2>
                @if($mySubmissions->isEmpty())
                    <p class="text-sm text-gray-500 text-center py-4">No submissions yet</p>
                @else
                    <div class="space-y-3">
                        @foreach($mySubmissions as $submission)
                            <div class="border border-gray-100 rounded-xl p-3">
                                <p class="text-sm font-medium text-gray-900 truncate">{{ $submission->task?->title }}</p>
                                <div class="flex items-center justify-between mt-2">
                                    <span class="text-[10px] px-2 py-0.5 rounded-full font-medium
                                        @if($submission->status === 'approved') bg-green-100 text-green-700
                                        @elseif($submission->status === 'rejected') bg-red-100 text-red-700
                                        @else bg-amber-100 text-amber-700 @endif">
                                        {{ ucfirst($submission->status) }}
                                    </span>
                                    <span class="text-[10px] text-gray-400">{{ $submission->created_at->diffForHumans() }}</span>
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
