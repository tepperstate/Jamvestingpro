<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('feed_sources', function (Blueprint $table) {
            $table->dropColumn('category_id');
            $table->string('category')->nullable()->default('General')->after('url');
            $table->timestamp('last_run')->nullable()->after('ai_model');
        });
    }

    public function down()
    {
        Schema::table('feed_sources', function (Blueprint $table) {
            $table->dropColumn(['category', 'last_run']);
            $table->unsignedBigInteger('category_id')->nullable();
        });
    }
};
