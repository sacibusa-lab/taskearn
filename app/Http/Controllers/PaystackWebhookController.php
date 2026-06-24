<?php

namespace App\Http\Controllers;

use App\Models\AdminSetting;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PaystackWebhookController extends Controller
{
    public function handle(Request $request)
    {
        // Verify Paystack signature
        $signature = $request->header('x-paystack-signature');
        $secret = AdminSetting::getValue('paystack_secret_key', '');

        if (!$signature || $signature !== hash_hmac('sha512', $request->getContent(), $secret)) {
            Log::warning('Paystack webhook: Invalid signature');
            return response()->json(['status' => 'invalid signature'], 401);
        }

        $event = $request->input('event');
        $data = $request->input('data');

        if (!$event || !$data) {
            return response()->json(['status' => 'invalid payload'], 400);
        }

        switch ($event) {
            case 'charge.success':
                $this->handleChargeSuccess($data);
                break;

            case 'dedicated_account.assign.success':
                $this->handleDvaAssigned($data);
                break;

            case 'transfer.success':
                $this->handleTransferSuccess($data);
                break;

            default:
                Log::info('Paystack webhook: Unhandled event', ['event' => $event]);
        }

        return response()->json(['status' => 'success']);
    }

    protected function handleChargeSuccess(array $data): void
    {
        $reference = $data['reference'] ?? null;
        $status = $data['status'] ?? null;

        if (!$reference || $status !== 'success') {
            return;
        }

        $transaction = Transaction::where('reference', $reference)->first();
        if (!$transaction || $transaction->status === 'completed') {
            return;
        }

        // Use the shared deposit completion logic
        \App\Http\Controllers\DepositController::completeDeposit($transaction);

        Log::info("Paystack webhook: Deposit {$reference} completed successfully.");
    }

    protected function handleDvaAssigned(array $data): void
    {
        $email = $data['customer']['email'] ?? null;
        $accountNumber = $data['account_number'] ?? null;
        $bankName = $data['bank']['name'] ?? null;

        if ($email && $accountNumber) {
            Log::info("Paystack DVA assigned: {$accountNumber} ({$bankName}) for {$email}");
        }
    }

    protected function handleTransferSuccess(array $data): void
    {
        $reference = $data['reference'] ?? null;
        $status = $data['status'] ?? null;

        if ($reference && $status === 'success') {
            $transaction = Transaction::where('reference', $reference)->first();
            if ($transaction) {
                $transaction->update(['status' => 'completed']);
                Log::info("Paystack: Transfer {$reference} completed");
            }
        }
    }
}
