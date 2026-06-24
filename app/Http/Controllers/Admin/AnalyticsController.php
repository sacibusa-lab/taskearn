<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Task;
use App\Models\TaskSubmission;
use App\Models\Withdrawal;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AnalyticsController extends Controller
{
    public function index()
    {
        $stats = $this->getStats();
        return view('admin.analytics.index', $stats);
    }

    private function getStats(): array
    {
        $days = request('range', 30);
        $start = now()->subDays($days);

        // Earnings over time
        $earnings = Transaction::whereIn('type', ['deposit', 'task_reward', 'referral_bonus', 'festive_bonus', 'daily_bonus', 'leaderboard_bonus'])
            ->where('status', 'completed')
            ->where('created_at', '>=', $start)
            ->selectRaw('DATE(created_at) as date, SUM(amount) as total')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // User growth
        $userGrowth = User::where('created_at', '>=', $start)
            ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Task completion rate
        $taskStats = TaskSubmission::where('created_at', '>=', $start)
            ->selectRaw('DATE(created_at) as date, 
                COUNT(*) as total,
                SUM(CASE WHEN status = "approved" THEN 1 ELSE 0 END) as approved,
                SUM(CASE WHEN status = "rejected" THEN 1 ELSE 0 END) as rejected')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Summary cards
        $summary = [
            'total_users' => User::count(),
            'active_users' => User::active()->count(),
            'total_deposits' => Transaction::where('type', 'deposit')->where('status', 'completed')->sum('amount'),
            'total_payouts' => Transaction::where('type', 'payout')->where('status', 'completed')->sum('amount'),
            'total_tasks' => Task::count(),
            'completion_rate' => TaskSubmission::count() > 0
                ? round(TaskSubmission::where('status', 'approved')->count() / TaskSubmission::count() * 100, 1)
                : 0,
            'pending_withdrawals' => Withdrawal::pending()->sum('amount'),
            'total_referrals' => User::whereNotNull('referred_by')->count(),
        ];

        return [
            'summary' => $summary,
            'earnings' => $earnings,
            'userGrowth' => $userGrowth,
            'taskStats' => $taskStats,
            'days' => $days,
            'chartLabels' => $this->getDateRange($days),
            'earningsData' => $this->fillDates($earnings, 'total', $days),
            'userGrowthData' => $this->fillDates($userGrowth, 'count', $days),
            'taskApprovedData' => $this->fillDates($taskStats, 'approved', $days),
            'taskRejectedData' => $this->fillDates($taskStats, 'rejected', $days),
        ];
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

    // CSV Exports
    public function exportUsers()
    {
        $users = User::with('level')->latest()->get();
        return response()->streamDownload(function () use ($users) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Name', 'Username', 'Phone', 'Level', 'Deposit', 'Balance', 'Total Earned', 'Referrals', 'Status', 'Joined']);
            foreach ($users as $u) {
                fputcsv($handle, [$u->name, $u->username, $u->phone, $u->level?->level ?? 0, $u->deposit_amount, $u->balance, $u->total_earned, $u->referrals()->count(), $u->status, $u->created_at->format('Y-m-d')]);
            }
            fclose($handle);
        }, 'users_'.now()->format('Ymd').'.csv');
    }

    public function exportTransactions()
    {
        $txns = Transaction::with('user')->latest()->get();
        return response()->streamDownload(function () use ($txns) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Date', 'User', 'Type', 'Amount', 'Status', 'Description']);
            foreach ($txns as $t) {
                fputcsv($handle, [$t->created_at->format('Y-m-d H:i'), $t->user?->display_name ?? 'N/A', $t->type, $t->amount, $t->status, $t->description]);
            }
            fclose($handle);
        }, 'transactions_'.now()->format('Ymd').'.csv');
    }

    public function exportWithdrawals()
    {
        $withdrawals = Withdrawal::with('user')->latest()->get();
        return response()->streamDownload(function () use ($withdrawals) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Date', 'User', 'Amount', 'Net', 'Method', 'Bank Name', 'Account Name', 'Account Number', 'Status']);
            foreach ($withdrawals as $w) {
                $details = $w->account_details ?? [];
                fputcsv($handle, [$w->created_at->format('Y-m-d H:i'), $w->user?->display_name ?? 'N/A', $w->amount, $w->net_amount, $w->payout_method, $details['bank_name'] ?? '', $details['account_name'] ?? '', $details['account_number'] ?? '', $w->status]);
            }
            fclose($handle);
        }, 'withdrawals_'.now()->format('Ymd').'.csv');
    }
}
