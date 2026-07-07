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
        Schema::table('site_settings', function (Blueprint $row) {
            $row->string('google_client_id')->nullable();
            $row->string('google_client_secret')->nullable();
            $row->string('google_redirect_url')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('site_settings', function (Blueprint $row) {
            $row->dropColumn(['google_client_id', 'google_client_secret', 'google_redirect_url']);
        });
    }
};
