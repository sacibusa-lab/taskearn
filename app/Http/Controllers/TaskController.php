<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\TaskSubmission;
use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TaskController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $levelFilter = function ($q) use ($user) {
            if ($user->level) {
                $q->whereNull('level_id')->orWhere('level_id', '<=', $user->level->id);
            } else {
                $q->whereNull('level_id');
            }
        };

        $availableBase = Task::active()->available()
            ->whereDoesntHave('submissions', fn($q) => $q->where('user_id', $user->id));

        // Categorized tasks
        $featuredTasks = (clone $availableBase)->featured()->where($levelFilter)->latest()->take(4)->get();
        $dailyTasks = (clone $availableBase)->daily()->where($levelFilter)->latest()->take(6)->get();
        $premiumTasks = (clone $availableBase)->premium()->where($levelFilter)->latest()->take(6)->get();
        $generalTasks = (clone $availableBase)->general()->where($levelFilter)->latest()->paginate(12);

        // Progress tracking
        $completedCount = $user->taskSubmissions()->where('status', 'approved')->count();
        $pendingCount = $user->taskSubmissions()->where('status', 'pending')->count();
        $rejectedCount = $user->taskSubmissions()->where('status', 'rejected')->count();
        $totalSubmitted = $completedCount + $pendingCount + $rejectedCount;

        $mySubmissions = $user->taskSubmissions()->with('task')->latest()->take(10)->get();

        $progress = [
            'total_submitted' => $totalSubmitted,
            'completed' => $completedCount,
            'pending' => $pendingCount,
            'rejected' => $rejectedCount,
            'completion_rate' => $totalSubmitted > 0 ? round(($completedCount / $totalSubmitted) * 100) : 0,
        ];

        return view('tasks.index', compact(
            'featuredTasks', 'dailyTasks', 'premiumTasks', 'generalTasks',
            'mySubmissions', 'progress'
        ));
    }

    public function show(Task $task)
    {
        if ($task->status !== 'active') {
            abort(404);
        }

        $user = Auth::user();
        $existingSubmission = $user->taskSubmissions()->where('task_id', $task->id)->first();

        return view('tasks.show', compact('task', 'existingSubmission'));
    }

    public function submit(Request $request, Task $task)
    {
        $user = Auth::user();

        if (!$user->canPerformTasks()) {
            return back()->with('error', 'You must complete your probation period and make a deposit before performing tasks.');
        }

        if ($task->status !== 'active') {
            return back()->with('error', 'This task is no longer available.');
        }

        if ($user->taskSubmissions()->where('task_id', $task->id)->exists()) {
            return back()->with('error', 'You have already submitted this task.');
        }

        $rules = [
            'notes' => ['nullable', 'string', 'max:1000'],
            'submission_data' => ['nullable', 'string', 'max:50000'],
        ];

        // Handle file uploads for image/file type tasks
        if (in_array($task->task_type, ['image', 'file'])) {
            $fileTypes = $task->getMeta('file_types', 'jpg,jpeg,png,pdf,doc,docx');
            $maxSize = $task->getMeta('max_size', 2048); // KB
            $rules['submission_file'] = ['required', 'file', 'mimes:' . str_replace(',', ',', $fileTypes), 'max:' . $maxSize];
        }

        $request->validate($rules);

        $submissionData = $request->submission_data;

        // Handle file upload
        $filePath = null;
        if ($request->hasFile('submission_file')) {
            $filePath = $request->file('submission_file')->store('task-submissions/' . $user->id, 'public');
            $submissionData = $submissionData ?? 'File uploaded: ' . $request->file('submission_file')->getClientOriginalName();
        }

        $submission = $user->taskSubmissions()->create([
            'task_id' => $task->id,
            'notes' => $request->notes,
            'submission_data' => $submissionData,
            'file_path' => $filePath,
            'status' => 'pending',
        ]);

        // Auto-approve if setting is enabled
        if (\App\Models\AdminSetting::getValue('auto_approve_tasks', false)) {
            $submission->update([
                'status' => 'approved',
                'reviewed_at' => now(),
                'reviewed_by' => User::where('is_admin', true)->first()?->id,
                'admin_notes' => 'Auto-approved',
            ]);

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
                'Your task "' . $task->title . '" was approved. ' . currency($task->reward) . ' added to your balance.',
                route('tasks.index')
            );

            return redirect()->route('tasks.index')->with('success', 'Task submitted and approved! ' . \App\Models\AdminSetting::currency($task->reward) . ' has been added to your balance.');
        }

        return redirect()->route('tasks.index')->with('success', 'Task submitted successfully! Pending admin review.');
    }
}
