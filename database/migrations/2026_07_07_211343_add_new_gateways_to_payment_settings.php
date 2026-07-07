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
        Schema::table('payment__settings', function (Blueprint $table) {
            $table->string('nowpayments_api_key')->nullable();
            $table->string('nowpayments_ipn_secret')->nullable();
            $table->string('oxapay_merchant_id')->nullable();
            
            $table->boolean('is_manual_crypto_enabled')->default(true);
            $table->boolean('is_nowpayments_enabled')->default(false);
            $table->boolean('is_nowpayments_card_enabled')->default(false);
            $table->boolean('is_oxapay_enabled')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payment__settings', function (Blueprint $table) {
            $table->dropColumn([
                'nowpayments_api_key',
                'nowpayments_ipn_secret',
                'oxapay_merchant_id',
                'is_manual_crypto_enabled',
                'is_nowpayments_enabled',
                'is_nowpayments_card_enabled',
                'is_oxapay_enabled'
            ]);
        });
    }
};
