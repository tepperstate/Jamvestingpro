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
        Schema::table('price_providers', function (Blueprint $table) {
            $table->decimal('spread_percentage', 8, 4)->default(0)->after('priority');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('price_providers', function (Blueprint $table) {
            $table->dropColumn('spread_percentage');
        });
    }
};
