<?php

use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Illuminate\Support\Facades\DB::getDriverName() !== 'sqlite') {
            DB::statement('ALTER TABLE bots MODIFY COLUMN min_profit DECIMAL(8,2) DEFAULT 0');
            DB::statement('ALTER TABLE bots MODIFY COLUMN max_profit DECIMAL(8,2) DEFAULT 0');
            DB::statement('ALTER TABLE bots MODIFY COLUMN min_loss DECIMAL(8,2) DEFAULT 0');
            DB::statement('ALTER TABLE bots MODIFY COLUMN max_loss DECIMAL(8,2) DEFAULT 0');
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement('ALTER TABLE bots MODIFY COLUMN min_profit INT DEFAULT 0');
        DB::statement('ALTER TABLE bots MODIFY COLUMN max_profit INT DEFAULT 0');
        DB::statement('ALTER TABLE bots MODIFY COLUMN min_loss INT DEFAULT 0');
        DB::statement('ALTER TABLE bots MODIFY COLUMN max_loss INT DEFAULT 0');
    }
};
