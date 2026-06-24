<?php

namespace App\Http\Middleware;

use App\Models\AdminSetting;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MaintenanceMode
{
    public function handle(Request $request, Closure $next)
    {
        $enabled = AdminSetting::getValue('maintenance_mode', 'false') === 'true';

        if ($enabled && !Auth::check()?->user()?->is_admin) {
            if ($request->expectsJson()) {
                return response()->json(['message' => AdminSetting::getValue('maintenance_message', 'Under maintenance.')], 503);
            }
            return response()->view('maintenance', [
                'message' => AdminSetting::getValue('maintenance_message', 'We are currently performing maintenance. Please check back soon.'),
            ], 503);
        }

        return $next($request);
    }
}
