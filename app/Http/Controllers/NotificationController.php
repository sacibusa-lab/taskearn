<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use App\Models\NotificationPreference;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $notifications = Notification::forUser($user->id)->latest()->paginate(20);
        $unreadCount = NotificationService::unreadCount($user->id);

        return view('notifications.index', compact('notifications', 'unreadCount'));
    }

    public function markRead(Notification $notification)
    {
        if ($notification->user_id !== Auth::id()) {
            abort(403);
        }

        $notification->markAsRead();

        if ($notification->action_url) {
            return redirect($notification->action_url);
        }

        return back();
    }

    public function markAllRead()
    {
        NotificationService::markAllRead(Auth::id());
        return back()->with('success', 'All notifications marked as read.');
    }

    public function preferences()
    {
        $user = Auth::user();
        $preferences = NotificationPreference::where('user_id', $user->id)->get()->keyBy('type');

        // Initialize defaults for any missing types
        foreach (NotificationPreference::getDefaultTypes() as $type) {
            if (!$preferences->has($type)) {
                $pref = NotificationPreference::firstOrCreate(
                    ['user_id' => $user->id, 'type' => $type],
                    ['email' => true, 'sms' => false, 'in_app' => true]
                );
                $preferences->put($type, $pref);
            }
        }

        return view('notifications.preferences', compact('preferences'));
    }

    public function updatePreferences(Request $request)
    {
        $user = Auth::user();
        $types = NotificationPreference::getDefaultTypes();

        foreach ($types as $type) {
            $data = $request->input("preferences.$type", []);
            NotificationPreference::updateOrCreate(
                ['user_id' => $user->id, 'type' => $type],
                [
                    'in_app' => filter_var($data['in_app'] ?? true, FILTER_VALIDATE_BOOLEAN),
                    'email' => filter_var($data['email'] ?? true, FILTER_VALIDATE_BOOLEAN),
                    'sms' => filter_var($data['sms'] ?? false, FILTER_VALIDATE_BOOLEAN),
                ]
            );
        }

        return back()->with('success', 'Notification preferences updated.');
    }

    /**
     * Poll for new notifications (AJAX).
     */
    public function poll(Request $request)
    {
        $user = Auth::user();
        $since = $request->query('since'); // timestamp of last check

        $query = Notification::forUser($user->id);
        if ($since) {
            $query->where('created_at', '>', $since);
        }

        $newNotifications = $query->latest()->take(5)->get();
        $unreadCount = NotificationService::unreadCount($user->id);

        return response()->json([
            'unread_count' => $unreadCount,
            'notifications' => $newNotifications->map(fn($n) => [
                'id' => $n->id,
                'type' => $n->type,
                'title' => $n->title,
                'message' => $n->message,
                'action_url' => $n->action_url,
                'is_read' => $n->is_read,
                'created_at' => $n->created_at->diffForHumans(),
                'timestamp' => $n->created_at->toIso8601String(),
            ]),
            'server_time' => now()->toIso8601String(),
        ]);
    }
}
