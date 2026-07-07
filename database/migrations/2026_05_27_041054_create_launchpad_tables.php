<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('launchpad_projects', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('symbol');
            $table->text('description');
            $table->decimal('total_supply', 20, 2);
            $table->decimal('tokens_for_sale', 20, 2);
            $table->decimal('tokens_sold', 20, 2)->default(0);
            $table->decimal('price_per_token', 16, 8);
            $table->decimal('hard_cap', 16, 2);
            $table->decimal('soft_cap', 16, 2);
            $table->decimal('raised_amount', 16, 2)->default(0);
            $table->decimal('admin_allocation_pct', 5, 2);
            $table->integer('vesting_days')->default(0);
            $table->timestamp('start_date');
            $table->timestamp('end_date')->nullable();
            $table->timestamp('listing_date')->nullable();
            $table->decimal('listing_price', 16, 8)->nullable();
            $table->boolean('audit_badge')->default(false);
            $table->boolean('kyc_verified')->default(false);
            $table->string('whitepaper_url')->nullable();
            $table->string('website_url')->nullable();
            $table->decimal('buffer_percent', 5, 2)->default(20);
            $table->decimal('per_withdrawal_percent', 5, 2)->default(5);
            $table->enum('status', ['upcoming', 'active', 'completed', 'cancelled']);
            $table->string('image')->nullable();
            $table->timestamps();
        });

        Schema::create('launchpad_participations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('launchpad_project_id')->constrained('launchpad_projects')->onDelete('cascade');
            $table->decimal('amount_invested', 16, 2);
            $table->decimal('tokens_allocated', 20, 8);
            $table->decimal('tokens_claimed', 20, 8)->default(0);
            $table->decimal('current_value', 16, 2);
            $table->decimal('pnl', 16, 2);
            $table->timestamp('vesting_end_date')->nullable();
            $table->enum('admin_status', ['profitable', 'loss'])->nullable();
            $table->enum('status', ['active', 'vesting', 'claimable', 'claimed', 'cancelled']);
            $table->boolean('is_demo')->default(false);
            $table->decimal('buffer_percent', 5, 2)->default(20);
            $table->decimal('per_withdrawal_percent', 5, 2)->default(5);
            $table->integer('splits_paid')->default(0);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('launchpad_participations');
        Schema::dropIfExists('launchpad_projects');
    }
};
