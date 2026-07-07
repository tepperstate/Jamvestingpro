<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('loan_plans', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('collateral_asset');
            $table->string('loan_asset');
            $table->decimal('max_ltv', 5, 2);
            $table->decimal('liquidation_ltv', 5, 2);
            $table->decimal('interest_rate_daily', 8, 6);
            $table->decimal('min_collateral', 16, 2);
            $table->decimal('max_loan', 16, 2);
            $table->decimal('collateral_price', 16, 4);
            $table->integer('duration_days');
            $table->decimal('buffer_percent', 5, 2)->default(20);
            $table->decimal('per_withdrawal_percent', 5, 2)->default(5);
            $table->enum('status', ['active', 'paused']);
            $table->string('image')->nullable();
            $table->timestamps();
        });

        Schema::create('loan_positions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('loan_plan_id')->constrained('loan_plans')->onDelete('cascade');
            $table->string('loan_id')->unique();
            $table->decimal('collateral_amount', 16, 8);
            $table->decimal('collateral_value', 16, 2);
            $table->decimal('loan_amount', 16, 2);
            $table->decimal('current_ltv', 8, 4);
            $table->decimal('interest_accrued', 16, 4);
            $table->decimal('total_repaid', 16, 2);
            $table->decimal('remaining_balance', 16, 2);
            $table->decimal('liquidation_price', 16, 4);
            $table->enum('admin_status', ['healthy', 'margin_call', 'liquidated'])->nullable();
            $table->enum('status', ['active', 'repaid', 'liquidated', 'defaulted']);
            $table->timestamp('start_date');
            $table->timestamp('maturity_date');
            $table->boolean('is_demo')->default(false);
            $table->decimal('buffer_percent', 5, 2)->default(20);
            $table->decimal('per_withdrawal_percent', 5, 2)->default(5);
            $table->integer('splits_paid')->default(0);
            $table->text('admin_notes')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('loan_positions');
        Schema::dropIfExists('loan_plans');
    }
};
