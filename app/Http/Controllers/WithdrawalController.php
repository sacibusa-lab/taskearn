<?php

namespace App\Http\Controllers;

use App\Models\AdminSetting;
use App\Models\Withdrawal;
use App\Services\PaystackService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class WithdrawalController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $withdrawals = $user->withdrawals()->latest()->paginate(15);
        $stats = [
            'balance' => $user->balance,
            'total_withdrawn' => $user->total_withdrawn,
            'min_withdrawal' => AdminSetting::getValue('min_withdrawal', 50),
            'max_withdrawal' => AdminSetting::getValue('max_withdrawal', 5000),
            'pending_count' => $user->withdrawals()->where('status', 'pending')->count(),
        ];

        return view('withdrawals.index', compact('withdrawals', 'stats'));
    }

    public function create(PaystackService $paystack)
    {
        $user = Auth::user();
        $min = AdminSetting::getValue('min_withdrawal', 50);
        $max = AdminSetting::getValue('max_withdrawal', 5000);

        if ($user->balance < $min) {
            return redirect()->route('withdrawals.index')
                ->with('error', "Your balance of " . currency($user->balance) . " is below the minimum withdrawal of " . currency($min) . ".");
        }

        $banks = $paystack->listBanks();

        return view('withdrawals.create', compact('min', 'max', 'banks'));
    }

    public function store(Request $request, PaystackService $paystack)
    {
        $user = Auth::user();
        $min = AdminSetting::getValue('min_withdrawal', 50);
        $max = AdminSetting::getValue('max_withdrawal', 5000);

        $request->validate([
            'amount' => ['required', 'numeric', 'min:' . $min, 'max:' . $max],
            'payout_method' => ['required', 'in:bank_transfer'],
            'bank_code' => ['required', 'string', 'max:20'],
            'account_number' => ['required', 'string', 'max:10'],
            'account_name' => ['required', 'string', 'max:255'],
        ]);

        if ($request->amount > $user->balance) {
            return back()->with('error', 'Insufficient balance.')->withInput();
        }

        $pendingCount = $user->withdrawals()->where('status', 'pending')->count();
        if ($pendingCount > 0) {
            return back()->with('error', 'You already have a pending withdrawal request. Please wait for it to be processed.')->withInput();
        }

        // Verify account via Paystack
        $resolve = $paystack->resolveAccount($request->account_number, $request->bank_code);
        if (!$resolve) {
            return back()->with('error', 'Could not verify your bank account. Please check the details and try again.')->withInput();
        }

        $accountDetails = [
            'bank_code' => $request->bank_code,
            'bank_name' => $resolve['bank_name'] ?? '',
            'account_name' => $resolve['account_name'] ?? $request->account_name,
            'account_number' => $request->account_number,
        ];

        $charge = 0; // Could add withdrawal fee logic here
        $netAmount = $request->amount - $charge;

        $withdrawal = $user->withdrawals()->create([
            'amount' => $request->amount,
            'charge' => $charge,
            'net_amount' => $netAmount,
            'payout_method' => $request->payout_method,
            'account_details' => $accountDetails,
            'status' => 'pending',
            'reference' => 'WTH-' . strtoupper(Str::random(12)),
        ]);

        // Deduct from balance immediately (held until processed)
        $user->decrement('balance', $request->amount);

        $user->transactions()->create([
            'type' => 'payout',
            'amount' => $request->amount,
            'balance_before' => $user->balance + $request->amount,
            'balance_after' => $user->balance,
            'status' => 'pending',
            'description' => "Withdrawal request via " . str_replace('_', ' ', $request->payout_method),
            'reference' => $withdrawal->reference,
        ]);

        return redirect()->route('withdrawals.index')
            ->with('success', 'Withdrawal request submitted! Your request of ' . currency($request->amount) . ' is pending admin approval.');
    }

    public function show(Withdrawal $withdrawal)
    {
        if ($withdrawal->user_id !== Auth::id() && !Auth::user()->is_admin) {
            abort(403);
        }

        return view('withdrawals.show', compact('withdrawal'));
    }

    /**
     * Verify bank account via Paystack (AJAX)
     */
    public function verifyAccount(Request $request, PaystackService $paystack)
    {
        $request->validate([
            'account_number' => ['required', 'string', 'size:10'],
            'bank_code' => ['required', 'string'],
        ]);

        $result = $paystack->resolveAccount(
            $request->account_number,
            $request->bank_code
        );

        if ($result) {
            return response()->json([
                'success' => true,
                'account_name' => $result['account_name'],
                'account_number' => $result['account_number'] ?? $request->account_number,
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Could not verify this account. Check the account number and bank.',
        ]);
    }
}
