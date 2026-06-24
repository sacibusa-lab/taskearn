<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Level;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $users = User::with('level')
            ->when($request->search, function ($q, $search) {
                $q->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('phone', 'like', "%{$search}%");
                });
            })
            ->when($request->status, function ($q, $status) {
                $q->where('status', $status);
            })
            ->when($request->level, function ($q, $level) {
                $q->whereHas('level', function ($q) use ($level) {
                    $q->where('level', $level);
                });
            })
            ->latest()
            ->paginate(20);

        $levels = Level::orderBy('level')->get();

        return view('admin.users.index', compact('users', 'levels'));
    }

    public function edit(User $user)
    {
        $levels = Level::orderBy('level')->get();
        return view('admin.users.edit', compact('user', 'levels'));
    }

    public function show(User $user)
    {
        $user->load(['level', 'referrer', 'badges']);
        $stats = [
            'total_earned' => $user->total_earned,
            'total_withdrawn' => $user->total_withdrawn,
            'referral_count' => $user->referrals()->count(),
            'commission_earned' => $user->referral_earnings,
            'tasks_completed' => $user->taskSubmissions()->where('status', 'approved')->count(),
            'tasks_pending' => $user->taskSubmissions()->where('status', 'pending')->count(),
            'login_streak' => $user->login_streak,
        ];
        $recentTransactions = $user->transactions()->latest()->take(15)->get();
        $referrals = $user->referrals()->with('level')->latest()->take(10)->get();

        return view('admin.users.show', compact('user', 'stats', 'recentTransactions', 'referrals'));
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'regex:/^[0-9]+$/', 'min:10', 'max:20', 'unique:users,phone,' . $user->id],
            'level_id' => ['nullable', 'exists:levels,id'],
            'status' => ['required', 'in:active,suspended,banned'],
            'balance' => ['required', 'numeric', 'min:0'],
            'is_admin' => ['boolean'],
        ]);

        $user->update([
            'name' => $request->name,
            'phone' => $request->phone,
            'email' => $request->phone . '@taskearn.com',
            'level_id' => $request->level_id,
            'status' => $request->status,
            'balance' => $request->balance,
            'is_admin' => $request->boolean('is_admin'),
        ]);

        return redirect()->route('admin.users.index')->with('success', 'User updated successfully.');
    }

    public function toggleStatus(User $user)
    {
        $user->update([
            'status' => $user->status === 'active' ? 'suspended' : 'active',
        ]);

        return back()->with('success', 'User status updated.');
    }

    public function unlockDeposit(User $user)
    {
        $user->update(['deposit_locked_until' => null]);

        return back()->with('success', "{$user->name}'s deposit has been unlocked.");
    }
}
