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
        // Drop the old tables
        Schema::dropIfExists('onboarding_responses');
        Schema::dropIfExists('onboarding_questions');

        // Recreate onboarding_questions with the new Soft Gate schema
        Schema::create('onboarding_questions', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('subtitle')->nullable();
            $table->string('input_type')->default('radio'); // radio, select, text
            $table->string('depends_on')->nullable(); // e.g. "q1_institutional"
            $table->boolean('is_required')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        // Create onboarding_options table
        Schema::create('onboarding_options', function (Blueprint $table) {
            $table->id();
            $table->foreignId('question_id')->constrained('onboarding_questions')->onDelete('cascade');
            $table->string('label');
            $table->string('value');
            $table->string('icon')->nullable();
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        // Create user_onboarding_responses table
        Schema::create('user_onboarding_responses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('question_id')->constrained('onboarding_questions')->onDelete('cascade');
            $table->string('response_value');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_onboarding_responses');
        Schema::dropIfExists('onboarding_options');
        Schema::dropIfExists('onboarding_questions');
    }
};
