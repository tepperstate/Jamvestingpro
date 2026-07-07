<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('hip_plans', function (Blueprint $table) {
            $table->id();
            $table->string('vehicle_type'); // e.g. Hedge Fund Alpha
            $table->string('tier_level'); // e.g. Starter, Bronze
            $table->decimal('min_investment', 15, 2);
            $table->text('smart_logic_description')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hip_plans');
    }
};
