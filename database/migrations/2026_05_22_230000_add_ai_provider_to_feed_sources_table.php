<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('feed_sources', function (Blueprint $table) {
            $table->string('ai_provider')->default('gemini');
            $table->string('ai_model')->nullable();
        });
    }

    public function down()
    {
        Schema::table('feed_sources', function (Blueprint $table) {
            $table->dropColumn(['ai_provider', 'ai_model']);
        });
    }
};
