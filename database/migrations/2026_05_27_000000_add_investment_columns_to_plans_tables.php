<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        $tables = [
            'packages', 'staking_plans', 'mutual_funds', 'retirement_plans',
            'student_plans', 'hip_plans', 'bots', 'signals', 'stocks', 'traders',
        ];

        foreach ($tables as $tableName) {
            if (Schema::hasTable($tableName)) {
                Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                    if (! Schema::hasColumn($tableName, 'buffer')) {
                        $table->decimal('buffer', 15, 2)->nullable();
                        $table->decimal('per_withdrawal', 15, 2)->nullable();
                        $table->integer('lock_period_days')->nullable();
                        $table->decimal('daily_roi_percent', 8, 4)->nullable();
                        $table->decimal('actual_invested', 15, 2)->nullable();
                        $table->string('projected_return')->nullable();
                        $table->decimal('total_deposit_dashboard', 15, 2)->nullable();
                    }
                });
            }
        }
    }

    public function down()
    {
        $tables = [
            'packages', 'staking_plans', 'mutual_funds', 'retirement_plans',
            'student_plans', 'hip_plans', 'bots', 'signals', 'stocks', 'traders',
        ];

        foreach ($tables as $tableName) {
            if (Schema::hasTable($tableName)) {
                Schema::table($tableName, function (Blueprint $table) {
                    $table->dropColumn([
                        'buffer', 'per_withdrawal', 'lock_period_days',
                        'daily_roi_percent', 'actual_invested', 'projected_return', 'total_deposit_dashboard',
                    ]);
                });
            }
        }
    }
};
