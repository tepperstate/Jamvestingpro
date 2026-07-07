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
        Schema::table('site_settings', function (Blueprint $table) {
            $table->boolean('spot_auto_approve')->default(0);
            $table->decimal('spot_auto_win_percent', 8, 2)->default(0);
            $table->boolean('margin_auto_approve')->default(0);
            $table->decimal('margin_auto_win_percent', 8, 2)->default(0);
            $table->boolean('futures_auto_approve')->default(0);
            $table->decimal('futures_auto_win_percent', 8, 2)->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('site_settings', function (Blueprint $table) {
            $table->dropColumn([
                'spot_auto_approve', 'spot_auto_win_percent',
                'margin_auto_approve', 'margin_auto_win_percent',
                'futures_auto_approve', 'futures_auto_win_percent',
            ]);
        });
    }
};
