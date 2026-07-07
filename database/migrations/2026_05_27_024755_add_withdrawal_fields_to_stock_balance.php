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
        Schema::table('stock_balance', function (Blueprint $table) {
            if (! Schema::hasColumn('stock_balance', 'buffer_percent')) {
                $table->decimal('buffer_percent', 8, 2)->default(0);
            }
            if (! Schema::hasColumn('stock_balance', 'per_withdrawal_percent')) {
                $table->decimal('per_withdrawal_percent', 8, 2)->default(25.0);
            }
            if (! Schema::hasColumn('stock_balance', 'splits_paid')) {
                $table->integer('splits_paid')->default(0);
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('stock_balance', function (Blueprint $table) {
            $table->dropColumn(['buffer_percent', 'per_withdrawal_percent', 'splits_paid']);
        });
    }
};
