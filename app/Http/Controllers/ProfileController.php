<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Services\PaystackService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    private PaystackService $paystack;

    public function __construct(PaystackService $paystack)
    {
        $this->paystack = $paystack;
    }

    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        $banks = $this->paystack->listBanks();

        return view('profile.edit', [
            'user' => $request->user(),
            'banks' => $banks,
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('phone')) {
            $request->user()->phone_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Update bank details.
     */
    public function updateBank(Request $request): RedirectResponse
    {
        $request->validate([
            'bank_code' => ['required', 'string'],
            'bank_account_number' => ['required', 'string', 'min:10', 'max:10'],
            'bank_account_name' => ['required', 'string'],
        ]);

        $user = $request->user();
        $user->update([
            'bank_code' => $request->bank_code,
            'bank_account_number' => $request->bank_account_number,
            'bank_account_name' => $request->bank_account_name,
            'bank_name' => collect($this->paystack->listBanks())->firstWhere('code', $request->bank_code)['name'] ?? '',
        ]);

        return Redirect::route('profile.edit')->with('status', 'bank-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
