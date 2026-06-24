<?php

namespace App\Http\Controllers;

use App\Models\Level;
use App\Models\AdminSetting;
use App\Models\User;
use App\Services\NotificationService;
use App\Services\PaystackService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class DepositController extends Controller
{
    protected PaystackService $paystack;

    public function __construct(PaystackService $paystack)
    {
        $this->paystack = $paystack;
    }

    public function create()
    {
        $user = Auth::user();
        $levels = Level::where('level', '>', 0)->orderBy('level')->get();

        $paystackConfigured = $this->paystack->isConfigured();

        return view('deposits.create', compact('levels', 'user', 'paystackConfigured'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();

        // Check if deposit is locked
        if ($user->isDepositLocked()) {
            return back()->with('error', 'Your deposit is locked until ' . $user->deposit_locked_until->format('M d, Y') . '. You can only deposit once per month. You may upgrade after it unlocks.');
        }

        $request->validate([
            'amount' => ['required', 'numeric', 'min:1'],
            'payment_method' => ['required', 'string', 'in:paystack'],
        ]);

        $amount = $request->amount;

        if ($amount < 1) {
            return back()->with('error', 'Please select a valid deposit amount.');
        }

        // Find the level matching the deposit amount
        $level = Level::where('deposit_amount', '<=', $amount)
            ->orderBy('level', 'desc')
            ->first();

        if (!$level) {
            $level = Level::where('level', 0)->first();
        }

        $reference = 'DEP-' . strtoupper(Str::random(16));

        // Create pending transaction
        $transaction = $user->transactions()->create([
            'type' => 'deposit',
            'amount' => $amount,
            'balance_before' => $user->balance,
            'balance_after' => $user->balance + $amount,
            'status' => 'pending',
            'reference' => $reference,
            'description' => "Deposit of " . currency($amount),
        ]);

        // --- Paystack Payment ---
        $callbackUrl = route('deposits.callback', ['reference' => $reference]);
        $result = $this->paystack->initializeTransaction(
            $user->email,
            $amount,
            $reference,
            $callbackUrl
        );

        if ($result && isset($result['authorization_url'])) {
            return redirect($result['authorization_url']);
        }

        // ── Paystack failed — show the actual reason ──
        $errorMsg = 'Unable to process payment. ';
        if (!$this->paystack->isConfigured()) {
            $errorMsg .= 'Paystack API keys are not configured. Go to Admin → Settings → Paystack to set them up.';
        } else {
            $errorMsg .= 'Could not reach Paystack. Check your internet connection or firewall settings.';
        }

        return redirect()->route('deposits.create')
            ->with('error', $errorMsg);
    }

    /**
     * Handle Paystack callback after payment.
     */
    public function callback(Request $request)
    {
        $reference = $request->query('reference');
        $trxref = $request->query('trxref');

        if (!$reference && $trxref) {
            $reference = $trxref;
        }

        if (!$reference) {
            return redirect()->route('dashboard')->with('error', 'Invalid payment reference.');
        }

        $transaction = \App\Models\Transaction::where('reference', $reference)->first();

        if (!$transaction) {
            return redirect()->route('dashboard')->with('error', 'Transaction not found.');
        }

        if ($transaction->status === 'completed') {
            return redirect()->route('dashboard')->with('success', 'Payment already confirmed! ' . currency($transaction->amount) . ' added to your balance.');
        }

        // Verify with Paystack
        $data = $this->paystack->verifyTransaction($reference);

        if ($data && ($data['status'] === 'success')) {
            $this->completeDeposit($transaction);
            return redirect()->route('dashboard')
                ->with('success', 'Payment confirmed! ' . currency($transaction->amount) . ' has been added to your balance.');
        }

        return redirect()->route('dashboard')
            ->with('error', 'Payment verification failed. Please contact support if your money was deducted.');
    }

    /**
     * Complete a deposit: credit user, assign level, handle referral commission.
     */
    public static function completeDeposit(\App\Models\Transaction $transaction): void
    {
        if ($transaction->status === 'completed') {
            return;
        }

        $user = $transaction->user;
        $amount = $transaction->amount;

        $transaction->update(['status' => 'completed']);

        $user->increment('balance', $amount);
        $user->increment('total_earned', $amount);
        $user->increment('deposit_amount', $amount);

        if (!$user->deposited_at) {
            $user->update(['deposited_at' => now()]);
        }

        // Lock deposits for 1 month from now
        $user->update(['deposit_locked_until' => now()->addMonth()]);

        // Upgrade level
        $level = Level::where('deposit_amount', '<=', $user->deposit_amount)
            ->orderBy('level', 'desc')
            ->first();

        if ($level) {
            $user->update(['level_id' => $level->id]);
        }

        $user->transactions()
            ->where('id', $transaction->id)
            ->update([
                'balance_before' => $user->balance - $amount,
                'balance_after' => $user->balance,
            ]);

        // Send deposit notification
        NotificationService::send(
            $user->id,
            'deposit_received',
            'Deposit Received',
            currency($amount) . ' has been credited to your account.',
            route('transactions.index')
        );

        // Multi-Level Referral Commissions (Level 1: 10%, Level 2: 3%, Level 3: 1%)
        $mlmRates = [
            1 => (float) AdminSetting::getValue('referral_commission_rate', 10),
            2 => (float) AdminSetting::getValue('referral_level2_rate', 3),
            3 => (float) AdminSetting::getValue('referral_level3_rate', 1),
        ];

        $currentUser = $user;
        $ancestor = $currentUser->referrer;
        $level = 1;
        $sourceUserId = $user->id; // The original depositor

        while ($ancestor && $level <= 3) {
            $rate = $mlmRates[$level];
            if ($rate > 0) {
                $commissionAmount = ($amount * $rate) / 100;

                $ancestor->increment('referral_earnings', $commissionAmount);
                $ancestor->increment('balance', $commissionAmount);
                $ancestor->increment('total_earned', $commissionAmount);

                $ancestor->transactions()->create([
                    'type' => 'referral_bonus',
                    'amount' => $commissionAmount,
                    'balance_before' => $ancestor->balance - $commissionAmount,
                    'balance_after' => $ancestor->balance,
                    'status' => 'completed',
                    'description' => "Level {$level} referral commission ({$rate}%) from deposit by " . ($level === 1 ? $user->name : 'downline network'),
                ]);

                $ancestor->referralCommissions()->create([
                    'referred_user_id' => $user->id,
                    'source_user_id' => $sourceUserId,
                    'amount' => $commissionAmount,
                    'rate' => $rate,
                    'level' => $level,
                    'status' => 'completed',
                ]);

                NotificationService::send(
                    $ancestor->id,
                    'referral_bonus',
                    "Level {$level} Referral Bonus!",
                    'You earned ' . currency($commissionAmount) . ' (' . $rate . '%) from your downline network.',
                    route('referrals.index')
                );
            }

            $sourceUserId = $ancestor->id;
            $ancestor = $ancestor->referrer;
            $level++;
        }
    }
}
