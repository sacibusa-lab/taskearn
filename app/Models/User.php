<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;

#[Fillable([
    'name', 'username', 'phone', 'email', 'password', 'level_id', 'deposit_amount',
    'is_probation', 'probation_ends_at', 'referred_by',
    'balance', 'referral_earnings', 'total_earned', 'total_withdrawn',
    'bank_name', 'bank_account_number', 'bank_account_name', 'bank_code',
    'is_admin', 'status', 'referral_code', 'deposited_at',
])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Use phone number as the username for authentication.
     */
    public function username(): string
    {
        return 'phone';
    }

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'phone_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_probation' => 'boolean',
            'is_admin' => 'boolean',
            'probation_ends_at' => 'datetime',
            'deposited_at' => 'datetime',
            'username' => 'string',
            'deposit_locked_until' => 'datetime',
            'deposit_amount' => 'decimal:2',
            'balance' => 'decimal:2',
            'referral_earnings' => 'decimal:2',
            'total_earned' => 'decimal:2',
            'total_withdrawn' => 'decimal:2',
            'login_streak' => 'integer',
            'last_login_date' => 'date',
        ];
    }

    protected static function booted()
    {
        static::creating(function ($user) {
            if (empty($user->referral_code)) {
                $user->referral_code = self::generateReferralCode();
            }
        });
    }

    public static function generateReferralCode(): string
    {
        do {
            $code = strtoupper(Str::random(8));
        } while (static::where('referral_code', $code)->exists());

        return $code;
    }

    /**
     * Find a user by their phone number.
     */
    public function scopeWherePhone($query, $phone)
    {
        return $query->where('phone', $phone);
    }

    // Relationships
    public function level()
    {
        return $this->belongsTo(Level::class);
    }

    public function referrer()
    {
        return $this->belongsTo(User::class, 'referred_by');
    }

    public function referrals()
    {
        return $this->hasMany(User::class, 'referred_by');
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function referralCommissions()
    {
        return $this->hasMany(ReferralCommission::class, 'referrer_id');
    }

    public function taskSubmissions()
    {
        return $this->hasMany(TaskSubmission::class);
    }

    public function festiveRewards()
    {
        return $this->hasMany(FestiveReward::class);
    }

    public function withdrawals()
    {
        return $this->hasMany(Withdrawal::class);
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    public function notificationPreferences()
    {
        return $this->hasMany(NotificationPreference::class);
    }

    public function unreadNotificationsCount(): int
    {
        return $this->notifications()->where('is_read', false)->count();
    }

    public function badges()
    {
        return $this->belongsToMany(Badge::class, 'user_badges')
            ->withTimestamps()
            ->withPivot('awarded_at');
    }

    /**
     * Track daily login streak.
     */
    public function getDisplayNameAttribute(): string
    {
        return $this->username ? '@' . $this->username : $this->name;
    }

    public function trackLoginStreak(): void
    {
        $today = now()->toDateString();

        if ($this->last_login_date === $today) return;

        $yesterday = now()->subDay()->toDateString();
        if ($this->last_login_date === $yesterday) {
            $this->login_streak += 1;
        } else {
            $this->login_streak = 1;
        }
        $this->last_login_date = $today;
        $this->save();
    }

    /**
     * Get multi-level referral tree (up to 3 levels deep).
     */
    public function getReferralTree(): array
    {
        return $this->buildTree($this->id, 1);
    }

    private function buildTree(int $userId, int $currentLevel, int $maxLevel = 3): array
    {
        if ($currentLevel > $maxLevel) return [];

        $referrals = self::where('referred_by', $userId)->with('level')->get();
        $tree = [];

        foreach ($referrals as $referral) {
            $children = $this->buildTree($referral->id, $currentLevel + 1, $maxLevel);
            $tree[] = [
                'id' => $referral->id,
                'name' => $referral->display_name,
                'level_num' => $referral->level?->level ?? 0,
                'deposit' => $referral->deposit_amount,
                'joined' => $referral->created_at->diffForHumans(),
                'children' => $children,
            ];
        }

        return $tree;
    }

    /**
     * Get referral stats per level.
     */
    public function getReferralStats(): array
    {
        return [
            'level1_count' => self::where('referred_by', $this->id)->count(),
            'level2_count' => self::whereIn('referred_by', self::where('referred_by', $this->id)->pluck('id'))->count(),
            'level3_count' => self::whereIn('referred_by', self::whereIn('referred_by', self::where('referred_by', $this->id)->pluck('id'))->pluck('id'))->count(),
            'level1_earnings' => $this->referralCommissions()->where('level', 1)->sum('amount'),
            'level2_earnings' => $this->referralCommissions()->where('level', 2)->sum('amount'),
            'level3_earnings' => $this->referralCommissions()->where('level', 3)->sum('amount'),
        ];
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeProbation($query)
    {
        return $query->where('is_probation', true);
    }

    // Helpers
    public function isOnProbation(): bool
    {
        return $this->is_probation && $this->probation_ends_at && now()->lessThan($this->probation_ends_at);
    }

    public function hasCompletedProbation(): bool
    {
        return !$this->is_probation || ($this->probation_ends_at && now()->greaterThanOrEqualTo($this->probation_ends_at));
    }

    public function hasDeposited(): bool
    {
        return $this->deposit_amount > 0 && $this->deposited_at !== null;
    }

    public function isDepositLocked(): bool
    {
        return $this->deposit_locked_until && now()->lessThan($this->deposit_locked_until);
    }

    public function depositUnlocksIn(): string
    {
        if (!$this->isDepositLocked()) return 'Unlocked';
        return $this->deposit_locked_until->diffForHumans();
    }

    public function canPerformTasks(): bool
    {
        return $this->hasDeposited() && $this->hasCompletedProbation();
    }
}
