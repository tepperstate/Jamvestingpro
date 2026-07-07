<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('assets', function (Blueprint $table) {
            $table->string('mirror_symbol')->nullable()->after('symbols');
        });

        Schema::table('futures_pairs', function (Blueprint $table) {
            $table->string('mirror_symbol')->nullable()->after('symbol');
        });

        Schema::table('margin_pairs', function (Blueprint $table) {
            $table->string('mirror_symbol')->nullable()->after('symbol');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('assets', function (Blueprint $table) {
            $table->dropColumn('mirror_symbol');
        });

        Schema::table('futures_pairs', function (Blueprint $table) {
            $table->dropColumn('mirror_symbol');
        });

        Schema::table('margin_pairs', function (Blueprint $table) {
            $table->dropColumn('mirror_symbol');
        });
    }
};
