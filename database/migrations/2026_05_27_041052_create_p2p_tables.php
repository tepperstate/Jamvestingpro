<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('p2p_listings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->enum('type', ['buy', 'sell']);
            $table->string('asset');
            $table->string('currency');
            $table->decimal('price', 16, 4);
            $table->decimal('amount', 16, 8);
            $table->decimal('min_order', 16, 2);
            $table->decimal('max_order', 16, 2);
            $table->json('payment_methods');
            $table->text('terms')->nullable();
            $table->decimal('completion_rate', 5, 2);
            $table->integer('total_trades');
            $table->boolean('is_admin_listing')->default(false);
            $table->enum('status', ['active', 'paused', 'completed', 'cancelled']);
            $table->decimal('buffer_percent', 5, 2)->default(20);
            $table->decimal('per_withdrawal_percent', 5, 2)->default(5);
            $table->timestamps();
        });

        Schema::create('p2p_orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_id')->unique();
            $table->foreignId('listing_id')->constrained('p2p_listings')->onDelete('cascade');
            $table->foreignId('buyer_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('seller_id')->constrained('users')->onDelete('cascade');
            $table->decimal('amount', 16, 8);
            $table->decimal('price', 16, 4);
            $table->decimal('total_fiat', 16, 2);
            $table->enum('escrow_status', ['held', 'released', 'refunded', 'disputed']);
            $table->boolean('payment_confirmed_by_buyer')->default(false);
            $table->boolean('payment_confirmed_by_seller')->default(false);
            $table->enum('admin_resolution', ['release_to_buyer', 'release_to_seller', 'cancelled'])->nullable();
            $table->text('dispute_reason')->nullable();
            $table->text('admin_notes')->nullable();
            $table->enum('status', ['pending', 'paid', 'completed', 'disputed', 'cancelled']);
            $table->boolean('is_demo')->default(false);
            $table->timestamp('expires_at');
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('p2p_orders');
        Schema::dropIfExists('p2p_listings');
    }
};
