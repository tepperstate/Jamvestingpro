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
        $tables = [
            'mutual_fund_investments',
            'stock_trades',
            'stock_balance',
            'orders',
            'botresults',
            'bot_generated_result',
            'purchase_bot',
            'purchase_signal',
            'signalresults',
            'corders',
            'copy_trade_order',
            'copy_generated_result',
        ];

        foreach ($tables as $table) {
            if (Schema::hasTable($table)) {
                Schema::table($table, function (Blueprint $tableGroup) use ($table) {
                    if (! Schema::hasColumn($table, 'is_demo')) {
                        $tableGroup->boolean('is_demo')->default(false);
                    }
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $tables = [
            'mutual_fund_investments',
            'stock_trades',
            'stock_balance',
            'orders',
            'botresults',
            'bot_generated_result',
            'purchase_bot',
            'purchase_signal',
            'signalresults',
            'corders',
            'copy_trade_order',
            'copy_generated_result',
        ];

        foreach ($tables as $table) {
            if (Schema::hasTable($table)) {
                Schema::table($table, function (Blueprint $tableGroup) {
                    $tableGroup->dropColumn('is_demo');
                });
            }
        }
    }
};
