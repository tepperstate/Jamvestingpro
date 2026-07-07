<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('site_settings', function (Blueprint $table) {
            $table->string('smtp_host')->nullable();
            $table->string('smtp_port')->nullable()->after('smtp_host');
            $table->string('smtp_user')->nullable()->after('smtp_port');
            $table->string('smtp_pass')->nullable()->after('smtp_user');
            $table->string('smtp_encryption')->nullable()->after('smtp_pass');
            $table->string('mail_from_address')->nullable()->after('smtp_encryption');
            $table->boolean('app_debug')->default(0)->after('mail_from_address');
            $table->string('app_url')->nullable()->after('app_debug');
            $table->string('pusher_app_id')->nullable()->after('app_url');
            $table->string('pusher_app_key')->nullable()->after('pusher_app_id');
            $table->string('pusher_app_secret')->nullable()->after('pusher_app_key');
            $table->string('pusher_app_cluster')->nullable()->after('pusher_app_secret');
            $table->string('alphavantage_api_key')->nullable()->after('pusher_app_cluster');
            $table->string('finnhub_api_key')->nullable()->after('alphavantage_api_key');
            $table->string('binance_api_key')->nullable()->after('finnhub_api_key');
            $table->string('binance_api_secret')->nullable()->after('binance_api_key');
            $table->string('coingecko_api_key')->nullable()->after('binance_api_secret');
        });
    }

    public function down()
    {
        Schema::table('site_settings', function (Blueprint $table) {
            $table->dropColumn([
                'smtp_host',
                'smtp_port',
                'smtp_user',
                'smtp_pass',
                'smtp_encryption',
                'mail_from_address',
                'app_debug',
                'app_url',
                'pusher_app_id',
                'pusher_app_key',
                'pusher_app_secret',
                'pusher_app_cluster',
                'alphavantage_api_key',
                'finnhub_api_key',
                'binance_api_key',
                'binance_api_secret',
                'coingecko_api_key',
            ]);
        });
    }
};
