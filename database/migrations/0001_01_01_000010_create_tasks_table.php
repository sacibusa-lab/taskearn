<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description');
            $table->text('instructions')->nullable();
            $table->decimal('reward', 15, 2);
            $table->integer('estimated_minutes')->default(0);
            $table->foreignId('level_id')->nullable()->constrained('levels')->nullOnDelete();
            $table->string('status')->default('active'); // active, inactive, completed
            $table->integer('total_slots')->default(0); // 0 = unlimited
            $table->integer('remaining_slots')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
