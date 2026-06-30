<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\AdminSetting;
use App\Models\Level;
use App\Models\NotificationPreference;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    public function create(): View
    {
        return view('auth.register');
    }

    public function store(Request $request): RedirectResponse
    {
        // Anti-bot: honeypot check (hidden field must be empty)
        if (!empty($request->website)) {
            abort(422, 'Bot detected');
        }

        // Anti-bot: minimum time check (form must take at least 3 seconds)
        $submittedAt = now()->timestamp;
        $formStarted = (int) $request->form_started_at;
        if ($formStarted > 0 && ($submittedAt - $formStarted) < 3) {
            throw ValidationException::withMessages(['form' => 'Please wait a moment before submitting.']);
        }

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'min:3', 'max:30', 'regex:/^[a-zA-Z0-9_]+$/', 'unique:users,username'],
            'phone' => ['required', 'string', 'regex:/^[0-9]+$/', 'min:10', 'max:20', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'referral_code' => ['required', 'string', 'exists:users,referral_code'],
            'terms' => ['required', 'accepted'],
            'privacy' => ['required', 'accepted'],
            'device_fingerprint' => ['nullable', 'string', 'max:255'],
        ]);

        // Fraud detection: check if phone is already linked to a different device
        $fingerprint = $request->device_fingerprint;
        if ($fingerprint && User::isPhoneOnDifferentDevice($request->phone, $fingerprint)) {
            throw ValidationException::withMessages(['phone' => 'This phone number is already registered from a different device.']);
        }

        // Fraud detection: limit accounts per device
        if ($fingerprint && User::deviceAccountCount($fingerprint) >= 3) {
            throw ValidationException::withMessages(['form' => 'Too many accounts registered from this device.']);
        }

        $probationDays = (int) AdminSetting::getValue('probation_days', 3);

        $referrer = null;
        if ($request->referral_code) {
            $referrer = User::where('referral_code', $request->referral_code)->first();
        }

        $level0 = Level::where('level', 0)->first();

        $user = User::create([
            'name' => $request->name,
            'username' => $request->username,
            'phone' => $request->phone,
            'email' => $request->phone . '@taskearn.com',
            'password' => Hash::make($request->password),
            'level_id' => $level0?->id,
            'is_probation' => true,
            'probation_ends_at' => now()->addDays($probationDays),
            'referred_by' => $referrer?->id,
            'status' => 'active',
            'device_fingerprint' => $fingerprint,
            'device_fingerprinted_at' => $fingerprint ? now() : null,
            'registered_ip' => $request->ip(),
        ]);

        // Process referral commission if applicable
        if ($referrer) {
            $commissionRate = (float) AdminSetting::getValue('referral_commission_rate', 10);
            // Initial signup bonus for referrer
            $bonus = $commissionRate; // Small flat bonus for signup
            $referrer->increment('referral_earnings', $bonus);
            $referrer->increment('balance', $bonus);
            $referrer->increment('total_earned', $bonus);

            $referrer->transactions()->create([
                'type' => 'referral_bonus',
                'amount' => $bonus,
                'balance_before' => $referrer->balance - $bonus,
                'balance_after' => $referrer->balance,
                'status' => 'completed',
                'description' => "Referral bonus for {$user->name}'s registration",
            ]);

            $referrer->referralCommissions()->create([
                'referred_user_id' => $user->id,
                'amount' => $bonus,
                'rate' => $commissionRate,
                'status' => 'completed',
            ]);
        }

        // Initialize default notification preferences
        NotificationPreference::initDefaults($user->id);

        event(new Registered($user));
        Auth::login($user);

        return redirect(route('dashboard'));
    }
}
