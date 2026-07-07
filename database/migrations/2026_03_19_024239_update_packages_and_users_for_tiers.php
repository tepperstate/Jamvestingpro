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
        if (Schema::hasTable('packages')) {
            Schema::table('packages', function (Blueprint $table) {
                if (! Schema::hasColumn('packages', 'min_deposit')) {
                    $table->decimal('min_deposit', 16, 4)->default(0)->after('amount');
                }
                if (! Schema::hasColumn('packages', 'features')) {
                    $table->json('features')->nullable()->after('min_deposit');
                }
            });
        }

        // Initialize features and min_deposit for existing packages based on implementation plan
        $mappings = [
            1 => ['name' => 'Starter', 'min' => 1000, 'features' => ['basic_trading']],
            3 => ['name' => 'Classic', 'min' => 15000, 'features' => ['basic_trading', 'high_leverage']],
            4 => ['name' => 'Pro', 'min' => 125000, 'features' => ['basic_trading', 'high_leverage', 'vip_stocks']],
            5 => ['name' => 'Elite', 'min' => 175000, 'features' => ['basic_trading', 'high_leverage', 'vip_stocks', 'mutual_funds']],
            6 => ['name' => 'Institutional', 'min' => 250000, 'features' => ['basic_trading', 'high_leverage', 'vip_stocks', 'mutual_funds', 'advanced_controls']],
        ];

        if (Schema::hasTable('packages')) {
            foreach ($mappings as $id => $data) {
                DB::table('packages')->where('id', $id)->update([
                    'name' => $data['name'],
                    'min_deposit' => $data['min'],
                    'features' => json_encode($data['features']),
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('packages', function (Blueprint $table) {
            $table->dropColumn(['min_deposit', 'features']);
        });
    }
};
