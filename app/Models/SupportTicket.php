<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SupportTicket extends Model
{
    protected $fillable = [
        'user_id',
        'subject',
        'category',
        'priority',
        'status',
        'messages',
        'closed_at',
    ];

    protected $casts = [
        'messages' => 'array',
        'closed_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scopeOpen($query)
    {
        return $query->whereIn('status', ['open', 'replied']);
    }

    public function scopeClosed($query)
    {
        return $query->where('status', 'closed');
    }

    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function addMessage(string $senderType, string $message): void
    {
        $messages = $this->messages ?? [];
        $messages[] = [
            'sender_type' => $senderType, // 'user' or 'admin'
            'message' => $message,
            'created_at' => now()->toIso8601String(),
        ];
        $this->messages = $messages;
        $this->save();
    }

    public function lastMessage(): ?string
    {
        $messages = $this->messages ?? [];
        if (empty($messages)) return null;
        $last = end($messages);
        return $last['message'] ?? null;
    }

    public function lastMessagePreview(int $length = 80): string
    {
        $msg = $this->lastMessage();
        if (!$msg) return '(no messages)';
        return strlen($msg) > $length ? substr($msg, 0, $length) . '...' : $msg;
    }

    public static function categories(): array
    {
        return [
            'general' => 'General Inquiry',
            'account' => 'Account Issue',
            'payment' => 'Payment Issue',
            'technical' => 'Technical Problem',
            'other' => 'Other',
        ];
    }
}
