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
        Schema::table('stock_trades', function (Blueprint $table) {
            $table->string('loss_percentage')->nullable()->after('profit_percentage');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stock_trades', function (Blueprint $table) {
            $table->dropColumn('loss_percentage');
        });
    }
};
