<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('futures_pairs', function (Blueprint $table) {
            $table->id();
            $table->string('symbol');
            $table->string('base_asset');
            $table->string('quote_asset');
            $table->integer('max_leverage');
            $table->decimal('funding_rate', 8, 4);
            $table->decimal('mark_price', 16, 4);
            $table->decimal('index_price', 16, 4);
            $table->decimal('maintenance_margin', 5, 2);
            $table->decimal('maker_fee', 5, 4);
            $table->decimal('taker_fee', 5, 4);
            $table->decimal('insurance_fund', 16, 2);
            $table->decimal('open_interest_long', 16, 2);
            $table->decimal('open_interest_short', 16, 2);
            $table->decimal('buffer_percent', 5, 2)->default(20);
            $table->decimal('per_withdrawal_percent', 5, 2)->default(5);
            $table->enum('status', ['active', 'paused', 'delisted']);
            $table->string('image')->nullable();
            $table->timestamps();
        });

        Schema::create('futures_positions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('futures_pair_id')->constrained('futures_pairs')->onDelete('cascade');
            $table->string('trade_id')->unique();
            $table->enum('direction', ['long', 'short']);
            $table->integer('leverage');
            $table->decimal('entry_price', 16, 4);
            $table->decimal('quantity', 16, 8);
            $table->decimal('margin_amount', 16, 2);
            $table->decimal('liquidation_price', 16, 4);
            $table->decimal('take_profit', 16, 4)->nullable();
            $table->decimal('stop_loss', 16, 4)->nullable();
            $table->decimal('unrealized_pnl', 16, 2);
            $table->decimal('realized_pnl', 16, 2);
            $table->decimal('funding_paid', 16, 2);
            $table->enum('admin_status', ['win', 'loss', 'liquidated'])->nullable();
            $table->string('outcome_preset')->nullable();
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
        Schema::dropIfExists('futures_positions');
        Schema::dropIfExists('futures_pairs');
    }
};
