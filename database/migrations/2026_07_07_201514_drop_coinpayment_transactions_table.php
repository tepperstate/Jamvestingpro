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
        Schema::dropIfExists('coinpayment_transactions');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::create('coinpayment_transactions', function (Blueprint $table) {
            $table->id();
            // Re-creating the schema is omitted since this was a 3rd party package table
            // and we are migrating away from it permanently.
            $table->string('payment_id')->nullable();
            $table->string('payment_address')->nullable();
            $table->string('coin')->nullable();
            $table->string('fiat')->nullable();
            $table->string('status_text')->nullable();
            $table->integer('status')->default(0);
            $table->dateTime('payment_created_at')->nullable();
            $table->dateTime('expired')->nullable();
            $table->dateTime('confirmation_at')->nullable();
            $table->double('amount', 20, 8)->nullable();
            $table->integer('confirms_needed')->nullable();
            $table->string('qrcode_url')->nullable();
            $table->string('status_url')->nullable();
            $table->json('payload')->nullable();
            $table->timestamps();
        });
    }
};
