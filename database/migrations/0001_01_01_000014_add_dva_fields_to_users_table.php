<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('dva_bank_name')->nullable()->after('phone_verified_at');
            $table->string('dva_account_number')->nullable()->after('dva_bank_name');
            $table->string('dva_account_name')->nullable()->after('dva_account_number');
            $table->string('dva_customer_code')->nullable()->after('dva_account_name');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['dva_bank_name', 'dva_account_number', 'dva_account_name', 'dva_customer_code']);
        });
    }
};
