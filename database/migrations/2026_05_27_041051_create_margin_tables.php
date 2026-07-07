<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('margin_pairs', function (Blueprint $table) {
            $table->id();
            $table->string('symbol');
            $table->integer('max_leverage');
            $table->decimal('borrow_rate_hourly', 8, 6);
            $table->decimal('maintenance_margin', 5, 2);
            $table->decimal('max_borrow', 16, 2);
            $table->decimal('collateral_factor', 5, 2);
            $table->decimal('mark_price', 16, 4);
            $table->decimal('buffer_percent', 5, 2)->default(20);
            $table->decimal('per_withdrawal_percent', 5, 2)->default(5);
            $table->enum('status', ['active', 'paused']);
            $table->timestamps();
        });

        Schema::create('margin_positions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('margin_pair_id')->constrained('margin_pairs')->onDelete('cascade');
            $table->string('trade_id')->unique();
            $table->enum('direction', ['long', 'short']);
            $table->integer('leverage');
            $table->decimal('collateral', 16, 2);
            $table->decimal('borrowed', 16, 2);
            $table->decimal('entry_price', 16, 4);
            $table->decimal('quantity', 16, 8);
            $table->decimal('interest_accrued', 16, 4);
            $table->decimal('unrealized_pnl', 16, 2);
            $table->decimal('realized_pnl', 16, 2);
            $table->decimal('liquidation_price', 16, 4);
            $table->decimal('margin_ratio', 8, 4);
            $table->enum('admin_status', ['win', 'loss', 'liquidated'])->nullable();
            $table->enum('status', ['open', 'closed', 'liquidated']);
            $table->timestamp('expire_date')->nullable();
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
        Schema::dropIfExists('margin_positions');
        Schema::dropIfExists('margin_pairs');
    }
};
