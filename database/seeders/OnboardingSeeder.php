<?php

namespace Database\Seeders;

use App\Models\OnboardingQuestion;
use Illuminate\Database\Seeder;

class OnboardingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Let's just use Eloquent to clear
        OnboardingQuestion::query()->delete();

        // 1. Initial Profile Question
        $profileQ = OnboardingQuestion::create([
            'title' => 'Customize your trading environment.',
            'subtitle' => 'Select your investor profile so we can unlock the right tax-advantaged retirement tools, API limits, and Crypto ETF strategies for you.',
            'input_type' => 'radio',
            'depends_on' => null,
            'is_required' => true,
            'sort_order' => 1,
        ]);

        $profileQ->options()->createMany([
            ['label' => 'Institutional Investor', 'value' => 'institutional', 'icon' => 'building-library', 'sort_order' => 1],
            ['label' => 'Accredited Investor', 'value' => 'accredited', 'icon' => 'briefcase', 'sort_order' => 2],
            ['label' => 'Family Investor', 'value' => 'family', 'icon' => 'user-group', 'sort_order' => 3],
        ]);

        // ----------------------------------------------------
        // Option A: Institutional Investor
        // ----------------------------------------------------
        $instQ1 = OnboardingQuestion::create([
            'title' => 'What is your primary mandate for this account?',
            'input_type' => 'radio',
            'depends_on' => 'institutional', // Matches the profile option value
            'is_required' => true,
            'sort_order' => 2,
        ]);
        $instQ1->options()->createMany([
            ['label' => 'Proprietary Trading', 'value' => 'proprietary_trading', 'sort_order' => 1],
            ['label' => 'Client Asset Management', 'value' => 'client_asset_management', 'sort_order' => 2],
            ['label' => 'Treasury/Yield Generation', 'value' => 'treasury_yield', 'sort_order' => 3],
        ]);

        $instQ2 = OnboardingQuestion::create([
            'title' => 'Do you require high-frequency API access for algorithmic routing of Crypto ETFs and stocks?',
            'input_type' => 'radio',
            'depends_on' => 'institutional',
            'is_required' => true,
            'sort_order' => 3,
        ]);
        $instQ2->options()->createMany([
            ['label' => 'Yes (Unlock developer docs & API keys)', 'value' => 'yes_api', 'sort_order' => 1],
            ['label' => 'No (UI dashboard trading only)', 'value' => 'no_api', 'sort_order' => 2],
        ]);

        // ----------------------------------------------------
        // Option B: Accredited Investor
        // ----------------------------------------------------
        $accQ1 = OnboardingQuestion::create([
            'title' => 'Which advanced asset classes are you looking to allocate toward?',
            'input_type' => 'radio', // or checkbox if multiple is allowed
            'depends_on' => 'accredited',
            'is_required' => true,
            'sort_order' => 4,
        ]);
        $accQ1->options()->createMany([
            ['label' => 'Pre-IPO Stocks', 'value' => 'pre_ipo_stocks', 'sort_order' => 1],
            ['label' => 'Direct Crypto Assets', 'value' => 'direct_crypto', 'sort_order' => 2],
            ['label' => 'Spot/Futures Crypto ETFs', 'value' => 'crypto_etfs', 'sort_order' => 3],
            ['label' => 'OTC Markets', 'value' => 'otc_markets', 'sort_order' => 4],
        ]);

        $accQ2 = OnboardingQuestion::create([
            'title' => 'What is your timeline for these specific alternative investments?',
            'input_type' => 'radio',
            'depends_on' => 'accredited',
            'is_required' => true,
            'sort_order' => 5,
        ]);
        $accQ2->options()->createMany([
            ['label' => 'Active trading (weekly/monthly)', 'value' => 'active_trading', 'sort_order' => 1],
            ['label' => 'Long-term hold (1-5 years)', 'value' => 'long_term_hold', 'sort_order' => 2],
            ['label' => 'Retirement timeline (10+ years)', 'value' => 'retirement_timeline', 'sort_order' => 3],
        ]);

        // ----------------------------------------------------
        // Option C: Family Investor
        // ----------------------------------------------------
        $famQ1 = OnboardingQuestion::create([
            'title' => 'Are you structuring this account primarily for wealth transfer or generational retirement planning?',
            'input_type' => 'radio',
            'depends_on' => 'family',
            'is_required' => true,
            'sort_order' => 6,
        ]);
        $famQ1->options()->createMany([
            ['label' => 'Multi-generational wealth', 'value' => 'multi_generational', 'sort_order' => 1],
            ['label' => 'Single retirement trust', 'value' => 'single_retirement_trust', 'sort_order' => 2],
            ['label' => 'Tax-advantaged portfolio growth', 'value' => 'tax_advantaged_growth', 'sort_order' => 3],
        ]);

        $famQ2 = OnboardingQuestion::create([
            'title' => 'Do you need multi-user access for other family members or a registered financial advisor?',
            'input_type' => 'radio',
            'depends_on' => 'family',
            'is_required' => true,
            'sort_order' => 7,
        ]);
        $famQ2->options()->createMany([
            ['label' => 'Yes (Set up a master account/sub-accounts)', 'value' => 'yes_multi_user', 'sort_order' => 1],
            ['label' => 'No (Just me)', 'value' => 'no_multi_user', 'sort_order' => 2],
        ]);
    }
}
