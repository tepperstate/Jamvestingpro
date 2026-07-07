<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('spot_orders', function (Blueprint $table) {
            $table->string('margin_mode')->default('spot')->after('is_demo');      // spot | cross | isolated
            $table->unsignedTinyInteger('leverage')->default(1)->after('margin_mode'); // 1, 3, 10
            $table->string('order_type')->default('limit')->after('leverage');      // limit | market | stop-limit
            $table->decimal('trigger_price', 20, 8)->nullable()->after('order_type'); // For stop-limit orders
        });
    }

    public function down(): void
    {
        Schema::table('spot_orders', function (Blueprint $table) {
            $table->dropColumn(['margin_mode', 'leverage', 'order_type', 'trigger_price']);
        });
    }
};
