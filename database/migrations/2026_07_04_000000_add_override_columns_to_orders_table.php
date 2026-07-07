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
        Schema::table('orders', function (Blueprint $table) {
            $table->boolean('is_overridden')->default(false);
            $table->unsignedBigInteger('override_by')->nullable();
            $table->text('override_reason')->nullable();
            $table->timestamp('override_timestamp')->nullable();
            $table->string('forced_outcome', 20)->nullable();
            $table->decimal('forced_percentage', 10, 2)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn([
                'is_overridden',
                'override_by',
                'override_reason',
                'override_timestamp',
                'forced_outcome',
                'forced_percentage'
            ]);
        });
    }
};
