<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('referral_commissions', function (Blueprint $table) {
            $table->integer('level')->default(1)->after('rate');
            $table->foreignId('source_user_id')->nullable()->after('referred_user_id')->constrained('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('referral_commissions', function (Blueprint $table) {
            $table->dropForeign(['source_user_id']);
            $table->dropColumn(['level', 'source_user_id']);
        });
    }
};
