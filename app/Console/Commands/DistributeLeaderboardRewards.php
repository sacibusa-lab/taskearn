<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\Transaction;
use App\Services\NotificationService;
use Illuminate\Console\Command;

class DistributeLeaderboardRewards extends Command
{
    protected $signature = 'leaderboard:reward {period=week : week or month}';
    protected $description = 'Award 10% bonus to the top earner of the period';

    public function handle(): void
    {
        $period = $this->argument('period');
        $start = match ($period) {
            'month' => now()->subMonth()->startOfMonth(),
            default => now()->subWeek()->startOfWeek(),
        };
        $end = match ($period) {
            'month' => now()->subMonth()->endOfMonth(),
            default => now()->subWeek()->endOfWeek(),
        };

        // Sum earnings (task_reward + daily_bonus) for the period
        $topEarner = User::active()
            ->whereHas('transactions', fn($q) => $q
                ->whereIn('type', ['task_reward', 'daily_bonus', 'referral_bonus', 'festive_bonus'])
                ->where('status', 'completed')
                ->whereBetween('created_at', [$start, $end])
            )
            ->withSum(['transactions' => fn($q) => $q
                ->whereIn('type', ['task_reward', 'daily_bonus', 'referral_bonus', 'festive_bonus'])
                ->where('status', 'completed')
                ->whereBetween('created_at', [$start, $end])
            ], 'amount')
            ->orderByDesc('transactions_sum_amount')
            ->first();

        if (!$topEarner || ($topEarner->transactions_sum_amount ?? 0) <= 0) {
            $this->info("No qualifying earnings found for the {$period}.");
            return;
        }

        $periodEarnings = $topEarner->transactions_sum_amount;
        $bonus = round($periodEarnings * 0.1, 2);

        $topEarner->increment('balance', $bonus);
        $topEarner->increment('total_earned', $bonus);

        $topEarner->transactions()->create([
            'type' => 'leaderboard_bonus',
            'amount' => $bonus,
            'balance_before' => $topEarner->balance - $bonus,
            'balance_after' => $topEarner->balance,
            'status' => 'completed',
            'description' => "🏆 Top earner {$period}ly bonus — 10% of " . currency($periodEarnings),
        ]);

        NotificationService::send(
            $topEarner->id,
            'festive_reward',
            "🏆 You're the {$period}ly Top Earner!",
            "Congratulations! You earned a 10% bonus of " . currency($bonus) . " for being the highest earner this {$period}.",
            route('leaderboard.index')
        );

        $this->info("✅ {$period}ly reward: {$topEarner->display_name} earned " . currency($periodEarnings) . " → 10% bonus of " . currency($bonus) . " awarded.");
    }
}
