<?php

use App\Models\MutualFund;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $funds = MutualFund::all();

        foreach ($funds as $fund) {
            $name = $fund->name;
            $image = 'mutual_fund_global.png'; // Default category

            if (stripos($name, 'Index') !== false || stripos($name, '500') !== false || stripos($name, 'S&P') !== false) {
                $image = 'mutual_fund_index.png';
            } elseif (stripos($name, 'Tech') !== false || stripos($name, 'Software') !== false || stripos($name, 'Information') !== false) {
                $image = 'mutual_fund_tech.png';
            } elseif (stripos($name, 'Health') !== false || stripos($name, 'Science') !== false || stripos($name, 'Medicine') !== false) {
                $image = 'mutual_fund_health.png';
            } elseif (stripos($name, 'Real Estate') !== false || stripos($name, 'REIT') !== false) {
                $image = 'mutual_fund_real_estate.png';
            } elseif (stripos($name, 'Bond') !== false || stripos($name, 'Income') !== false || stripos($name, 'Fixed') !== false) {
                $image = 'mutual_fund_bonds.png';
            } elseif (stripos($name, 'Growth') !== false || stripos($name, 'Aggressive') !== false || stripos($name, 'Blue Chip') !== false) {
                $image = 'mutual_fund_growth.png';
            }

            $fund->update(['image' => $image]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Optional: revert to default or leave as is
    }
};
