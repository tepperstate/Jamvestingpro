<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('admin_wallets', function (Blueprint $blueprint) {
            $blueprint->id();
            $blueprint->string('name');
            $blueprint->string('symbol');
            $blueprint->string('address');
            $blueprint->string('network')->nullable();
            $blueprint->string('image')->nullable();
            $blueprint->boolean('is_active')->default(true);
            $blueprint->string('icon_class')->nullable(); // Lucide or Remix icon name
            $blueprint->timestamps();
        });

        // Seed initial data from fragmented tables
        try {
            $btc = DB::table('manuel_deposit')->where('id', 1)->first();
            if ($btc) {
                DB::table('admin_wallets')->insert([
                    'name' => 'Bitcoin',
                    'symbol' => 'BTC',
                    'address' => $btc->address ?? '',
                    'network' => 'BTC Network',
                    'icon_class' => 'ri-bit-coin-line',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            $eth = DB::table('manuel_deposit_eth')->where('id', 1)->first();
            if ($eth) {
                DB::table('admin_wallets')->insert([
                    'name' => 'Ethereum',
                    'symbol' => 'ETH',
                    'address' => $eth->address ?? '',
                    'network' => 'ERC-20 Network',
                    'icon_class' => 'ri-copper-diamond-line',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            $usd = DB::table('manuel_deposit_usd')->where('id', 1)->first();
            if ($usd) {
                DB::table('admin_wallets')->insert([
                    'name' => 'USDT',
                    'symbol' => 'USDT',
                    'address' => $usd->address ?? '',
                    'network' => 'TRC-20 Network',
                    'icon_class' => 'ri-hand-coin-line',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        } catch (Exception $e) {
            // Log or ignore if tables don't exist yet during a fresh migration
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admin_wallets');
    }
};
