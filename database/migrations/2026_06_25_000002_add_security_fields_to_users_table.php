<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('device_fingerprint')->nullable()->after('remember_token');
            $table->timestamp('device_fingerprinted_at')->nullable()->after('device_fingerprint');
            $table->string('registered_ip', 45)->nullable()->after('device_fingerprinted_at');
            $table->string('last_login_ip', 45)->nullable()->after('registered_ip');
            $table->string('last_user_agent')->nullable()->after('last_login_ip');
            $table->timestamp('withdrawal_cooldown_until')->nullable()->after('last_user_agent');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'device_fingerprint',
                'device_fingerprinted_at',
                'registered_ip',
                'last_login_ip',
                'last_user_agent',
                'withdrawal_cooldown_until',
            ]);
        });
    }
};
