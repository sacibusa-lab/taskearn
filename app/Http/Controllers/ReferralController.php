<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReferralController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        $referrals = $user->referrals()
            ->with('level')
            ->latest()
            ->paginate(15);

        $commissions = $user->referralCommissions()
            ->with(['referredUser', 'sourceUser'])
            ->latest()
            ->paginate(15, ['*'], 'commissions_page');

        $mlmStats = $user->getReferralStats();
        $referralTree = $user->getReferralTree();

        $stats = [
            'total_referrals' => $user->referrals()->count(),
            'total_commissions' => $user->referral_earnings,
            'referral_code' => $user->referral_code,
            'referral_url' => route('register', ['ref' => $user->referral_code]),
        ];

        $referralSettings = [
            'l1_rate' => \App\Models\AdminSetting::getValue('referral_commission_rate', 10),
            'l2_rate' => \App\Models\AdminSetting::getValue('referral_level2_rate', 3),
            'l3_rate' => \App\Models\AdminSetting::getValue('referral_level3_rate', 1),
            'max_levels' => (int) \App\Models\AdminSetting::getValue('max_referral_levels', 3),
        ];

        return view('referrals.index', compact('referrals', 'commissions', 'stats', 'mlmStats', 'referralTree', 'referralSettings'));
    }
}
