<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('site_settings', function (Blueprint $table) {
            $table->string('logo')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->text('address')->nullable();
            $table->text('meta')->nullable();
            $table->text('video')->nullable();
            $table->text('withdrawal_message')->nullable();
            $table->boolean('login')->default(1);
            $table->boolean('bank')->default(1);
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
                'logo', 'email', 'phone', 'address', 'meta', 'video', 'withdrawal_message', 'login', 'bank'
            ]);
        });
    }
};
