<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\AchievementService;
use Illuminate\Http\Request;

class LeaderboardController extends Controller
{
    public function index(Request $request)
    {
        $period = $request->query('period', 'all');
        $leaders = AchievementService::leaderboard($period, 50);

        return view('admin.leaderboard.index', compact('leaders', 'period'));
    }

    public function reward(string $period)
    {
        \Artisan::call("leaderboard:reward {$period}");
        $output = \Artisan::output();
        return back()->with('success', trim($output));
    }
}
