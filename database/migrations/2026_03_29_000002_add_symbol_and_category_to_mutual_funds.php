<?php

use App\Models\MutualFund;
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
        if (! Schema::hasColumn('mutual_funds', 'symbol')) {
            Schema::table('mutual_funds', function (Blueprint $table) {
                $table->string('symbol')->nullable()->after('name');
                $table->string('category')->nullable()->after('description');
            });
        }

        // Programmatically populate symbols and categories from existing names
        $funds = MutualFund::all();
        foreach ($funds as $fund) {
            $name = $fund->name;
            $symbol = 'FUND';
            $category = 'Global';

            // Extract symbol from name like "Vanguard 500 Index Fund (VFIAX)"
            if (preg_match('/\(([^)]+)\)/', $name, $matches)) {
                $symbol = $matches[1];
            }

            // Determine category
            if (stripos($name, 'Index') !== false || stripos($name, '500') !== false || stripos($name, 'S&P') !== false) {
                $category = 'Index';
            } elseif (stripos($name, 'Tech') !== false || stripos($name, 'Software') !== false || stripos($name, 'Information') !== false) {
                $category = 'Tech';
            } elseif (stripos($name, 'Health') !== false || stripos($name, 'Science') !== false || stripos($name, 'Medicine') !== false) {
                $category = 'Health';
            } elseif (stripos($name, 'Real Estate') !== false || stripos($name, 'REIT') !== false) {
                $category = 'Real Estate';
            } elseif (stripos($name, 'Bond') !== false || stripos($name, 'Income') !== false || stripos($name, 'Fixed') !== false) {
                $category = 'Bonds';
            } elseif (stripos($name, 'Growth') !== false || stripos($name, 'Aggressive') !== false || stripos($name, 'Blue Chip') !== false) {
                $category = 'Growth';
            }

            $fund->update(['symbol' => $symbol, 'category' => $category]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mutual_funds', function (Blueprint $table) {
            $table->dropColumn(['symbol', 'category']);
        });
    }
};
