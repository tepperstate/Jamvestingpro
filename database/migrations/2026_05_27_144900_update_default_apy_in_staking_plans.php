<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Update the default value for the column
        Schema::table('staking_plans', function (Blueprint $table) {
            $table->decimal('apy_percentage', 8, 2)->default(15.00)->change();
        });

        // Update existing staking plans based on lock duration
        DB::table('staking_plans')->where('lock_days', '<=', 30)->update(['apy_percentage' => 12.00]);
        DB::table('staking_plans')->where('lock_days', '>', 30)->where('lock_days', '<=', 90)->update(['apy_percentage' => 18.00]);
        DB::table('staking_plans')->where('lock_days', '>', 90)->update(['apy_percentage' => 25.00]);
    }

    public function down(): void
    {
        Schema::table('staking_plans', function (Blueprint $table) {
            $table->decimal('apy_percentage', 8, 2)->default(5.00)->change();
        });
    }
};
