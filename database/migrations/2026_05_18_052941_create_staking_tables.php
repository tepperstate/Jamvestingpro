<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('staking_plans', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('symbol')->default('ETH');
            $table->decimal('apy_percentage', 8, 2)->default(5.00);
            $table->decimal('min_amount', 16, 4)->default(100);
            $table->decimal('max_amount', 16, 4)->default(100000);
            $table->integer('lock_days')->default(30);
            $table->string('status')->default('active');
            $table->string('image')->nullable();
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('staking_positions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('staking_plan_id');
            $table->decimal('amount', 16, 4);
            $table->decimal('earned', 16, 4)->default(0);
            $table->date('start_date');
            $table->date('end_date');
            $table->string('status')->default('active'); // active, completed, withdrawn
            $table->boolean('is_demo')->default(false);
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('staking_plan_id')->references('id')->on('staking_plans')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('staking_positions');
        Schema::dropIfExists('staking_plans');
    }
};
