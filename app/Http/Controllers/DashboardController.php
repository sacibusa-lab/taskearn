<?php

namespace App\Http\Controllers;

use App\Models\FestiveProgram;
use App\Models\Task;
use App\Models\Transaction;
use App\Models\User;
use App\Services\AchievementService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Track daily login streak & bonus
        $addedToday = now()->toDateString();
        $alreadyLoggedToday = $user->last_login_date?->toDateString() === $addedToday;

        $user->trackLoginStreak();

        if (!$alreadyLoggedToday) {
            AchievementService::dailyLoginBonus($user);
        }

        // Check and award achievements
        AchievementService::checkAll($user);
        
        $stats = [
            'balance' => $user->balance,
            'total_earned' => $user->total_earned,
            'referral_earnings' => $user->referral_earnings,
            'total_withdrawn' => $user->total_withdrawn,
            'pending_tasks' => $user->taskSubmissions()->where('status', 'pending')->count(),
            'completed_tasks' => $user->taskSubmissions()->where('status', 'approved')->count(),
            'referrals_count' => $user->referrals()->count(),
            'recent_transactions' => $user->transactions()->latest()->take(5)->get(),
        ];

        $activeTasks = Task::active()->available()
            ->whereDoesntHave('submissions', fn($q) => $q->where('user_id', $user->id))
            ->where(function ($q) use ($user) {
            if ($user->level) {
                $q->whereNull('level_id')->orWhere('level_id', '<=', $user->level->id);
            }
        })->latest()->take(5)->get();

        $activeFestivePrograms = FestiveProgram::active()->get();

        $levelProgress = null;
        if ($user->level) {
            $nextLevel = \App\Models\Level::where('level', $user->level->level + 1)->first();
            if ($nextLevel) {
                $levelProgress = [
                    'current' => $user->level,
                    'next' => $nextLevel,
                ];
            }
        }

        return view('dashboard.index', compact('stats', 'activeTasks', 'activeFestivePrograms', 'levelProgress'));
    }
}
