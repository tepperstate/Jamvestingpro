<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('swap_history', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('from_symbol', 20);
            $table->string('to_symbol', 20);
            $table->decimal('from_amount', 20, 8);
            $table->decimal('to_amount', 20, 8);
            $table->decimal('rate', 20, 8);
            $table->decimal('fee_percent', 5, 4)->default(0.0050);
            $table->string('status', 20)->default('completed');
            $table->timestamps();

            $table->index('user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('swap_history');
    }
};
