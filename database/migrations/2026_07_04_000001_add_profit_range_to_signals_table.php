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
        Schema::table('signals', function (Blueprint $table) {
            $table->decimal('profit_min_percent', 6, 2)->nullable();
            $table->decimal('profit_max_percent', 6, 2)->nullable();
            $table->decimal('profit_actual_percent', 6, 2)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('signals', function (Blueprint $table) {
            $table->dropColumn([
                'profit_min_percent',
                'profit_max_percent',
                'profit_actual_percent'
            ]);
        });
    }
};
