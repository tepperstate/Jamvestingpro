<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('spot_orders', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('symbol');
            $table->enum('type', ['buy', 'sell']);
            $table->decimal('amount', 16, 4); // quantity
            $table->decimal('price', 16, 4);  // price per unit
            $table->decimal('total_usd', 16, 4); // total USD value
            $table->string('status')->default('pending'); // pending, approved, rejected, filled
            $table->text('admin_notes')->nullable();
            $table->decimal('admin_profit_override', 16, 4)->nullable();
            $table->decimal('admin_loss_override', 16, 4)->nullable();
            $table->unsignedBigInteger('approved_by')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->boolean('is_demo')->default(false);
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->index(['status', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('spot_orders');
    }
};
