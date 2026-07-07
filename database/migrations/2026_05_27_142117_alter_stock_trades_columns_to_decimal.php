<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Change int columns to decimal with enough precision for crypto prices
        // buy/sell: up to 16 digits with 8 decimal places (handles BTC at 76000 and SHIB at 0.00000569)
        // changes: percentage with 2 decimal places
        // volume: keep as bigint for large volume numbers
        if (DB::getDriverName() !== 'sqlite') {
            DB::statement('ALTER TABLE stock_trades MODIFY buy DECIMAL(20,8) NOT NULL DEFAULT 0');
            DB::statement('ALTER TABLE stock_trades MODIFY sell DECIMAL(20,8) NOT NULL DEFAULT 0');
            DB::statement('ALTER TABLE stock_trades MODIFY changes DECIMAL(10,2) NOT NULL DEFAULT 0');
            DB::statement('ALTER TABLE stock_trades MODIFY volume BIGINT NOT NULL DEFAULT 0');
        }
    }

    public function down(): void
    {
        if (DB::getDriverName() !== 'sqlite') {
            DB::statement('ALTER TABLE stock_trades MODIFY buy INT NOT NULL DEFAULT 0');
            DB::statement('ALTER TABLE stock_trades MODIFY sell INT NOT NULL DEFAULT 0');
            DB::statement('ALTER TABLE stock_trades MODIFY changes INT NOT NULL DEFAULT 0');
            DB::statement('ALTER TABLE stock_trades MODIFY volume INT NOT NULL DEFAULT 0');
        }
    }
};
