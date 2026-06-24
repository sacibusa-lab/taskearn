<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FestiveProgram;
use App\Models\FestiveReward;
use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Http\Request;

class FestiveProgramController extends Controller
{
    public function index()
    {
        $programs = FestiveProgram::latest()->paginate(15);
        return view('admin.festive-programs.index', compact('programs'));
    }

    public function create()
    {
        return view('admin.festive-programs.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after:start_date'],
            'bonus_type' => ['required', 'in:fixed,percentage,task_bonus'],
            'bonus_value' => ['required', 'numeric', 'min:0.01'],
            'status' => ['required', 'in:upcoming,active,completed,cancelled'],
            'banner' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,webp', 'max:2048'],
        ]);

        $data = $request->except('banner');

        if ($request->hasFile('banner')) {
            $data['banner'] = $request->file('banner')->store('festive-programs', 'public');
        }

        $program = FestiveProgram::create($data);

        // Notify all users based on status
        $statusLabels = ['upcoming' => '🎉 Upcoming', 'active' => '🔥 Now Active', 'completed' => '✅ Completed'];
        $label = $statusLabels[$request->status] ?? ucfirst($request->status);
        NotificationService::sendToAll(
            'festive_status',
            "{$label}: {$program->title}",
            "A festive program \"{$program->title}\" is now {$request->status}! " . ($program->description ?? ''),
            route('dashboard')
        );

        return redirect()->route('admin.festive-programs.index')->with('success', 'Festive program created successfully.');
    }

    public function edit(FestiveProgram $festiveProgram)
    {
        return view('admin.festive-programs.edit', compact('festiveProgram'));
    }

    public function update(Request $request, FestiveProgram $festiveProgram)
    {
        $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after:start_date'],
            'bonus_type' => ['required', 'in:fixed,percentage,task_bonus'],
            'bonus_value' => ['required', 'numeric', 'min:0.01'],
            'status' => ['required', 'in:upcoming,active,completed,cancelled'],
            'banner' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,webp', 'max:2048'],
        ]);

        $data = $request->except('banner');

        if ($request->hasFile('banner')) {
            $data['banner'] = $request->file('banner')->store('festive-programs', 'public');
        }

        $oldStatus = $festiveProgram->status;
        $festiveProgram->update($data);

        // Notify if status changed
        if ($oldStatus !== $request->status) {
            $statusLabels = ['upcoming' => '🎉 Upcoming', 'active' => '🔥 Now Active', 'completed' => '✅ Completed'];
            $label = $statusLabels[$request->status] ?? ucfirst($request->status);
            NotificationService::sendToAll(
                'festive_status',
                "{$label}: {$festiveProgram->title}",
                "The festive program \"{$festiveProgram->title}\" status changed to {$request->status}!",
                route('dashboard')
            );
        }

        return redirect()->route('admin.festive-programs.index')->with('success', 'Festive program updated successfully.');
    }

    public function distribute(FestiveProgram $festiveProgram)
    {
        if ($festiveProgram->status !== 'active') {
            return back()->with('error', 'Only active programs can distribute rewards.');
        }

        $activeUsers = User::active()->where('is_probation', false)->get();
        $distributed = 0;

        foreach ($activeUsers as $user) {
            $amount = match ($festiveProgram->bonus_type) {
                'fixed' => $festiveProgram->bonus_value,
                'percentage' => ($user->total_earned * $festiveProgram->bonus_value) / 100,
                'task_bonus' => $festiveProgram->bonus_value,
                default => 0,
            };

            if ($amount <= 0) continue;

            FestiveReward::create([
                'user_id' => $user->id,
                'festive_program_id' => $festiveProgram->id,
                'amount' => $amount,
                'status' => 'credited',
            ]);

            $user->increment('balance', $amount);
            $user->increment('total_earned', $amount);

            $user->transactions()->create([
                'type' => 'festive_bonus',
                'amount' => $amount,
                'balance_before' => $user->balance - $amount,
                'balance_after' => $user->balance,
                'status' => 'completed',
                'description' => "Festive bonus: {$festiveProgram->title}",
            ]);

            $distributed++;
        }

        return redirect()->route('admin.festive-programs.index')
            ->with('success', "Distributed rewards to {$distributed} active users!");
    }
}
