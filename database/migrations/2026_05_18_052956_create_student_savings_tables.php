<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('student_plans', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->tinyInteger('tier')->default(1); // 1-4
            $table->decimal('min_amount', 16, 4)->default(50);
            $table->decimal('max_amount', 16, 4)->default(500);
            $table->decimal('interest_rate', 8, 2)->default(3.00);
            $table->integer('duration_months')->default(6);
            $table->json('features')->nullable();
            $table->string('status')->default('active');
            $table->string('image')->nullable();
            $table->timestamps();
        });

        Schema::create('student_savings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('student_plan_id');
            $table->decimal('amount', 16, 4);
            $table->decimal('earned', 16, 4)->default(0);
            $table->date('start_date');
            $table->date('maturity_date');
            $table->string('status')->default('active'); // active, matured, withdrawn
            $table->boolean('is_demo')->default(false);
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('student_plan_id')->references('id')->on('student_plans')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_savings');
        Schema::dropIfExists('student_plans');
    }
};
