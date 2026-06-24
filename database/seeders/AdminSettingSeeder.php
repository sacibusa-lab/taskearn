<?php

namespace Database\Seeders;

use App\Models\AdminSetting;
use Illuminate\Database\Seeder;

class AdminSettingSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            // === Global Settings ===
            [
                'key' => 'site_name',
                'value' => 'TaskEarn',
                'type' => 'string',
                'group' => 'global',
                'description' => 'Site name displayed across the platform',
            ],
            [
                'key' => 'site_description',
                'value' => 'Complete tasks, earn rewards!',
                'type' => 'string',
                'group' => 'global',
                'description' => 'Short site description / tagline',
            ],
            [
                'key' => 'site_logo',
                'value' => '',
                'type' => 'string',
                'group' => 'global',
                'description' => 'URL or path to site logo (recommended: 180x60px)',
            ],
            [
                'key' => 'site_favicon',
                'value' => '',
                'type' => 'string',
                'group' => 'global',
                'description' => 'URL or path to favicon (recommended: 32x32px .ico/.png)',
            ],
            [
                'key' => 'probation_days',
                'value' => '3',
                'type' => 'number',
                'group' => 'global',
                'description' => 'Number of days for new user probation period',
            ],
            [
                'key' => 'referral_commission_rate',
                'value' => '10',
                'type' => 'number',
                'group' => 'global',
                'description' => 'Percentage commission earned on referred users deposits',
            ],
            [
                'key' => 'max_referral_levels',
                'value' => '3',
                'type' => 'number',
                'group' => 'global',
                'description' => 'Maximum depth of multi-level referral commission',
            ],
            [
                'key' => 'auto_approve_tasks',
                'value' => 'false',
                'type' => 'boolean',
                'group' => 'global',
                'description' => 'Auto-approve task submissions without admin review',
            ],

            // === Paystack (DVA & Payments) ===
            [
                'key' => 'paystack_public_key',
                'value' => '',
                'type' => 'string',
                'group' => 'paystack',
                'description' => 'Paystack Live Public Key',
            ],
            [
                'key' => 'paystack_secret_key',
                'value' => '',
                'type' => 'string',
                'group' => 'paystack',
                'description' => 'Paystack Live Secret Key',
            ],
            [
                'key' => 'paystack_dva_enabled',
                'value' => 'false',
                'type' => 'boolean',
                'group' => 'paystack',
                'description' => 'Enable Dedicated Virtual Accounts (DVA) for deposits',
            ],
            [
                'key' => 'paystack_currency',
                'value' => 'NGN',
                'type' => 'string',
                'group' => 'paystack',
                'description' => 'Currency for Paystack transactions (e.g. NGN, USD)',
            ],

            // === Currency & Payments ===
            [
                'key' => 'currency_code',
                'value' => 'NGN',
                'type' => 'string',
                'group' => 'currency',
                'description' => 'Currency code (e.g. USD, NGN, EUR, GBP)',
            ],
            [
                'key' => 'currency_symbol',
                'value' => '₦',
                'type' => 'string',
                'group' => 'currency',
                'description' => 'Currency symbol (e.g. $, ₦, €, £)',
            ],
            [
                'key' => 'exchange_rate',
                'value' => '1',
                'type' => 'number',
                'group' => 'currency',
                'description' => 'Exchange rate relative to USD (1 USD = X)',
            ],
            [
                'key' => 'min_withdrawal',
                'value' => '50',
                'type' => 'number',
                'group' => 'currency',
                'description' => 'Minimum withdrawal amount in your currency',
            ],
            [
                'key' => 'max_withdrawal',
                'value' => '5000',
                'type' => 'number',
                'group' => 'currency',
                'description' => 'Maximum withdrawal amount per transaction in your currency',
            ],
            [
                'key' => 'weekly_payout_day',
                'value' => 'Monday',
                'type' => 'string',
                'group' => 'currency',
                'description' => 'Day of the week when weekly payouts are processed',
            ],

            // === Theme & Customization ===
            [
                'key' => 'theme_primary_color',
                'value' => '#4f46e5',
                'type' => 'string',
                'group' => 'theme',
                'description' => 'Primary brand color (hex code, e.g. #4f46e5)',
            ],
            [
                'key' => 'theme_primary_hover',
                'value' => '#4338ca',
                'type' => 'string',
                'group' => 'theme',
                'description' => 'Primary hover color (usually darker than primary)',
            ],
            [
                'key' => 'custom_css',
                'value' => '',
                'type' => 'text',
                'group' => 'theme',
                'description' => 'Custom CSS injected into the <head> — use for additional styling',
            ],
            [
                'key' => 'custom_js',
                'value' => '',
                'type' => 'text',
                'group' => 'theme',
                'description' => 'Custom JavaScript injected before </body> — use for analytics or widgets',
            ],

            // === Maintenance Mode ===
            [
                'key' => 'maintenance_mode',
                'value' => 'false',
                'type' => 'boolean',
                'group' => 'maintenance',
                'description' => 'Enable maintenance mode (only admins can access the site)',
            ],
            [
                'key' => 'maintenance_message',
                'value' => 'We are currently performing maintenance. Please check back soon.',
                'type' => 'string',
                'group' => 'maintenance',
                'description' => 'Message shown to users during maintenance',
            ],

            // === Legal Pages ===
            [
                'key' => 'terms_of_service',
                'value' => '<h3>1. Acceptance of Terms</h3><p>By creating an account, you agree to be bound by these Terms of Service.</p>',
                'type' => 'text',
                'group' => 'legal',
                'description' => 'Terms of Service page content (HTML allowed)',
            ],
            [
                'key' => 'privacy_policy',
                'value' => '<h3>1. Information We Collect</h3><p>We collect your name, phone number, username, and transaction data.</p>',
                'type' => 'text',
                'group' => 'legal',
                'description' => 'Privacy Policy page content (HTML allowed)',
            ],
        ];

        foreach ($settings as $setting) {
            AdminSetting::firstOrCreate(
                ['key' => $setting['key']],
                $setting
            );
        }
    }
}
