<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminSetting;
use App\Models\User;
use App\Models\Transaction;
use App\Models\Task;
use App\Models\TaskSubmission;
use App\Models\Level;
use App\Models\Withdrawal;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $days = (int) $request->query('range', 7);

        $stats = [
            'total_users' => User::count(),
            'active_users' => User::active()->count(),
            'total_deposits' => Transaction::where('type', 'deposit')->where('status', 'completed')->sum('amount'),
            'total_payouts' => Transaction::where('type', 'payout')->where('status', 'completed')->sum('amount'),
            'pending_submissions' => TaskSubmission::pending()->count(),
            'total_tasks' => Task::count(),
            'levels' => Level::orderBy('level')->get(),
            'recent_users' => User::latest()->take(5)->get(),
            'recent_transactions' => Transaction::latest()->take(5)->get(),
        ];

        // Analytics data
        $start = now()->subDays($days);

        $earnings = Transaction::whereIn('type', ['deposit', 'task_reward', 'referral_bonus', 'festive_bonus', 'daily_bonus', 'leaderboard_bonus'])
            ->where('status', 'completed')
            ->where('created_at', '>=', $start)
            ->selectRaw('DATE(created_at) as date, SUM(amount) as total')
            ->groupBy('date')->orderBy('date')->get();

        $userGrowth = User::where('created_at', '>=', $start)
            ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->groupBy('date')->orderBy('date')->get();

        $taskStats = TaskSubmission::where('created_at', '>=', $start)
            ->selectRaw("DATE(created_at) as date, COUNT(*) as total, SUM(CASE WHEN status = 'approved' THEN 1 ELSE 0 END) as approved, SUM(CASE WHEN status = 'rejected' THEN 1 ELSE 0 END) as rejected")
            ->groupBy('date')->orderBy('date')->get();

        $summary = [
            'completion_rate' => TaskSubmission::count() > 0
                ? round(TaskSubmission::where('status', 'approved')->count() / TaskSubmission::count() * 100, 1) : 0,
            'pending_withdrawals' => Withdrawal::pending()->sum('amount'),
            'total_referrals' => User::whereNotNull('referred_by')->count(),
        ];

        $chartData = [
            'days' => $days,
            'chartLabels' => $this->getDateRange($days),
            'earningsData' => $this->fillDates($earnings, 'total', $days),
            'userGrowthData' => $this->fillDates($userGrowth, 'count', $days),
            'taskApprovedData' => $this->fillDates($taskStats, 'approved', $days),
            'taskRejectedData' => $this->fillDates($taskStats, 'rejected', $days),
        ];

        return view('admin.dashboard', array_merge(compact('stats', 'summary'), $chartData));
    }

    private function getDateRange(int $days): array
    {
        $labels = [];
        for ($i = $days - 1; $i >= 0; $i--) {
            $labels[] = now()->subDays($i)->format('M d');
        }
        return $labels;
    }

    private function fillDates($collection, string $field, int $days): array
    {
        $data = [];
        $map = $collection->keyBy(fn($item) => Carbon::parse($item->date)->format('Y-m-d'));
        for ($i = $days - 1; $i >= 0; $i--) {
            $key = now()->subDays($i)->format('Y-m-d');
            $data[] = $map->has($key) ? (float) $map[$key]->$field : 0;
        }
        return $data;
    }
}
