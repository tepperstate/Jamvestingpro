<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('site_settings', function (Blueprint $table) {
            if (! Schema::hasColumn('site_settings', 'maker_fee')) {
                $table->decimal('maker_fee', 8, 4)->default(0.0010); // 0.1% default
            }
            if (! Schema::hasColumn('site_settings', 'taker_fee')) {
                $table->decimal('taker_fee', 8, 4)->default(0.0020); // 0.2% default
            }
            if (! Schema::hasColumn('site_settings', 'withdrawal_fee')) {
                $table->decimal('withdrawal_fee', 8, 4)->default(5.0000); // fixed amount or percentage depending on interpretation, let's just make it a general fee
            }
        });
    }

    public function down(): void
    {
        Schema::table('site_settings', function (Blueprint $table) {
            $table->dropColumn(['maker_fee', 'taker_fee', 'withdrawal_fee']);
        });
    }
};
