<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('crypto_payments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('gateway_name'); // oxapay, nowpayments, nowpayments_card
            $table->string('txn_id')->unique();
            $table->decimal('amount', 16, 8)->nullable();
            $table->string('currency')->nullable();
            $table->string('crypto_currency')->nullable();
            $table->string('status')->default('pending'); // pending, paid, failed
            $table->json('ipn_payload')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('crypto_payments');
    }
};
