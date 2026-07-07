<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('dual_investment_products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('underlying_asset');
            $table->string('deposit_asset');
            $table->enum('direction', ['up', 'down']);
            $table->decimal('strike_price', 16, 4);
            $table->decimal('settlement_price', 16, 4)->nullable();
            $table->decimal('apy', 8, 2);
            $table->integer('duration_days');
            $table->decimal('min_amount', 16, 2);
            $table->decimal('max_amount', 16, 2);
            $table->timestamp('settlement_date');
            $table->enum('settlement_oracle', ['internal', 'binance', 'coinbase']);
            $table->decimal('buffer_percent', 5, 2)->default(20);
            $table->decimal('per_withdrawal_percent', 5, 2)->default(5);
            $table->enum('status', ['subscribing', 'active', 'settled']);
            $table->string('image')->nullable();
            $table->timestamps();
        });

        Schema::create('dual_investment_subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('dual_product_id')->constrained('dual_investment_products')->onDelete('cascade');
            $table->decimal('amount', 16, 2);
            $table->decimal('expected_return', 16, 2);
            $table->decimal('actual_return', 16, 2)->nullable();
            $table->string('settlement_asset')->nullable();
            $table->decimal('settlement_amount', 16, 8)->nullable();
            $table->enum('admin_status', ['won', 'lost'])->nullable();
            $table->enum('status', ['active', 'settled', 'cancelled']);
            $table->boolean('is_demo')->default(false);
            $table->decimal('buffer_percent', 5, 2)->default(20);
            $table->decimal('per_withdrawal_percent', 5, 2)->default(5);
            $table->integer('splits_paid')->default(0);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('dual_investment_subscriptions');
        Schema::dropIfExists('dual_investment_products');
    }
};
