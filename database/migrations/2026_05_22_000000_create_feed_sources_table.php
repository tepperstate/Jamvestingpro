<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('feed_sources', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('url');
            $table->unsignedBigInteger('category_id')->nullable();
            $table->unsignedBigInteger('author_id')->nullable();
            $table->boolean('active')->default(true);
            $table->string('cron_schedule')->default('hourly');
            $table->integer('import_limit')->default(5);
            $table->json('filters')->nullable();
            $table->text('ai_prompt')->nullable();
            $table->string('translation_lang')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('feed_sources');
    }
};
