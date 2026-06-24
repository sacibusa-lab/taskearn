<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Withdrawal extends Model
{
    protected $fillable = [
        'user_id',
        'amount',
        'charge',
        'net_amount',
        'payout_method',
        'account_details',
        'status',
        'admin_notes',
        'processed_at',
        'processed_by',
        'reference',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'charge' => 'decimal:2',
        'net_amount' => 'decimal:2',
        'account_details' => 'array',
        'processed_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function processor()
    {
        return $this->belongsTo(User::class, 'processed_by');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function getAccountSummaryAttribute(): string
    {
        $details = $this->account_details;
        return match ($this->payout_method) {
            'bank_transfer' => ($details['bank_name'] ?? '') . ' - ' . ($details['account_number'] ?? ''),
            'crypto' => ($details['wallet_address'] ?? ''),
            'paypal' => ($details['paypal_email'] ?? ''),
            default => '—',
        };
    }
}
