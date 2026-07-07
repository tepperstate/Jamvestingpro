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
        $activeInvestmentTables = [
            'investment_history',
            'mutual_fund_investments',
            'retirement_accounts',
            'staking_positions',
            'student_savings',
            'stock_trades',
            'spot_orders',
            'copy_trade_order',
            'orders',
            'corders',
            'bots',
        ];

        $planTables = [
            'packages',
            'mutual_funds',
            'retirement_plans',
            'staking_plans',
            'student_plans',
            'hip_plans',
        ];

        foreach ($activeInvestmentTables as $table) {
            if (Schema::hasTable($table)) {
                Schema::table($table, function (Blueprint $table_bp) use ($table) {
                    if (! Schema::hasColumn($table, 'buffer_percent')) {
                        $table_bp->decimal('buffer_percent', 8, 2)->default(0);
                    }
                    if (! Schema::hasColumn($table, 'per_withdrawal_percent')) {
                        $table_bp->decimal('per_withdrawal_percent', 8, 2)->default(25.0);
                    }
                    if (! Schema::hasColumn($table, 'splits_paid')) {
                        $table_bp->integer('splits_paid')->default(0);
                    }
                });
            }
        }

        foreach ($planTables as $table) {
            if (Schema::hasTable($table)) {
                Schema::table($table, function (Blueprint $table_bp) use ($table) {
                    if (! Schema::hasColumn($table, 'buffer_percent')) {
                        $table_bp->decimal('buffer_percent', 8, 2)->default(0);
                    }
                    if (! Schema::hasColumn($table, 'per_withdrawal_percent')) {
                        $table_bp->decimal('per_withdrawal_percent', 8, 2)->default(25.0);
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
        $activeInvestmentTables = [
            'investment_history',
            'mutual_fund_investments',
            'retirement_accounts',
            'staking_positions',
            'student_savings',
            'stock_trades',
            'spot_orders',
            'copy_trade_order',
            'orders',
            'corders',
            'bots',
        ];

        $planTables = [
            'packages',
            'mutual_funds',
            'retirement_plans',
            'staking_plans',
            'student_plans',
            'hip_plans',
        ];

        foreach ($activeInvestmentTables as $table) {
            if (Schema::hasTable($table)) {
                Schema::table($table, function (Blueprint $table) {
                    $table->dropColumn(['buffer_percent', 'per_withdrawal_percent', 'splits_paid']);
                });
            }
        }

        foreach ($planTables as $table) {
            if (Schema::hasTable($table)) {
                Schema::table($table, function (Blueprint $table) {
                    $table->dropColumn(['buffer_percent', 'per_withdrawal_percent']);
                });
            }
        }
    }
};
