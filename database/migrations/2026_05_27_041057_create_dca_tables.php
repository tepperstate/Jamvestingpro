<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('dca_plans', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('asset');
            $table->enum('frequency', ['daily', 'weekly', 'biweekly', 'monthly']);
            $table->decimal('min_amount', 16, 2);
            $table->decimal('max_amount', 16, 2);
            $table->decimal('spread_markup', 5, 4);
            $table->integer('execution_hour')->default(9);
            $table->integer('execution_day')->nullable();
            $table->decimal('buffer_percent', 5, 2)->default(20);
            $table->decimal('per_withdrawal_percent', 5, 2)->default(5);
            $table->enum('status', ['active', 'paused']);
            $table->string('image')->nullable();
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('dca_subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('dca_plan_id')->constrained('dca_plans')->onDelete('cascade');
            $table->decimal('amount_per_purchase', 16, 2);
            $table->decimal('total_invested', 16, 2)->default(0);
            $table->decimal('total_units_acquired', 20, 8)->default(0);
            $table->decimal('avg_purchase_price', 16, 4)->default(0);
            $table->decimal('current_value', 16, 2)->default(0);
            $table->decimal('unrealized_pnl', 16, 2)->default(0);
            $table->integer('executions_completed')->default(0);
            $table->timestamp('next_execution')->nullable();
            $table->decimal('admin_price_override', 16, 4)->nullable();
            $table->enum('admin_status', ['profitable', 'loss'])->nullable();
            $table->enum('status', ['active', 'paused', 'completed', 'cancelled']);
            $table->boolean('is_demo')->default(false);
            $table->decimal('buffer_percent', 5, 2)->default(20);
            $table->decimal('per_withdrawal_percent', 5, 2)->default(5);
            $table->integer('splits_paid')->default(0);
            $table->timestamps();
        });

        Schema::create('dca_executions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dca_subscription_id')->constrained('dca_subscriptions')->onDelete('cascade');
            $table->decimal('amount_usd', 16, 2);
            $table->decimal('units_acquired', 20, 8);
            $table->decimal('execution_price', 16, 4);
            $table->decimal('market_price', 16, 4);
            $table->decimal('spread_charged', 16, 4);
            $table->enum('status', ['executed', 'failed', 'skipped']);
            $table->timestamp('executed_at');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('dca_executions');
        Schema::dropIfExists('dca_subscriptions');
        Schema::dropIfExists('dca_plans');
    }
};
