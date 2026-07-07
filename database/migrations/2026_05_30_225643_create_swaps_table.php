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
        Schema::create('swaps', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('from_currency');
            $table->string('to_currency');
            $table->decimal('from_amount', 24, 8);
            $table->decimal('to_amount', 24, 8);
            $table->decimal('exchange_rate', 24, 8);
            $table->decimal('fee_amount', 24, 8)->default(0);
            $table->string('status')->default('completed'); // 'pending', 'completed', 'failed'
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
        Schema::dropIfExists('swaps');
    }
};
