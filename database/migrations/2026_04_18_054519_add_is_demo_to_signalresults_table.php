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
        if (! Schema::hasColumn('signalresults', 'is_demo')) {
            Schema::table('signalresults', function (Blueprint $table) {
                $table->boolean('is_demo')->default(false)->after('win');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('signalresults', function (Blueprint $table) {
            $table->dropColumn('is_demo');
        });
    }
};
