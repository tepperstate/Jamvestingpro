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
        Schema::table('botresults', function (Blueprint $table) {
            $table->boolean('is_auto_renew')->default(false);
        });

        Schema::table('copy_trade_orders', function (Blueprint $table) {
            $table->boolean('is_auto_renew')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('botresults', function (Blueprint $table) {
            $table->dropColumn('is_auto_renew');
        });

        Schema::table('copy_trade_orders', function (Blueprint $table) {
            $table->dropColumn('is_auto_renew');
        });
    }
};
