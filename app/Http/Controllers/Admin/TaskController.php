<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Level;
use App\Models\Task;
use App\Services\NotificationService;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function index()
    {
        $tasks = Task::with('level')->latest()->paginate(15);
        return view('admin.tasks.index', compact('tasks'));
    }

    public function create()
    {
        $levels = Level::orderBy('level')->get();
        return view('admin.tasks.create', compact('levels'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => [($request->task_type === 'youtube' ? 'nullable' : 'required'), 'string', 'max:255'],
            'description' => [($request->task_type === 'youtube' ? 'nullable' : 'required'), 'string'],
            'instructions' => ['nullable', 'string'],
            'task_type' => ['required', 'string', 'in:text,url,youtube,video,image,file,social_share,quiz,code,custom'],
            'reward' => ['required', 'numeric', 'min:0.01'],
            'estimated_minutes' => ['required', 'integer', 'min:1'],
            'level_id' => ['nullable', 'exists:levels,id'],
            'total_slots' => ['required', 'integer', 'min:0'],
            'status' => ['required', 'in:active,inactive'],
            // Type-specific meta fields
            'meta_url' => ['nullable', 'string', 'max:2000'],
            'meta_platform' => ['nullable', 'string', 'max:100'],
            'meta_question' => ['nullable', 'string', 'max:2000'],
            'meta_answer' => ['nullable', 'string', 'max:2000'],
            'meta_options' => ['nullable', 'string', 'max:5000'],
            'meta_file_types' => ['nullable', 'string', 'max:500'],
            'meta_max_size' => ['nullable', 'integer', 'min:1'],
            'meta_code_language' => ['nullable', 'string', 'max:100'],
            'meta_social_platform' => ['nullable', 'string', 'max:100'],
        ]);

        // Build task_meta from type-specific fields
        $taskMeta = $this->buildTaskMeta($request);

        // For YouTube, generate title from URL if not provided
        $title = $request->title;
        if ($request->task_type === 'youtube' && empty($title)) {
            $title = 'Watch YouTube Video';
        }
        $description = $request->description;
        if ($request->task_type === 'youtube' && empty($description)) {
            $description = 'Watch this video and submit your confirmation to earn a reward.';
        }

        Task::create([
            'title' => $title,
            'description' => $description,
            'instructions' => $request->instructions,
            'task_type' => $request->task_type,
            'task_meta' => $taskMeta,
            'reward' => $request->reward,
            'estimated_minutes' => $request->estimated_minutes,
            'level_id' => $request->level_id,
            'total_slots' => $request->total_slots,
            'remaining_slots' => $request->total_slots,
            'status' => $request->status,
        ]);

        return redirect()->route('admin.tasks.index')->with('success', 'Task created successfully.');
    }

    /**
     * Build task_meta JSON from request fields based on task_type.
     */
    private function buildTaskMeta(Request $request): ?array
    {
        $meta = [];

        switch ($request->task_type) {
            case 'url':
                if ($request->meta_url) $meta['url'] = $request->meta_url;
                break;
            case 'youtube':
                if ($request->meta_url) {
                    $meta['url'] = $request->meta_url;
                    // Extract video ID from various YouTube URL formats
                    $videoId = $this->extractYoutubeId($request->meta_url);
                    if ($videoId) $meta['video_id'] = $videoId;
                }
                break;
            case 'video':
                if ($request->meta_url) $meta['url'] = $request->meta_url;
                if ($request->meta_platform) $meta['platform'] = $request->meta_platform;
                break;
            case 'image':
                if ($request->meta_file_types) $meta['file_types'] = $request->meta_file_types;
                if ($request->meta_max_size) $meta['max_size'] = $request->meta_max_size;
                break;
            case 'file':
                if ($request->meta_file_types) $meta['file_types'] = $request->meta_file_types;
                if ($request->meta_max_size) $meta['max_size'] = $request->meta_max_size;
                break;
            case 'quiz':
                if ($request->meta_question) $meta['question'] = $request->meta_question;
                if ($request->meta_answer) $meta['answer'] = $request->meta_answer;
                if ($request->meta_options) {
                    $meta['options'] = array_map('trim', explode("\n", $request->meta_options));
                }
                break;
            case 'social_share':
                if ($request->meta_url) $meta['url'] = $request->meta_url;
                if ($request->meta_social_platform) $meta['social_platform'] = $request->meta_social_platform;
                break;
            case 'code':
                if ($request->meta_code_language) $meta['code_language'] = $request->meta_code_language;
                if ($request->meta_url) $meta['url'] = $request->meta_url;
                break;
            case 'custom':
                if ($request->meta_url) $meta['url'] = $request->meta_url;
                break;
        }

        return !empty($meta) ? $meta : null;
    }

    /**
     * Extract YouTube video ID from various URL formats.
     */
    private function extractYoutubeId(string $url): ?string
    {
        $patterns = [
            '/(?:youtube\.com\/watch\?v=|youtu\.be\/|youtube\.com\/embed\/|youtube\.com\/v\/|youtube\.com\/shorts\/)([a-zA-Z0-9_-]{11})/',
            '/youtube\.com\/watch\?.*v=([a-zA-Z0-9_-]{11})/',
        ];
        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $url, $matches)) {
                return $matches[1];
            }
        }
        return null;
    }

    public function edit(Task $task)
    {
        $levels = Level::orderBy('level')->get();
        return view('admin.tasks.edit', compact('task', 'levels'));
    }

    public function update(Request $request, Task $task)
    {
        $request->validate([
            'title' => [($request->task_type === 'youtube' ? 'nullable' : 'required'), 'string', 'max:255'],
            'description' => [($request->task_type === 'youtube' ? 'nullable' : 'required'), 'string'],
            'instructions' => ['nullable', 'string'],
            'task_type' => ['required', 'string', 'in:text,url,youtube,video,image,file,social_share,quiz,code,custom'],
            'reward' => ['required', 'numeric', 'min:0.01'],
            'estimated_minutes' => ['required', 'integer', 'min:1'],
            'level_id' => ['nullable', 'exists:levels,id'],
            'total_slots' => ['required', 'integer', 'min:0'],
            'remaining_slots' => ['required', 'integer', 'min:0'],
            'status' => ['required', 'in:active,inactive'],
            // Type-specific meta fields
            'meta_url' => ['nullable', 'string', 'max:2000'],
            'meta_platform' => ['nullable', 'string', 'max:100'],
            'meta_question' => ['nullable', 'string', 'max:2000'],
            'meta_answer' => ['nullable', 'string', 'max:2000'],
            'meta_options' => ['nullable', 'string', 'max:5000'],
            'meta_file_types' => ['nullable', 'string', 'max:500'],
            'meta_max_size' => ['nullable', 'integer', 'min:1'],
            'meta_code_language' => ['nullable', 'string', 'max:100'],
            'meta_social_platform' => ['nullable', 'string', 'max:100'],
        ]);

        $taskMeta = $this->buildTaskMeta($request);

        $title = $request->title;
        if ($request->task_type === 'youtube' && empty($title)) {
            $title = 'Watch YouTube Video';
        }
        $description = $request->description;
        if ($request->task_type === 'youtube' && empty($description)) {
            $description = 'Watch this video and submit your confirmation to earn a reward.';
        }

        $task->update(array_merge($request->except('meta_url', 'meta_platform', 'meta_question', 'meta_answer', 'meta_options', 'meta_file_types', 'meta_max_size', 'meta_code_language', 'meta_social_platform'), [
            'title' => $title,
            'description' => $description,
            'task_meta' => $taskMeta,
            'task_type' => $request->task_type,
        ]));

        return redirect()->route('admin.tasks.index')->with('success', 'Task updated successfully.');
    }

    public function submissions()
    {
        $submissions = \App\Models\TaskSubmission::with(['user', 'task'])
            ->latest()
            ->paginate(20);

        return view('admin.tasks.submissions', compact('submissions'));
    }

    public function destroy(Task $task)
    {
        $task->delete();
        return back()->with('success', 'Task deleted successfully.');
    }

    public function approveSubmission(\App\Models\TaskSubmission $submission)
    {
        if ($submission->status !== 'pending') {
            return back()->with('error', 'Submission has already been reviewed.');
        }

        $submission->update([
            'status' => 'approved',
            'reviewed_at' => now(),
            'reviewed_by' => auth()->id(),
        ]);

        $user = $submission->user;
        $task = $submission->task;

        $user->increment('balance', $task->reward);
        $user->increment('total_earned', $task->reward);

        $user->transactions()->create([
            'type' => 'task_reward',
            'amount' => $task->reward,
            'balance_before' => $user->balance - $task->reward,
            'balance_after' => $user->balance,
            'status' => 'completed',
            'description' => "Reward for task: {$task->title}",
        ]);

        if ($task->total_slots > 0) {
            $task->decrement('remaining_slots');
        }

        NotificationService::send(
            $user->id,
            'task_approved',
            'Task Approved!',
            'Your task "' . $task->title . '" has been approved. ' . currency($task->reward) . ' added to your balance.',
            route('tasks.index')
        );

        return back()->with('success', 'Submission approved. ' . \App\Models\AdminSetting::currency($task->reward) . ' credited to user.');
    }

    public function rejectSubmission(Request $request, \App\Models\TaskSubmission $submission)
    {
        if ($submission->status !== 'pending') {
            return back()->with('error', 'Submission has already been reviewed.');
        }

        $request->validate([
            'admin_notes' => ['required', 'string', 'max:1000'],
        ]);

        $submission->update([
            'status' => 'rejected',
            'admin_notes' => $request->admin_notes,
            'reviewed_at' => now(),
            'reviewed_by' => auth()->id(),
        ]);

        NotificationService::send(
            $submission->user_id,
            'task_rejected',
            'Task Submission Rejected',
            'Your task "' . ($submission->task?->title ?? '') . '" was rejected. Reason: ' . $request->admin_notes,
            route('tasks.index')
        );

        return back()->with('success', 'Submission rejected.');
    }
}
