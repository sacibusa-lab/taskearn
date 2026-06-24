<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\NotificationPreference;
use App\Models\User;

class NotificationService
{
    /**
     * Send an in-app notification to a user.
     */
    public static function send(
        int $userId,
        string $type,
        string $title,
        ?string $message = null,
        ?string $actionUrl = null
    ): ?Notification {
        // Check user's preference for this notification type
        $pref = NotificationPreference::where('user_id', $userId)
            ->where('type', $type)
            ->first();

        if ($pref && !$pref->in_app) {
            return null;
        }

        $icon = Notification::getIcon($type);

        return Notification::create([
            'user_id' => $userId,
            'type' => $type,
            'title' => $title,
            'message' => $message,
            'icon' => $icon,
            'action_url' => $actionUrl,
        ]);
    }

    /**
     * Send notification to multiple users at once.
     */
    public static function sendBulk(
        array $userIds,
        string $type,
        string $title,
        ?string $message = null,
        ?string $actionUrl = null
    ): int {
        $count = 0;
        foreach ($userIds as $userId) {
            $result = self::send($userId, $type, $title, $message, $actionUrl);
            if ($result) $count++;
        }
        return $count;
    }

    /**
     * Send to all active users.
     */
    public static function sendToAll(
        string $type,
        string $title,
        ?string $message = null,
        ?string $actionUrl = null
    ): int {
        $userIds = User::active()->pluck('id')->toArray();
        return self::sendBulk($userIds, $type, $title, $message, $actionUrl);
    }

    /**
     * Get unread count for a user.
     */
    public static function unreadCount(int $userId): int
    {
        return Notification::forUser($userId)->unread()->count();
    }

    /**
     * Get recent notifications for a user.
     */
    public static function recent(int $userId, int $limit = 10)
    {
        return Notification::forUser($userId)
            ->latest()
            ->take($limit)
            ->get();
    }

    /**
     * Mark all notifications as read for a user.
     */
    public static function markAllRead(int $userId): int
    {
        return Notification::forUser($userId)->unread()->update([
            'is_read' => true,
            'read_at' => now(),
        ]);
    }
}
