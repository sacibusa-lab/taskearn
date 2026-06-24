<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminSetting;
use App\Models\ReferralCommission;
use App\Models\User;
use Illuminate\Http\Request;

class ReferralController extends Controller
{
    public function index()
    {
        $stats = [
            'total_referrals' => User::whereNotNull('referred_by')->count(),
            'total_commissions' => ReferralCommission::sum('amount'),
            'top_referrers' => User::has('referrals')
                ->withCount('referrals')
                ->orderByDesc('referrals_count')
                ->take(10)
                ->get(),
            'recent_commissions' => ReferralCommission::with(['referrer', 'referredUser', 'sourceUser'])
                ->latest()
                ->take(10)
                ->get(),
        ];

        $settings = [
            'referral_commission_rate' => AdminSetting::getValue('referral_commission_rate', 10),
            'referral_level2_rate' => AdminSetting::getValue('referral_level2_rate', 3),
            'referral_level3_rate' => AdminSetting::getValue('referral_level3_rate', 1),
            'max_referral_levels' => AdminSetting::getValue('max_referral_levels', 3),
        ];

        return view('admin.referrals.index', compact('stats', 'settings'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'referral_commission_rate' => ['required', 'numeric', 'min:0', 'max:100'],
            'referral_level2_rate' => ['required', 'numeric', 'min:0', 'max:100'],
            'referral_level3_rate' => ['required', 'numeric', 'min:0', 'max:100'],
            'max_referral_levels' => ['required', 'integer', 'min:1', 'max:10'],
        ]);

        AdminSetting::setValue('referral_commission_rate', $request->referral_commission_rate, 'Level 1 referral commission percentage');
        AdminSetting::setValue('referral_level2_rate', $request->referral_level2_rate, 'Level 2 referral commission percentage');
        AdminSetting::setValue('referral_level3_rate', $request->referral_level3_rate, 'Level 3 referral commission percentage');
        AdminSetting::setValue('max_referral_levels', $request->max_referral_levels, 'Maximum depth of multi-level referral commission');

        return back()->with('success', 'Referral settings updated.');
    }
}
