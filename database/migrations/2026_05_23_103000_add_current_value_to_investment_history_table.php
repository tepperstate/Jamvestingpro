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
        Schema::table('investment_history', function (Blueprint $table) {
            if (! Schema::hasColumn('investment_history', 'current_value')) {
                $table->decimal('current_value', 16, 2)->nullable()->after('amount');
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
        Schema::table('investment_history', function (Blueprint $table) {
            if (Schema::hasColumn('investment_history', 'current_value')) {
                $table->dropColumn('current_value');
            }
        });
    }
};
