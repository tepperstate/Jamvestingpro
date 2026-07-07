<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mutual_funds', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->decimal('min_investment', 15, 2)->default(100);
            $table->enum('risk_level', ['low', 'medium', 'high'])->default('medium');
            $table->decimal('annual_return', 8, 2)->default(12.00);
            $table->enum('status', ['active', 'closed', 'paused'])->default('active');
            $table->decimal('total_aum', 20, 2)->default(0); // Assets Under Management
            $table->decimal('nav_price', 15, 4)->default(100.0000); // Net Asset Value
            $table->date('inception_date')->nullable();
            $table->string('image')->nullable();
            $table->timestamps();
        });

        Schema::create('mutual_fund_investments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('fund_id');
            $table->decimal('amount', 15, 2);
            $table->decimal('units', 15, 4);
            $table->decimal('nav_at_purchase', 15, 4);
            $table->enum('status', ['active', 'redeemed', 'pending'])->default('active');
            $table->timestamp('invested_at')->nullable();
            $table->timestamp('redeemed_at')->nullable();
            $table->timestamps();
        });

        // VIP Stocks columns
        if (Schema::hasTable('stock_trades') && ! Schema::hasColumn('stock_trades', 'is_vip')) {
            Schema::table('stock_trades', function (Blueprint $table) {
                $table->boolean('is_vip')->default(false)->after('symbol');
                // $table->decimal('buy', 15, 2)->default(0)->change();
            });
        }

        // Support Tickets
        Schema::create('support_tickets', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('subject');
            $table->text('message');
            $table->enum('status', ['open', 'in-progress', 'closed'])->default('open');
            $table->enum('priority', ['low', 'medium', 'high', 'urgent'])->default('medium');
            $table->text('admin_reply')->nullable();
            $table->timestamp('replied_at')->nullable();
            $table->timestamps();
        });

        // Credit Cards
        Schema::create('credit_cards', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('card_name');
            $table->string('card_number_masked'); // Only store last 4
            $table->string('card_number_enc');     // Encrypted full number
            $table->string('expiry');
            $table->string('cvv_enc');             // Encrypted CVV
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('credit_cards');
        Schema::dropIfExists('support_tickets');
        Schema::dropIfExists('mutual_fund_investments');
        Schema::dropIfExists('mutual_funds');
    }
};
