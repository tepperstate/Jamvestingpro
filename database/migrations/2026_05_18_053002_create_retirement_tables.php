<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('retirement_plans', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->tinyInteger('tier')->default(1); // 1-4
            $table->decimal('employer_match_pct', 8, 2)->default(3.00);
            $table->string('vesting_schedule')->default('3-year'); // immediate, 2-year, 3-year
            $table->decimal('min_contribution', 16, 4)->default(100);
            $table->decimal('max_contribution', 16, 4)->default(23000);
            $table->json('features')->nullable();
            $table->string('status')->default('active');
            $table->string('image')->nullable();
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('retirement_accounts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('retirement_plan_id');
            $table->decimal('balance', 16, 4)->default(0);
            $table->decimal('employer_contributions', 16, 4)->default(0);
            $table->decimal('employee_contributions', 16, 4)->default(0);
            $table->decimal('vested_amount', 16, 4)->default(0);
            $table->date('start_date');
            $table->string('status')->default('active'); // active, vested, withdrawn
            $table->boolean('is_demo')->default(false);
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('retirement_plan_id')->references('id')->on('retirement_plans')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('retirement_accounts');
        Schema::dropIfExists('retirement_plans');
    }
};
