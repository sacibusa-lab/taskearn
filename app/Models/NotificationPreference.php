<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NotificationPreference extends Model
{
    protected $fillable = [
        'user_id',
        'type',
        'email',
        'sms',
        'in_app',
    ];

    protected $casts = [
        'email' => 'boolean',
        'sms' => 'boolean',
        'in_app' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public static function getDefaultTypes(): array
    {
        return [
            'task_approved',
            'task_rejected',
            'deposit_received',
            'referral_bonus',
            'withdrawal_status',
            'festive_reward',
            'festive_status',
        ];
    }

    public static function getTypeLabel(string $type): string
    {
        return match ($type) {
            'task_approved' => 'Task Approved',
            'task_rejected' => 'Task Rejected',
            'deposit_received' => 'Deposit Received',
            'referral_bonus' => 'Referral Bonus',
            'withdrawal_status' => 'Withdrawal Status',
            'festive_reward' => 'Festive Rewards',
            'festive_status' => 'Festive Program Updates',
            default => ucwords(str_replace('_', ' ', $type)),
        };
    }

    public static function initDefaults(int $userId): void
    {
        foreach (self::getDefaultTypes() as $type) {
            self::firstOrCreate(
                ['user_id' => $userId, 'type' => $type],
                ['email' => true, 'sms' => false, 'in_app' => true]
            );
        }
    }
}
