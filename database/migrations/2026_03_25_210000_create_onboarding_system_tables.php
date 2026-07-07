<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('onboarding_questions', function (Blueprint $table) {
            $table->id();
            $table->integer('section')->default(1);
            $table->string('question_text');
            $table->string('question_key')->unique(); // Slug for identification if needed
            $table->string('input_type')->default('text'); // text, number, select
            $table->json('options')->nullable(); // For select types
            $table->boolean('is_required')->default(true);
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('onboarding_responses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('question_id')->constrained('onboarding_questions')->onDelete('cascade');
            $table->text('answer')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('onboarding_responses');
        Schema::dropIfExists('onboarding_questions');
    }
};
