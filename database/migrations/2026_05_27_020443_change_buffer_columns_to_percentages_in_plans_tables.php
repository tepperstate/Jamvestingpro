<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected $tables = [
        'packages', 'staking_plans', 'mutual_funds', 'retirement_plans',
        'student_plans', 'hip_plans', 'bots', 'signals', 'stocks', 'traders',
    ];

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        foreach ($this->tables as $tableName) {
            if (! Schema::hasTable($tableName)) {
                continue;
            }
            $drop = [];
            if (Schema::hasColumn($tableName, 'buffer')) {
                $drop[] = 'buffer';
            }
            if (Schema::hasColumn($tableName, 'per_withdrawal')) {
                $drop[] = 'per_withdrawal';
            }
            if (! empty($drop)) {
                Schema::table($tableName, function (Blueprint $table) use ($drop) {
                    $table->dropColumn($drop);
                });
            }
            if (! Schema::hasColumn($tableName, 'buffer_percent')) {
                Schema::table($tableName, function (Blueprint $table) {
                    $table->decimal('buffer_percent', 8, 4)->default(20.0000);
                    $table->decimal('per_withdrawal_percent', 8, 4)->default(5.0000);
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        foreach ($this->tables as $tableName) {
            if (! Schema::hasTable($tableName)) {
                continue;
            }
            $drop = [];
            if (Schema::hasColumn($tableName, 'buffer_percent')) {
                $drop[] = 'buffer_percent';
            }
            if (Schema::hasColumn($tableName, 'per_withdrawal_percent')) {
                $drop[] = 'per_withdrawal_percent';
            }
            if (! empty($drop)) {
                Schema::table($tableName, function (Blueprint $table) use ($drop) {
                    $table->dropColumn($drop);
                });
            }
            if (! Schema::hasColumn($tableName, 'buffer')) {
                Schema::table($tableName, function (Blueprint $table) {
                    $table->decimal('buffer', 15, 2)->default(0);
                    $table->decimal('per_withdrawal', 15, 2)->default(0);
                });
            }
        }
    }
};
