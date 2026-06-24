@extends('layouts.app')

@section('title', $task->title)

@section('header')
<div>
    <a href="{{ route('tasks.index') }}" class="text-sm text-indigo-600 hover:text-indigo-500 font-medium mb-2 inline-block">&larr; Back to Tasks</a>
    <h1 class="text-2xl font-bold text-gray-900">{{ $task->title }}</h1>
</div>
@endsection

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-8">
            <div class="flex items-center justify-between mb-6">
                <div class="flex items-center space-x-4">
                    <div class="w-14 h-14 bg-green-100 rounded-2xl flex items-center justify-center">
                        <span class="text-2xl font-bold text-green-600">$</span>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Reward</p>
                        <p class="text-2xl font-bold text-green-600">+{{ currency($task->reward) }}</p>
                    </div>
                </div>
                <div class="flex items-center space-x-4">
                    <div class="text-right">
                        <p class="text-sm text-gray-500">Estimated Time</p>
                        <p class="text-lg font-semibold text-gray-900">{{ $task->estimated_minutes }} minutes</p>
                    </div>
                    <div class="text-right">
                        <p class="text-sm text-gray-500">Task Type</p>
                        <p class="text-lg font-semibold text-indigo-600">{{ $task->task_type_label }}</p>
                    </div>
                    @if($task->level)
                        <div class="text-right">
                            <p class="text-sm text-gray-500">Required Level</p>
                            <p class="text-lg font-semibold text-indigo-600">Level {{ $task->level->level }}+</p>
                        </div>
                    @endif
                </div>
            </div>

            <div class="prose prose-sm max-w-none text-gray-600 mb-8">
                <h3 class="text-lg font-semibold text-gray-900">Description</h3>
                <p>{{ $task->description }}</p>
            </div>

            @if($task->instructions)
                <div class="bg-gray-50 rounded-xl p-6 mb-8">
                    <h3 class="text-lg font-semibold text-gray-900 mb-3">Instructions</h3>
                    <div class="prose prose-sm max-w-none text-gray-600">
                        {!! nl2br(e($task->instructions)) !!}
                    </div>
                </div>
            @endif

            @if($existingSubmission)
                <div class="bg-blue-50 border border-blue-200 rounded-xl p-6 mb-8">
                    <div class="flex items-center space-x-2 mb-2">
                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <h4 class="font-semibold text-blue-900">Submission Status: <span class="uppercase">{{ $existingSubmission->status }}</span></h4>
                    </div>
                    @if($existingSubmission->status === 'approved')
                        <p class="text-sm text-blue-700">Your submission has been approved! {{ currency($task->reward) }} has been credited to your balance.</p>
                    @elseif($existingSubmission->status === 'rejected')
                        <p class="text-sm text-blue-700">Your submission was rejected.</p>
                        @if($existingSubmission->admin_notes)
                            <p class="text-sm text-blue-600 mt-1">Reason: {{ $existingSubmission->admin_notes }}</p>
                        @endif
                    @else
                        <p class="text-sm text-blue-700">Your submission is pending review by an admin.</p>
                    @endif
                </div>
            @elseif(Auth::user()->canPerformTasks())
                <div class="border-t border-gray-100 pt-8">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Submit Your Work</h3>

                    {{-- Task Type Specific Content --}}
                    @if($task->task_type === 'youtube' && ($task->getMeta('url') || $task->getMeta('video_id')))
                        <div class="mb-6">
                            <div class="rounded-xl overflow-hidden bg-black">
                                @php
                                    $videoId = $task->getMeta('video_id');
                                    $ytUrl = $task->getMeta('url');
                                    if (!$videoId && $ytUrl) {
                                        if (preg_match('/(?:youtube\.com\/watch\?v=|youtu\.be\/|youtube\.com\/embed\/|youtube\.com\/shorts\/)([a-zA-Z0-9_-]{11})/', $ytUrl, $m)) {
                                            $videoId = $m[1];
                                        }
                                    }
                                @endphp
                                @if($videoId)
                                    <div class="relative w-full" style="padding-bottom: 56.25%;">
                                        <div id="youtube-player" class="absolute inset-0 w-full h-full rounded-xl"></div>
                                    </div>
                                @else
                                    <div class="flex items-center justify-center h-full text-white">
                                        <a href="{{ $ytUrl }}" target="_blank" class="text-center">
                                            <svg class="w-16 h-16 mx-auto" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                                            <span class="block mt-2 text-sm">Click to watch on YouTube</span>
                                        </a>
                                    </div>
                                @endif
                            </div>

                            {{-- Countdown Timer --}}
                            <div id="watch-timer" class="mt-4 bg-gradient-to-r from-indigo-600 to-blue-600 text-white rounded-2xl p-4 sm:p-5 flex items-center justify-between flex-wrap gap-3 shadow-lg">
                                <div class="flex items-center space-x-3">
                                    <div class="w-12 h-12 bg-white/20 rounded-full flex items-center justify-center">
                                        <svg id="timer-icon" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    </div>
                                    <div>
                                        <p id="timer-label" class="text-sm font-semibold text-blue-100">Watch time required</p>
                                        <p id="timer-display" class="text-3xl font-extrabold tracking-wider font-mono">{{ floor($task->estimated_minutes) }}:00</p>
                                    </div>
                                </div>
                                <div id="timer-status" class="text-right">
                                    <span id="timer-badge" class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-bold bg-amber-500/80 text-white">
                                        <svg class="w-3.5 h-3.5 me-1 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                                        Watching...
                                    </span>
                                </div>
                            </div>
                        </div>
                    @endif

                    @if($task->task_type === 'video' && $task->getMeta('url'))
                        <div class="mb-6 bg-gray-50 rounded-xl p-4">
                            <a href="{{ $task->getMeta('url') }}" target="_blank" class="flex items-center space-x-3 text-indigo-600 hover:text-indigo-500">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                <span class="font-medium">Watch Video on {{ $task->getMeta('platform', 'the platform') }}</span>
                            </a>
                        </div>
                    @endif

                    @if($task->task_type === 'quiz' && $task->getMeta('question'))
                        <div class="mb-6 bg-indigo-50 rounded-xl p-6">
                            <p class="text-sm font-semibold text-indigo-900 mb-3">Question:</p>
                            <p class="text-sm text-indigo-800 mb-4">{{ $task->getMeta('question') }}</p>
                            @if($options = $task->getMeta('options'))
                                <div class="space-y-2">
                                    @foreach($options as $option)
                                        <label class="flex items-center space-x-3 p-3 bg-white rounded-xl border border-indigo-100 cursor-pointer hover:bg-indigo-50">
                                            <input type="radio" name="submission_data" value="{{ $option }}" class="text-indigo-600 focus:ring-indigo-500" {{ $existingSubmission ? 'disabled' : '' }}>
                                            <span class="text-sm text-gray-700">{{ $option }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    @endif

                    <form action="{{ route('tasks.submit', $task) }}" method="POST">
                        @csrf

                        {{-- Standard text submission (default) --}}
                        <div class="mb-4">
                            <label for="submission_data" class="block text-sm font-medium text-gray-700 mb-2">
                                @switch($task->task_type)
                                    @case('url') Submitted URL @break
                                    @case('youtube') I've Watched This Video — Confirm @break
                                    @case('video') I've Watched This Video — Confirm @break
                                    @case('social_share') Share Link @break
                                    @case('code') Your Code / Solution @break
                                    @case('quiz') Your Answer @break
                                    @default Your Submission
                                @endswitch
                            </label>
                            @if($task->task_type === 'youtube' || $task->task_type === 'video')
                                <input type="hidden" name="submission_data" value="CONFIRMED_WATCHED">
                                <div id="submit-locked" class="p-4 bg-amber-50 border border-amber-200 rounded-xl">
                                    <p class="text-sm text-amber-700 font-medium flex items-center">
                                        <svg class="w-5 h-5 me-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                                        <span id="timer-message">You must watch for {{ $task->estimated_minutes }} minute(s) before you can submit.</span>
                                    </p>
                                </div>
                                <div id="submit-unlocked" class="hidden p-4 bg-green-50 border border-green-200 rounded-xl">
                                    <p class="text-sm text-green-700 font-medium flex items-center">
                                        <svg class="w-5 h-5 me-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        Time requirement met! You may now submit.
                                    </p>
                                </div>
                            @elseif($task->task_type === 'quiz')
                                <input type="text" name="submission_data" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 mt-3" placeholder="Or type your answer here...">
                            @elseif($task->task_type === 'url' || $task->task_type === 'social_share')
                                <input type="url" name="submission_data" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="Paste the URL here...">
                            @elseif($task->task_type === 'image' || $task->task_type === 'file')
                                <input type="file" name="submission_file" accept="{{ $task->getMeta('file_types', '*') }}" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 file:py-2 file:px-4 file:border-0 file:rounded-xl file:bg-indigo-50 file:text-indigo-700 file:font-medium hover:file:bg-indigo-100">
                                <p class="text-xs text-gray-400 mt-1">
                                    @if($task->getMeta('file_types')) Allowed: {{ $task->getMeta('file_types') }} @endif
                                    @if($task->getMeta('max_size')) Max: {{ round($task->getMeta('max_size') / 1024, 1) }}MB @endif
                                </p>
                            @elseif($task->task_type === 'code')
                                <textarea name="submission_data" rows="10" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 font-mono text-sm" placeholder="{{ $task->getMeta('code_language') ? 'Write your ' . $task->getMeta('code_language') . ' code here...' : 'Write your code here...' }}"></textarea>
                            @else
                                <textarea name="submission_data" rows="6" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="Provide your work here..."></textarea>
                            @endif
                        </div>
                        <div class="mb-6">
                            <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">Additional Notes (Optional)</label>
                            <textarea id="notes" name="notes" rows="3" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="Any additional information..."></textarea>
                        </div>
                        <button type="submit" id="submit-btn" class="w-full sm:w-auto px-6 py-3 rounded-xl font-medium transition-colors disabled:cursor-not-allowed
                            {{ $task->task_type === 'youtube' ? 'bg-gray-400 text-white' : 'bg-indigo-600 text-white hover:bg-indigo-700' }}"
                            {{ $task->task_type === 'youtube' ? 'disabled' : '' }}>
                            {{ $task->task_type === 'youtube' ? '⏳ Watch to Unlock' : 'Submit for Review' }}
                        </button>
                    </form>
                </div>
            @else
                <div class="bg-amber-50 border border-amber-200 rounded-xl p-6">
                    <div class="flex items-start space-x-3">
                        <svg class="w-5 h-5 text-amber-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                        </svg>
                        <div>
                            <h4 class="font-medium text-amber-800">Action Required</h4>
                            @if(Auth::user()->isOnProbation())
                                <p class="text-sm text-amber-700 mt-1">Your probation period ends {{ Auth::user()->probation_ends_at->diffForHumans() }}. You'll be able to perform tasks after that.</p>
                            @else
                                <p class="text-sm text-amber-700 mt-1">You need to make a deposit before you can perform tasks.</p>
                                <a href="{{ route('deposits.create') }}" class="inline-block mt-2 px-4 py-2 bg-amber-600 text-white rounded-lg hover:bg-amber-700 text-sm font-medium">Make a Deposit</a>
                            @endif
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@if($task->task_type === 'youtube')
@push('scripts')
<script src="https://www.youtube.com/iframe_api"></script>
<script>
    var player;
    var timerInterval;
    var totalSeconds = {{ $task->estimated_minutes * 60 }};
    var remaining = totalSeconds;
    var isPlaying = false;

    var timerDisplay = document.getElementById('timer-display');
    var timerLabel = document.getElementById('timer-label');
    var timerBadge = document.getElementById('timer-badge');
    var submitBtn = document.getElementById('submit-btn');
    var submitLocked = document.getElementById('submit-locked');
    var submitUnlocked = document.getElementById('submit-unlocked');
    var timerMessage = document.getElementById('timer-message');
    var timerSection = document.getElementById('watch-timer');

    function onYouTubeIframeAPIReady() {
        @php
            $videoId = $task->getMeta('video_id');
            $ytUrl = $task->getMeta('url');
            if (!$videoId && $ytUrl) {
                if (preg_match('/(?:youtube\.com\/watch\?v=|youtu\.be\/|youtube\.com\/embed\/|youtube\.com\/shorts\/)([a-zA-Z0-9_-]{11})/', $ytUrl, $m)) {
                    $videoId = $m[1];
                }
            }
        @endphp
        @if($videoId)
        player = new YT.Player('youtube-player', {
            videoId: '{{ $videoId }}',
            playerVars: {
                'playsinline': 1,
                'rel': 0,
                'modestbranding': 1
            },
            events: {
                'onReady': onPlayerReady,
                'onStateChange': onPlayerStateChange
            }
        });
        @endif
    }

    function onPlayerReady(event) {
        // Player is ready
    }

    function onPlayerStateChange(event) {
        if (event.data == YT.PlayerState.PLAYING) {
            isPlaying = true;
            updateBadgeState(true);
        } else {
            isPlaying = false;
            updateBadgeState(false);
        }
    }

    function updateBadgeState(playing) {
        if (remaining <= 0) return;
        if (playing) {
            if (timerBadge) {
                timerBadge.innerHTML = '<svg class="w-3.5 h-3.5 me-1 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>Watching...';
                timerBadge.className = 'inline-flex items-center px-3 py-1.5 rounded-full text-xs font-bold bg-green-500 text-white';
            }
        } else {
            if (timerBadge) {
                timerBadge.innerHTML = '<svg class="w-3.5 h-3.5 me-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 9v6m4-6v6m7-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>Paused';
                timerBadge.className = 'inline-flex items-center px-3 py-1.5 rounded-full text-xs font-bold bg-amber-500/80 text-white';
            }
        }
    }

    function formatTime(s) {
        var m = Math.floor(s / 60);
        var sec = s % 60;
        return String(m).padStart(2, '0') + ':' + String(sec).padStart(2, '0');
    }

    function updateTimer() {
        if (!isPlaying && remaining > 0) return;

        if (remaining <= 0) {
            clearInterval(timerInterval);
            if (timerDisplay) { timerDisplay.textContent = '00:00'; timerDisplay.classList.add('text-green-200'); }
            if (timerLabel) timerLabel.textContent = 'Watch requirement met!';
            if (timerBadge) {
                timerBadge.innerHTML = '<svg class="w-3.5 h-3.5 me-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>Done';
                timerBadge.className = 'inline-flex items-center px-3 py-1.5 rounded-full text-xs font-bold bg-green-500 text-white';
            }
            if (submitLocked) submitLocked.classList.add('hidden');
            if (submitUnlocked) submitUnlocked.classList.remove('hidden');
            if (submitBtn) {
                submitBtn.disabled = false;
                submitBtn.classList.remove('bg-gray-400');
                submitBtn.classList.add('bg-indigo-600', 'hover:bg-indigo-700');
                submitBtn.textContent = 'Submit for Review';
            }
            return;
        }
        
        if (timerDisplay) timerDisplay.textContent = formatTime(remaining);
        if (timerMessage && remaining % 30 === 0) {
            timerMessage.textContent = 'Keep watching! ' + Math.ceil(remaining / 60) + ' minute(s) remaining.';
        }
        if (remaining <= 30 && timerSection) {
            timerSection.classList.add('from-amber-500', 'to-orange-600');
            timerSection.classList.remove('from-indigo-600', 'to-blue-600');
        }
        remaining--;
    }

    document.addEventListener('DOMContentLoaded', function() {
        // Initialize timer display
        if (timerDisplay) timerDisplay.textContent = formatTime(totalSeconds);
        if (timerLabel) timerLabel.textContent = 'Watch time required';
        updateBadgeState(false); // Initially paused waiting for play
        
        timerInterval = setInterval(updateTimer, 1000);
    });
</script>
@endpush
@endif
