<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('liquidity_pools', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('token_a');
            $table->string('token_b');
            $table->decimal('tvl', 20, 2);
            $table->decimal('apy', 8, 2);
            $table->decimal('fee_tier', 5, 4);
            $table->decimal('admin_fee_share', 5, 2);
            $table->decimal('volume_24h', 20, 2);
            $table->decimal('token_a_reserve', 20, 8);
            $table->decimal('token_b_reserve', 20, 8);
            $table->decimal('pool_token_price', 16, 8);
            $table->decimal('min_deposit', 16, 2);
            $table->integer('lock_days')->default(0);
            $table->decimal('buffer_percent', 5, 2)->default(20);
            $table->decimal('per_withdrawal_percent', 5, 2)->default(5);
            $table->enum('status', ['active', 'paused', 'ended']);
            $table->string('image')->nullable();
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('liquidity_positions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('liquidity_pool_id')->constrained('liquidity_pools')->onDelete('cascade');
            $table->decimal('amount_deposited', 16, 2);
            $table->decimal('lp_tokens', 20, 8);
            $table->decimal('earned_fees', 16, 4);
            $table->decimal('earned_rewards', 16, 4);
            $table->decimal('impermanent_loss', 16, 4);
            $table->decimal('current_value', 16, 2);
            $table->enum('admin_status', ['profitable', 'loss', 'rugged'])->nullable();
            $table->enum('status', ['active', 'withdrawn', 'locked']);
            $table->timestamp('start_date');
            $table->timestamp('unlock_date')->nullable();
            $table->boolean('is_demo')->default(false);
            $table->decimal('buffer_percent', 5, 2)->default(20);
            $table->decimal('per_withdrawal_percent', 5, 2)->default(5);
            $table->integer('splits_paid')->default(0);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('liquidity_positions');
        Schema::dropIfExists('liquidity_pools');
    }
};
