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
        Schema::table('onboarding_questions', function (Blueprint $table) {
            $table->integer('section')->default(1);
            $table->string('question_key')->nullable()->unique();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('onboarding_questions', function (Blueprint $table) {
            $table->dropColumn(['section', 'question_key']);
        });
    }
};
