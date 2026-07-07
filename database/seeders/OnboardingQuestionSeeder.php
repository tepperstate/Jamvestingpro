<?php

namespace Database\Seeders;

use App\Models\OnboardingQuestion;
use Illuminate\Database\Seeder;

class OnboardingQuestionSeeder extends Seeder
{
    public function run()
    {
        $questions = [
            [
                'section' => 1,
                'question_text' => 'What is your investment experience?',
                'input_type' => 'select',
                'options' => ['None', '1-3 years', '4-10 years', '10+ years'],
                'is_required' => true,
                'order' => 1,
            ],
            [
                'section' => 1,
                'question_text' => 'What is your primary investment goal?',
                'input_type' => 'select',
                'options' => ['Capital growth', 'Income', 'Speculation', 'Hedging'],
                'is_required' => true,
                'order' => 2,
            ],
            [
                'section' => 2,
                'question_text' => 'What is your risk tolerance?',
                'input_type' => 'radio',
                'options' => ['Low', 'Medium', 'High'],
                'is_required' => true,
                'order' => 1,
            ],
            [
                'section' => 3,
                'question_text' => 'What is your estimated annual income?',
                'input_type' => 'select',
                'options' => ['< $25,000', '$25,000 - $100,000', '> $100,000'],
                'is_required' => true,
                'order' => 1,
            ],
            [
                'section' => 4,
                'question_text' => 'What is your estimated net worth?',
                'input_type' => 'select',
                'options' => ['< $100,000', '$100,000 - $500,000', '> $500,000'],
                'is_required' => true,
                'order' => 1,
            ],
        ];

        foreach ($questions as $q) {
            OnboardingQuestion::updateOrCreate(
                ['question_text' => $q['question_text']],
                $q
            );
        }
    }
}
