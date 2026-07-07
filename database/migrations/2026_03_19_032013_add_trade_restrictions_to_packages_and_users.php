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
        Schema::table('packages', function (Blueprint $table) {
            if (! Schema::hasColumn('packages', 'daily_trade')) {
                $table->integer('daily_trade')->default(0)->after('trade');
            }
            if (! Schema::hasColumn('packages', 'weekly_trade')) {
                $table->integer('weekly_trade')->default(0)->after('daily_trade');
            }
        });

        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'daily_trade')) {
                $table->integer('daily_trade')->default(0)->after('trades');
            }
            if (! Schema::hasColumn('users', 'weekly_trade')) {
                $table->integer('weekly_trade')->default(0)->after('daily_trade');
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
        Schema::table('packages', function (Blueprint $table) {
            $table->dropColumn(['daily_trade', 'weekly_trade']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['daily_trade', 'weekly_trade']);
        });
    }
};
