<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('level_id')->nullable()->constrained('levels')->nullOnDelete();
            $table->decimal('deposit_amount', 15, 2)->default(0);
            $table->boolean('is_probation')->default(true);
            $table->timestamp('probation_ends_at')->nullable();
            $table->foreignId('referred_by')->nullable()->constrained('users')->nullOnDelete();
            $table->decimal('balance', 15, 2)->default(0);
            $table->decimal('referral_earnings', 15, 2)->default(0);
            $table->decimal('total_earned', 15, 2)->default(0);
            $table->decimal('total_withdrawn', 15, 2)->default(0);
            $table->boolean('is_admin')->default(false);
            $table->string('status')->default('active'); // active, suspended, banned
            $table->string('referral_code')->unique()->nullable();
            $table->timestamp('deposited_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['level_id']);
            $table->dropForeign(['referred_by']);
            $table->dropColumn([
                'level_id', 'deposit_amount', 'is_probation', 'probation_ends_at',
                'referred_by', 'balance', 'referral_earnings', 'total_earned',
                'total_withdrawn', 'is_admin', 'status', 'referral_code', 'deposited_at'
            ]);
        });
    }
};
