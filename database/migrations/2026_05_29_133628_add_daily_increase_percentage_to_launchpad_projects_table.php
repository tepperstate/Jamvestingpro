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
        Schema::table('launchpad_projects', function (Blueprint $table) {
            $table->decimal('daily_increase_percentage', 5, 2)->default(0)->after('price_per_token')->comment('Admin controlled percentage increase per day');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('launchpad_projects', function (Blueprint $table) {
            $table->dropColumn('daily_increase_percentage');
        });
    }
};
