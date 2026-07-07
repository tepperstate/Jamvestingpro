<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('feed_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('feed_source_id')->constrained()->onDelete('cascade');
            $table->string('status'); // success, failed, duplicate
            $table->text('message')->nullable();
            $table->string('article_url')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('feed_logs');
    }
};
