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
        Schema::table('corders', function (Blueprint $table) {
            $table->text('symbols')->nullable();
            $table->boolean('is_auto_renew')->default(false);
        });

        Schema::table('copy_trade_order', function (Blueprint $table) {
            $table->boolean('is_auto_renew')->default(false);
        });

        Schema::dropIfExists('copy_trade_orders');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('corders', function (Blueprint $table) {
            $table->dropColumn(['symbols', 'is_auto_renew']);
        });

        Schema::table('copy_trade_order', function (Blueprint $table) {
            $table->dropColumn('is_auto_renew');
        });
    }
};
