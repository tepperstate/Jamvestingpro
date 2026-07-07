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
        Schema::table('spot_orders', function (Blueprint $table) {
            if (! Schema::hasColumn('spot_orders', 'order_type')) {
                $table->enum('order_type', ['market', 'limit', 'stop_loss', 'take_profit', 'oco', 'trailing_stop'])->default('market')->after('type');
            }
            if (! Schema::hasColumn('spot_orders', 'stop_price')) {
                $table->decimal('stop_price', 16, 4)->nullable()->after('price');
            }
            if (! Schema::hasColumn('spot_orders', 'limit_price')) {
                $table->decimal('limit_price', 16, 4)->nullable()->after('stop_price');
            }
            if (! Schema::hasColumn('spot_orders', 'trailing_delta')) {
                $table->decimal('trailing_delta', 8, 4)->nullable()->after('limit_price');
            }
            if (! Schema::hasColumn('spot_orders', 'admin_hit_wick')) {
                $table->boolean('admin_hit_wick')->default(false)->after('admin_loss_override');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('spot_orders', function (Blueprint $table) {
            $table->dropColumn(['order_type', 'stop_price', 'limit_price', 'trailing_delta', 'admin_hit_wick']);
        });
    }
};
