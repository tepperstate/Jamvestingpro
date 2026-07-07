<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePriceProvidersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('price_providers', function (Blueprint $blueprint) {
            $blueprint->id();
            $blueprint->string('name');
            $blueprint->string('provider_type'); // binance, polygon, coingecko, huobi, etc.
            $blueprint->string('asset_type')->default('crypto'); // crypto, forex, stock
            $blueprint->string('api_key')->nullable();
            $blueprint->string('api_secret')->nullable(); // For future use
            $blueprint->string('base_url')->nullable();
            $blueprint->integer('priority')->default(0);
            $blueprint->boolean('is_active')->default(true);
            $blueprint->timestamp('last_used_at')->nullable();
            $blueprint->string('last_status')->nullable(); // success, error, rate_limited
            $blueprint->text('last_error')->nullable();
            $blueprint->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('price_providers');
    }
}
