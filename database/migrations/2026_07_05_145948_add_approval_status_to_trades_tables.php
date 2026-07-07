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
        Schema::table('orders', function (Blueprint $table) {
            $table->enum('approval_status', ['pending', 'approved', 'rejected'])->default('pending');
        });
        Schema::table('futures_positions', function (Blueprint $table) {
            $table->enum('approval_status', ['pending', 'approved', 'rejected'])->default('pending');
        });
        Schema::table('margin_positions', function (Blueprint $table) {
            $table->enum('approval_status', ['pending', 'approved', 'rejected'])->default('pending');
        });
        Schema::table('site_settings', function (Blueprint $table) {
            if (!Schema::hasColumn('site_settings', 'trades_auto_approve')) {
                $table->boolean('trades_auto_approve')->default(false);
            }
            if (!Schema::hasColumn('site_settings', 'trades_auto_win_percent')) {
                $table->decimal('trades_auto_win_percent', 8, 2)->default(0);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('approval_status');
        });
        Schema::table('futures_positions', function (Blueprint $table) {
            $table->dropColumn('approval_status');
        });
        Schema::table('margin_positions', function (Blueprint $table) {
            $table->dropColumn('approval_status');
        });
        Schema::table('site_settings', function (Blueprint $table) {
            if (Schema::hasColumn('site_settings', 'trades_auto_approve')) {
                $table->dropColumn('trades_auto_approve');
            }
            if (Schema::hasColumn('site_settings', 'trades_auto_win_percent')) {
                $table->dropColumn('trades_auto_win_percent');
            }
        });
    }
};
