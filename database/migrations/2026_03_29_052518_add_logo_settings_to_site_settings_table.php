<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('site_settings', function (Blueprint $table) {
            $table->string('twelve_data_api_key')->nullable()->after('coingecko_api_key');
            $table->string('polygon_api_key')->nullable()->after('twelve_data_api_key');
            $table->boolean('auto_sync_logos')->default(1)->after('polygon_api_key');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('site_settings', function (Blueprint $table) {
            $table->dropColumn(['twelve_data_api_key', 'polygon_api_key', 'auto_sync_logos']);
        });
    }
};
