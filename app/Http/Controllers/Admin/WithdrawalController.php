<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminSetting;
use App\Models\Withdrawal;
use App\Services\NotificationService;
use Illuminate\Http\Request;

class WithdrawalController extends Controller
{
    public function index(Request $request)
    {
        $withdrawals = Withdrawal::with('user')
            ->when($request->status, function ($q, $status) {
                $q->where('status', $status);
            })
            ->latest()
            ->paginate(20);

        $stats = [
            'pending' => Withdrawal::pending()->count(),
            'approved' => Withdrawal::where('status', 'approved')->count(),
            'completed' => Withdrawal::where('status', 'completed')->sum('amount'),
            'total_requested' => Withdrawal::sum('amount'),
        ];

        return view('admin.withdrawals.index', compact('withdrawals', 'stats'));
    }

    public function show(Withdrawal $withdrawal)
    {
        $withdrawal->load('user', 'processor');
        return view('admin.withdrawals.show', compact('withdrawal'));
    }

    public function approve(Request $request, Withdrawal $withdrawal)
    {
        if ($withdrawal->status !== 'pending') {
            return back()->with('error', 'This withdrawal has already been processed.');
        }

        $request->validate([
            'admin_notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $withdrawal->update([
            'status' => 'approved',
            'admin_notes' => $request->admin_notes,
            'processed_at' => now(),
            'processed_by' => auth()->id(),
        ]);

        $withdrawal->user->transactions()
            ->where('reference', $withdrawal->reference)
            ->update(['status' => 'completed']);

        NotificationService::send(
            $withdrawal->user_id,
            'withdrawal_status',
            'Withdrawal Approved',
            'Your withdrawal of ' . currency($withdrawal->amount) . ' has been approved and is being processed.',
            route('withdrawals.index')
        );

        return redirect()->route('admin.withdrawals.index')
            ->with('success', 'Withdrawal #' . $withdrawal->reference . ' approved. Payment is now being processed.');
    }

    public function markCompleted(Request $request, Withdrawal $withdrawal)
    {
        if ($withdrawal->status !== 'approved') {
            return back()->with('error', 'Only approved withdrawals can be marked as completed.');
        }

        $withdrawal->update([
            'status' => 'completed',
            'processed_at' => now(),
            'processed_by' => auth()->id(),
        ]);

        $user = $withdrawal->user;
        $user->increment('total_withdrawn', $withdrawal->amount);

        NotificationService::send(
            $withdrawal->user_id,
            'withdrawal_status',
            'Withdrawal Completed',
            'Your withdrawal of ' . currency($withdrawal->amount) . ' has been sent successfully.',
            route('withdrawals.index')
        );

        return redirect()->route('admin.withdrawals.index')
            ->with('success', 'Withdrawal #' . $withdrawal->reference . ' marked as completed.');
    }

    public function reject(Request $request, Withdrawal $withdrawal)
    {
        if ($withdrawal->status !== 'pending') {
            return back()->with('error', 'This withdrawal has already been processed.');
        }

        $request->validate([
            'admin_notes' => ['required', 'string', 'max:1000'],
        ]);

        $withdrawal->update([
            'status' => 'rejected',
            'admin_notes' => $request->admin_notes,
            'processed_at' => now(),
            'processed_by' => auth()->id(),
        ]);

        // Refund the amount back to user
        $user = $withdrawal->user;
        $user->increment('balance', $withdrawal->amount);

        $user->transactions()->create([
            'type' => 'admin_adjustment',
            'amount' => $withdrawal->amount,
            'balance_before' => $user->balance - $withdrawal->amount,
            'balance_after' => $user->balance,
            'status' => 'completed',
            'description' => 'Refund for rejected withdrawal #' . $withdrawal->reference,
        ]);

        $user->transactions()
            ->where('reference', $withdrawal->reference)
            ->update(['status' => 'cancelled']);

        NotificationService::send(
            $withdrawal->user_id,
            'withdrawal_status',
            'Withdrawal Declined',
            'Your withdrawal of ' . currency($withdrawal->amount) . ' was declined. Reason: ' . $request->admin_notes . '. Funds have been refunded.',
            route('withdrawals.index')
        );

        return redirect()->route('admin.withdrawals.index')
            ->with('success', 'Withdrawal #' . $withdrawal->reference . ' rejected. Funds refunded to user.');
    }
}
