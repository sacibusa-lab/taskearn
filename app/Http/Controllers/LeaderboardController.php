<?php

namespace App\Http\Controllers;

use App\Services\AchievementService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LeaderboardController extends Controller
{
    public function index(Request $request)
    {
        $period = $request->query('period', 'all');
        $leaders = AchievementService::leaderboard($period, 20);
        $user = Auth::user();

        // Find user's rank
        $userRank = null;
        foreach ($leaders as $leader) {
            if ($leader['name'] === $user->display_name) {
                $userRank = $leader['rank'];
                break;
            }
        }

        $userBadges = $user->badges()->latest('awarded_at')->get();
        $allBadges = \App\Models\Badge::all()->groupBy('criteria_type');

        return view('leaderboard.index', compact('leaders', 'period', 'userRank', 'userBadges', 'allBadges'));
    }
}
