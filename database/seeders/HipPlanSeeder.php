<?php

namespace Database\Seeders;

use App\Models\HipPlan;
use Illuminate\Database\Seeder;

class HipPlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $vehicles = [
            'Hedge Fund Alpha' => [
                'description' => 'Smart Rebalancing: Automatically rotates capital between long-short equity and macro strategies based on VIX volatility thresholds.',
                'tiers' => [
                    'Starter' => 25000,
                    'Bronze' => 100000,
                    'Silver' => 500000,
                    'Gold' => 1000000,
                    'Platinum' => 5000000,
                    'Sovereign' => 25000000,
                ],
            ],
            'ETFs (Primary Market)' => [
                'description' => 'Direct Creation: Allows the user to bypass secondary market spreads. Smart logic aggregates small orders into "Creation Units".',
                'tiers' => [
                    'Starter' => 50000,
                    'Bronze' => 250000,
                    'Silver' => 500000,
                    'Gold' => 2500000,
                    'Platinum' => 5000000,
                    'Sovereign' => 50000000,
                ],
            ],
            'Quant Trading' => [
                'description' => 'High-Freq Execution: Access to Latency-Sensitive nodes executing trades across multiple DEXs and CEXs simultaneously.',
                'tiers' => [
                    'Starter' => 10000,
                    'Bronze' => 50000,
                    'Silver' => 250000,
                    'Gold' => 1000000,
                    'Platinum' => 5000000,
                    'Sovereign' => 10000000,
                ],
            ],
            'Crypto Staking' => [
                'description' => 'Auto-Compounding: Harvests rewards daily and re-stakes them into the highest APY validator currently active on the network.',
                'tiers' => [
                    'Starter' => 5000,
                    'Bronze' => 25000,
                    'Silver' => 100000,
                    'Gold' => 500000,
                    'Platinum' => 2000000,
                    'Sovereign' => 10000000,
                ],
            ],
            'Retirement (401k/Student)' => [
                'description' => 'Tax-Loss Harvesting: Automatically sells losing positions at the end of the fiscal year to offset gains, optimized for tax-advantaged structures.',
                'tiers' => [
                    'Starter' => 1000,
                    'Bronze' => 10000,
                    'Silver' => 50000,
                    'Gold' => 250000,
                    'Platinum' => 1000000,
                    'Sovereign' => 5000000,
                ],
            ],
        ];

        foreach ($vehicles as $vehicleType => $data) {
            foreach ($data['tiers'] as $tierLevel => $minInvestment) {
                HipPlan::create([
                    'vehicle_type' => $vehicleType,
                    'tier_level' => $tierLevel,
                    'min_investment' => $minInvestment,
                    'smart_logic_description' => $data['description'],
                ]);
            }
        }
    }
}
