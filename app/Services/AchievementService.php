<?php

namespace App\Services;

use App\Models\Badge;
use App\Models\User;
use App\Services\NotificationService;

class AchievementService
{
    /**
     * Check and award all eligible badges for a user.
     */
    public static function checkAll(User $user): array
    {
        $awarded = [];
        $badges = Badge::all();

        foreach ($badges as $badge) {
            if ($user->badges()->where('badge_id', $badge->id)->exists()) {
                continue; // already has this badge
            }

            if (self::meetsCriteria($user, $badge)) {
                $user->badges()->attach($badge->id, ['awarded_at' => now()]);
                $awarded[] = $badge;

                // Send notification
                NotificationService::send(
                    $user->id,
                    'festive_reward',
                    "🎖 Badge Unlocked: {$badge->name}",
                    "Congratulations! You earned the '{$badge->name}' badge: {$badge->description}",
                    route('leaderboard.index')
                );
            }
        }

        return $awarded;
    }

    /**
     * Check if user meets a badge's criteria.
     */
    private static function meetsCriteria(User $user, Badge $badge): bool
    {
        return match ($badge->criteria_type) {
            'tasks_completed' => $user->taskSubmissions()->where('status', 'approved')->count() >= $badge->criteria_value,
            'referrals_count' => $user->referrals()->count() >= $badge->criteria_value,
            'deposit_amount' => ($user->deposit_amount ?? 0) >= $badge->criteria_value,
            'total_earned' => ($user->total_earned ?? 0) >= $badge->criteria_value,
            'login_streak' => ($user->login_streak ?? 0) >= $badge->criteria_value,
            'early_adopter' => $user->created_at && $user->created_at->lessThan(now()->subDays(7)),
            default => false,
        };
    }

    /**
     * Get leaderboard data.
     */
    public static function leaderboard(string $period = 'all', int $limit = 20): array
    {
        $query = User::active();

        return match ($period) {
            'week' => $query->orderByDesc('total_earned')->take($limit)->get()
                ->map(fn($u, $i) => [
                    'rank' => $i + 1, 'name' => $u->display_name, 'level' => $u->level?->level ?? 0,
                    'amount' => $u->total_earned, 'badges' => $u->badges->pluck('icon')->take(3)->implode(' '),
                ])->toArray(),
            'month' => $query->orderByDesc('total_earned')->take($limit)->get()
                ->map(fn($u, $i) => [
                    'rank' => $i + 1, 'name' => $u->display_name, 'level' => $u->level?->level ?? 0,
                    'amount' => $u->total_earned, 'badges' => $u->badges->pluck('icon')->take(3)->implode(' '),
                ])->toArray(),
            default => $query->orderByDesc('total_earned')->take($limit)->get()
                ->map(fn($u, $i) => [
                    'rank' => $i + 1, 'name' => $u->display_name, 'level' => $u->level?->level ?? 0,
                    'amount' => $u->total_earned, 'badges' => $u->badges->pluck('icon')->take(3)->implode(' '),
                ])->toArray(),
        };
    }

    /**
     * Award daily login bonus.
     */
    public static function dailyLoginBonus(User $user): float
    {
        $baseBonus = 1; // Base bonus per day
        $streakBonus = min(floor($user->login_streak / 7), 10) * 0.5; // Extra for streaks
        $bonus = $baseBonus + $streakBonus;

        $user->increment('balance', $bonus);
        $user->increment('total_earned', $bonus);

        $user->transactions()->create([
            'type' => 'daily_bonus',
            'amount' => $bonus,
            'balance_before' => $user->balance - $bonus,
            'balance_after' => $user->balance,
            'status' => 'completed',
            'description' => "Daily login bonus (Day {$user->login_streak} streak)",
        ]);

        return $bonus;
    }
}
