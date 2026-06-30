<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\DepositController;
use App\Http\Controllers\ReferralController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\WithdrawalController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\SupportTicketController;
use App\Http\Controllers\Admin\SupportTicketController as AdminSupportTicketController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\WithdrawalController as AdminWithdrawalController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Admin\LevelController as AdminLevelController;
use App\Http\Controllers\Admin\ReferralController as AdminReferralController;
use App\Http\Controllers\Admin\RewardController as AdminRewardController;
use App\Http\Controllers\Admin\TaskController as AdminTaskController;
use App\Http\Controllers\Admin\SettingController as AdminSettingController;
use App\Http\Controllers\Admin\FestiveProgramController as AdminFestiveProgramController;
use App\Http\Controllers\Admin\AnalyticsController as AdminAnalyticsController;
use App\Http\Controllers\PaystackWebhookController;
use Illuminate\Support\Facades\Route;

Route::post('/api/paystack/webhook', [PaystackWebhookController::class, 'handle'])
    ->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class])
    ->name('paystack.webhook');

Route::post('/api/verify-account', [App\Http\Controllers\WithdrawalController::class, 'verifyAccount'])
    ->name('api.verify-account');

Route::middleware(['maintenance'])->group(function () {
Route::get('/', function () {
    return view('welcome');
});

Route::get('/terms', fn() => view('pages.terms'))->name('terms');
Route::get('/privacy', fn() => view('pages.privacy'))->name('privacy');

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Tasks
    Route::resource('tasks', TaskController::class)->only(['index', 'show']);
    Route::post('/tasks/{task}/submit', [TaskController::class, 'submit'])->name('tasks.submit');

    // Deposits
    Route::get('/deposits/create', [DepositController::class, 'create'])->name('deposits.create');
    Route::post('/deposits', [DepositController::class, 'store'])->name('deposits.store');
    Route::get('/deposits/callback', [DepositController::class, 'callback'])->name('deposits.callback');

    // Withdrawals
    Route::get('/withdrawals', [WithdrawalController::class, 'index'])->name('withdrawals.index');
    Route::get('/withdrawals/create', [WithdrawalController::class, 'create'])->name('withdrawals.create');
    Route::post('/withdrawals', [WithdrawalController::class, 'store'])->name('withdrawals.store');
    Route::get('/withdrawals/{withdrawal}', [WithdrawalController::class, 'show'])->name('withdrawals.show');

    // Referrals
    Route::get('/referrals', [ReferralController::class, 'index'])->name('referrals.index');

    // Transactions
    Route::get('/transactions', [TransactionController::class, 'index'])->name('transactions.index');

    // Support Tickets
    Route::get('/support', [SupportTicketController::class, 'index'])->name('support.index');
    Route::get('/support/create', [SupportTicketController::class, 'create'])->name('support.create');
    Route::post('/support', [SupportTicketController::class, 'store'])->name('support.store');
    Route::get('/support/{ticket}', [SupportTicketController::class, 'show'])->name('support.show');
    Route::post('/support/{ticket}/reply', [SupportTicketController::class, 'reply'])->name('support.reply');
    Route::post('/support/{ticket}/close', [SupportTicketController::class, 'close'])->name('support.close');

    // Leaderboard
    Route::get('/leaderboard', [\App\Http\Controllers\LeaderboardController::class, 'index'])->name('leaderboard.index');

    // Notifications
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/mark-all-read', [NotificationController::class, 'markAllRead'])->name('notifications.markAllRead');
    Route::get('/notifications/{notification}/read', [NotificationController::class, 'markRead'])->name('notifications.markRead');
    Route::get('/notifications/preferences', [NotificationController::class, 'preferences'])->name('notifications.preferences');
    Route::post('/notifications/preferences', [NotificationController::class, 'updatePreferences'])->name('notifications.preferences.update');
    Route::get('/notifications/poll', [NotificationController::class, 'poll'])->name('notifications.poll');

    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/bank', [ProfileController::class, 'updateBank'])->name('profile.bank');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Admin Routes
    Route::middleware('admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

        // Users
        Route::get('/users', [AdminUserController::class, 'index'])->name('users.index');
        Route::get('/users/{user}', [AdminUserController::class, 'show'])->name('users.show');
        Route::get('/users/{user}/edit', [AdminUserController::class, 'edit'])->name('users.edit');
        Route::put('/users/{user}', [AdminUserController::class, 'update'])->name('users.update');
        Route::post('/users/{user}/toggle-status', [AdminUserController::class, 'toggleStatus'])->name('users.toggle-status');
        Route::post('/users/{user}/unlock-deposit', [AdminUserController::class, 'unlockDeposit'])->name('users.unlock-deposit');

        // Levels
        Route::get('/levels', [AdminLevelController::class, 'index'])->name('levels.index');
        Route::put('/levels', [AdminLevelController::class, 'updateAll'])->name('levels.update-all');
        Route::put('/levels/{level}', [AdminLevelController::class, 'update'])->name('levels.update');

        // Referrals
        Route::get('/referrals', [AdminReferralController::class, 'index'])->name('referrals.index');
        Route::post('/referrals', [AdminReferralController::class, 'update'])->name('referrals.update');

        // Rewards
        Route::get('/rewards', [AdminRewardController::class, 'index'])->name('rewards.index');
        Route::post('/rewards', [AdminRewardController::class, 'update'])->name('rewards.update');

        // Tasks
        Route::get('/tasks', [AdminTaskController::class, 'index'])->name('tasks.index');
        Route::get('/tasks/create', [AdminTaskController::class, 'create'])->name('tasks.create');
        Route::post('/tasks', [AdminTaskController::class, 'store'])->name('tasks.store');
        Route::get('/tasks/{task}/edit', [AdminTaskController::class, 'edit'])->name('tasks.edit');
        Route::put('/tasks/{task}', [AdminTaskController::class, 'update'])->name('tasks.update');
        Route::delete('/tasks/{task}', [AdminTaskController::class, 'destroy'])->name('tasks.destroy');
        Route::get('/submissions', [AdminTaskController::class, 'submissions'])->name('submissions');
        Route::post('/submissions/{submission}/approve', [AdminTaskController::class, 'approveSubmission'])->name('submissions.approve');
        Route::post('/submissions/{submission}/reject', [AdminTaskController::class, 'rejectSubmission'])->name('submissions.reject');

        // Withdrawals
        Route::get('/withdrawals', [AdminWithdrawalController::class, 'index'])->name('withdrawals.index');
        Route::get('/withdrawals/{withdrawal}', [AdminWithdrawalController::class, 'show'])->name('withdrawals.show');
        Route::post('/withdrawals/{withdrawal}/approve', [AdminWithdrawalController::class, 'approve'])->name('withdrawals.approve');
        Route::post('/withdrawals/{withdrawal}/complete', [AdminWithdrawalController::class, 'markCompleted'])->name('withdrawals.complete');
        Route::post('/withdrawals/{withdrawal}/reject', [AdminWithdrawalController::class, 'reject'])->name('withdrawals.reject');

        // Festive Programs
        Route::get('/festive-programs', [AdminFestiveProgramController::class, 'index'])->name('festive-programs.index');
        Route::get('/festive-programs/create', [AdminFestiveProgramController::class, 'create'])->name('festive-programs.create');
        Route::post('/festive-programs', [AdminFestiveProgramController::class, 'store'])->name('festive-programs.store');
        Route::get('/festive-programs/{festiveProgram}/edit', [AdminFestiveProgramController::class, 'edit'])->name('festive-programs.edit');
        Route::put('/festive-programs/{festiveProgram}', [AdminFestiveProgramController::class, 'update'])->name('festive-programs.update');
        Route::post('/festive-programs/{festiveProgram}/distribute', [AdminFestiveProgramController::class, 'distribute'])->name('festive-programs.distribute');

        // Announcements
        Route::resource('announcements', \App\Http\Controllers\Admin\AnnouncementController::class)->except(['show']);
        Route::post('/announcements/{announcement}/toggle', [\App\Http\Controllers\Admin\AnnouncementController::class, 'toggle'])->name('announcements.toggle');

        // Support Tickets
        Route::get('/support', [AdminSupportTicketController::class, 'index'])->name('support.index');
        Route::get('/support/{ticket}', [AdminSupportTicketController::class, 'show'])->name('support.show');
        Route::post('/support/{ticket}/reply', [AdminSupportTicketController::class, 'reply'])->name('support.reply');
        Route::post('/support/{ticket}/close', [AdminSupportTicketController::class, 'close'])->name('support.close');
        Route::post('/support/{ticket}/reopen', [AdminSupportTicketController::class, 'reopen'])->name('support.reopen');

        // Settings
        Route::get('/settings', [AdminSettingController::class, 'index'])->name('settings.index');
        Route::post('/settings', [AdminSettingController::class, 'update'])->name('settings.update');

        // Analytics (redirected to dashboard, exports kept)
        Route::get('/analytics', fn() => redirect()->route('admin.dashboard'))->name('analytics.index');
        Route::get('/analytics/export-users', [AdminAnalyticsController::class, 'exportUsers'])->name('analytics.export-users');
        Route::get('/analytics/export-transactions', [AdminAnalyticsController::class, 'exportTransactions'])->name('analytics.export-transactions');
        Route::get('/analytics/export-withdrawals', [AdminAnalyticsController::class, 'exportWithdrawals'])->name('analytics.export-withdrawals');

        // Leaderboard
        Route::get('/leaderboard', [\App\Http\Controllers\Admin\LeaderboardController::class, 'index'])->name('leaderboard.index');
        Route::post('/leaderboard-reward/{period}', [\App\Http\Controllers\Admin\LeaderboardController::class, 'reward'])->name('leaderboard.reward');
    });
});
});

require __DIR__.'/auth.php';
