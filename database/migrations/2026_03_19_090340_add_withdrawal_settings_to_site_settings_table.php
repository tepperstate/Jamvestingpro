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
        Schema::table('site_settings', function (Blueprint $table) {
            $table->boolean('withdrawal_flow_enabled')->default(0);
            $table->boolean('default_withdrawal_security')->default(0);
            $table->string('clearance_pin_name')->default('Institutional Clearance PIN');
            $table->string('tax_pin_name')->default('Regulatory Tax Authorization');
            $table->string('liquidation_pin_name')->default('Asset Liquidation Processing PIN');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('site_settings', function (Blueprint $table) {
            $table->dropColumn([
                'withdrawal_flow_enabled',
                'default_withdrawal_security',
                'clearance_pin_name',
                'tax_pin_name',
                'liquidation_pin_name',
            ]);
        });
    }
};
