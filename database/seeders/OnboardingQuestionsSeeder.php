<?php

namespace Database\Seeders;

use App\Models\OnboardingOption;
use App\Models\OnboardingQuestion;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class OnboardingQuestionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Truncate existing questions and options to start fresh
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        OnboardingQuestion::truncate();
        OnboardingOption::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $questions = [
            // Section 1: Investment Background (Experience)
            [
                'title' => 'Primary Investment Objective',
                'subtitle' => 'What is the main goal for your account?',
                'section' => 1,
                'question_key' => 'q_objective',
                'input_type' => 'select',
                'sort_order' => 1,
                'depends_on' => null,
                'options' => [
                    ['label' => 'Growth', 'value' => 'Growth'],
                    ['label' => 'Income', 'value' => 'Income'],
                    ['label' => 'Speculation', 'value' => 'Speculation'],
                    ['label' => 'Capital Preservation', 'value' => 'Capital Preservation'],
                ],
            ],
            [
                'title' => 'Years of Trading Experience',
                'subtitle' => 'How many years have you been trading equities?',
                'section' => 1,
                'question_key' => 'q_experience_years',
                'input_type' => 'select',
                'sort_order' => 2,
                'depends_on' => null,
                'options' => [
                    ['label' => '0-1 years', 'value' => '0-1'],
                    ['label' => '1-3 years', 'value' => '1-3'],
                    ['label' => '3-5 years', 'value' => '3-5'],
                    ['label' => '5+ years', 'value' => '5+'],
                ],
            ],
            [
                'title' => 'Options Trading Experience',
                'subtitle' => 'What is your level of experience with options?',
                'section' => 1,
                'question_key' => 'q_options_exp',
                'input_type' => 'select',
                'sort_order' => 3,
                'depends_on' => null,
                'options' => [
                    ['label' => 'None', 'value' => 'None'],
                    ['label' => 'Covered Calls', 'value' => 'Covered Calls'],
                    ['label' => 'Advanced Spreads', 'value' => 'Advanced Spreads'],
                ],
            ],

            // Section 2: Financials (Economic Profile)
            [
                'title' => 'Annual Income',
                'subtitle' => 'What is your approximate annual income?',
                'section' => 2,
                'question_key' => 'q_annual_income',
                'input_type' => 'select',
                'sort_order' => 4,
                'depends_on' => null,
                'options' => [
                    ['label' => 'Under $50k', 'value' => 'Under 50k'],
                    ['label' => '$50k - $100k', 'value' => '50k-100k'],
                    ['label' => '$100k - $250k', 'value' => '100k-250k'],
                    ['label' => 'Over $250k', 'value' => 'Over 250k'],
                ],
            ],
            [
                'title' => 'Liquid Net Worth',
                'subtitle' => 'Excluding your primary residence, what is your liquid net worth?',
                'section' => 2,
                'question_key' => 'q_liquid_net_worth',
                'input_type' => 'select',
                'sort_order' => 5,
                'depends_on' => null,
                'options' => [
                    ['label' => 'Under $50k', 'value' => 'Under 50k'],
                    ['label' => '$50k - $100k', 'value' => '50k-100k'],
                    ['label' => '$100k - $500k', 'value' => '100k-500k'],
                    ['label' => 'Over $500k', 'value' => 'Over 500k'],
                ],
            ],
            [
                'title' => 'Source of Wealth',
                'subtitle' => 'What is the primary source of your investable assets?',
                'section' => 2,
                'question_key' => 'q_source_wealth',
                'input_type' => 'select',
                'sort_order' => 6,
                'depends_on' => null,
                'options' => [
                    ['label' => 'Employment', 'value' => 'Employment'],
                    ['label' => 'Inheritance', 'value' => 'Inheritance'],
                    ['label' => 'Investments', 'value' => 'Investments'],
                    ['label' => 'Business Owner', 'value' => 'Business Owner'],
                ],
            ],

            // Section 3: Compliance (Regulatory Disclosure I)
            [
                'title' => 'Politically Exposed Person (PEP)',
                'subtitle' => 'Are you or an immediate family member a Politically Exposed Person?',
                'section' => 3,
                'question_key' => 'q_pep',
                'input_type' => 'select',
                'sort_order' => 7,
                'depends_on' => null,
                'options' => [
                    ['label' => 'Yes', 'value' => 'Yes'],
                    ['label' => 'No', 'value' => 'No'],
                ],
            ],
            [
                'title' => 'Broker-Dealer Affiliation',
                'subtitle' => 'Are you affiliated with or employed by a registered broker-dealer or FINRA?',
                'section' => 3,
                'question_key' => 'q_broker_affiliation',
                'input_type' => 'select',
                'sort_order' => 8,
                'depends_on' => null,
                'options' => [
                    ['label' => 'Yes', 'value' => 'Yes'],
                    ['label' => 'No', 'value' => 'No'],
                ],
            ],
            [
                'title' => '10% Shareholder',
                'subtitle' => 'Are you a 10% or greater shareholder of a publicly traded company?',
                'section' => 3,
                'question_key' => 'q_shareholder',
                'input_type' => 'select',
                'sort_order' => 9,
                'depends_on' => null,
                'options' => [
                    ['label' => 'Yes', 'value' => 'Yes'],
                    ['label' => 'No', 'value' => 'No'],
                ],
            ],

            // Section 4: Finalize (Final Attestation)
            [
                'title' => 'Terms of Service',
                'subtitle' => 'Do you agree to the Terms of Service and Customer Agreement?',
                'section' => 4,
                'question_key' => 'q_tos',
                'input_type' => 'select',
                'sort_order' => 10,
                'depends_on' => null,
                'options' => [
                    ['label' => 'I Agree', 'value' => 'I Agree'],
                ],
            ],
            [
                'title' => 'W-9 Certification',
                'subtitle' => 'Is the taxpayer identification number provided correct?',
                'section' => 4,
                'question_key' => 'q_w9',
                'input_type' => 'select',
                'sort_order' => 11,
                'depends_on' => null,
                'options' => [
                    ['label' => 'Yes', 'value' => 'Yes'],
                ],
            ],
        ];

        foreach ($questions as $qData) {
            $options = $qData['options'];
            unset($qData['options']);

            $question = OnboardingQuestion::create($qData);

            foreach ($options as $optData) {
                $question->options()->create($optData);
            }
        }
    }
}
