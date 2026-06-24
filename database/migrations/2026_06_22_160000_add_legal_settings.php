<?php

use App\Models\AdminSetting;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        AdminSetting::firstOrCreate(
            ['key' => 'terms_of_service'],
            [
                'value' => '<h3>1. Acceptance of Terms</h3>
<p>By creating an account on TaskEarn, you agree to be bound by these Terms of Service. If you do not agree, do not use the platform.</p>

<h3>2. Account Registration</h3>
<p>You must provide accurate and complete information when creating your account. You are responsible for maintaining the confidentiality of your login credentials. Each user is limited to one account. Multiple accounts will result in suspension.</p>

<h3>3. Deposits & Payments</h3>
<p>All deposits are processed through Paystack, our authorized payment partner. Deposits are non-refundable once your probation period ends and tasks are unlocked.</p>

<h3>4. Task Earnings</h3>
<p>Tasks must be completed according to the provided instructions. Submissions are reviewed by our team and may be approved or rejected. Fraudulent submissions will result in immediate account suspension and forfeiture of all earnings.</p>

<h3>5. Withdrawals</h3>
<p>Withdrawals are processed within 24-48 hours to your verified bank account. The minimum withdrawal amount is determined by the platform.</p>

<h3>6. Referral Program</h3>
<p>You earn commissions on deposits made by users you refer. Abusing the referral system (self-referrals, fake accounts) will result in forfeiture of commissions and account suspension.</p>

<h3>7. Prohibited Activities</h3>
<p>You may not: use bots or automation, submit fake task proofs, create multiple accounts, engage in fraudulent activity, harass other users, or attempt to exploit the platform.</p>

<h3>8. Account Termination</h3>
<p>We reserve the right to suspend or terminate any account for violation of these terms.</p>

<h3>9. Changes to Terms</h3>
<p>We may update these terms at any time. Continued use of the platform after changes constitutes acceptance of the new terms.</p>',
                'type' => 'text',
                'group' => 'legal',
                'description' => 'Terms of Service page content (HTML allowed)',
            ]
        );

        AdminSetting::firstOrCreate(
            ['key' => 'privacy_policy'],
            [
                'value' => '<h3>1. Information We Collect</h3>
<p>We collect the following information when you create an account: full name, phone number, username, and referral code. We also collect transaction data and task submission history.</p>

<h3>2. Bank Account Information</h3>
<p>When you save bank details or make a withdrawal, we collect your bank name, account number, and account name. This information is used solely for processing your withdrawals and is stored securely.</p>

<h3>3. How We Use Your Information</h3>
<p>Your information is used to: process transactions, verify your identity, communicate platform updates, display leaderboard rankings (using only your @username), and prevent fraud.</p>

<h3>4. Data Sharing</h3>
<p>We share your information only with: Paystack (our payment processor) for deposits and withdrawals, and law enforcement when required by law. We never sell your personal data to third parties.</p>

<h3>5. Data Security</h3>
<p>We implement industry-standard security measures including encryption at rest and in transit. Passwords are hashed using bcrypt. Payment information is handled by Paystack and never stored on our servers.</p>

<h3>6. Cookies</h3>
<p>We use essential cookies for session management and CSRF protection. We do not use tracking or advertising cookies.</p>

<h3>7. Data Retention</h3>
<p>We retain your data for as long as your account is active. If you delete your account, your personal data is permanently removed.</p>

<h3>8. Your Rights</h3>
<p>You have the right to: access your personal data, correct inaccurate data, delete your account and associated data, and withdraw consent where applicable.</p>',
                'type' => 'text',
                'group' => 'legal',
                'description' => 'Privacy Policy page content (HTML allowed)',
            ]
        );
    }

    public function down(): void
    {
        AdminSetting::whereIn('key', ['terms_of_service', 'privacy_policy'])->delete();
    }
};
