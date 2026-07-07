<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('stock_trades', function (Blueprint $table) {
            $table->decimal('profit_percentage', 8, 2)->default(0)->after('is_vip');
            $table->string('daily_gain', 50)->default('0.00%')->after('profit_percentage');
        });
    }

    public function down()
    {
        Schema::table('stock_trades', function (Blueprint $table) {
            $table->dropColumn(['profit_percentage', 'daily_gain']);
        });
    }
};
