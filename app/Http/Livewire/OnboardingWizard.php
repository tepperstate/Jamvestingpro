<?php

namespace App\Http\Livewire;

use App\Models\OnboardingQuestion;
use App\Models\UserOnboardingResponse;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class OnboardingWizard extends Component
{
    public $currentQuestion;

    public $responses = [];

    public $pathQueue = []; // Queue of question IDs to ask next

    public function mount()
    {
        // Get the first question (the profile selector)
        $this->currentQuestion = OnboardingQuestion::with('options')->whereNull('depends_on')->orderBy('sort_order')->first();
        if (! $this->currentQuestion) {
            // No questions available, skip onboarding
            $this->finishOnboarding();
        }
    }

    public function selectOption($questionId, $value)
    {
        $this->responses[$questionId] = $value;

        // Find next questions that depend on this value
        $dependentQuestions = OnboardingQuestion::where('depends_on', $value)->orderBy('sort_order')->pluck('id')->toArray();

        if (! empty($dependentQuestions)) {
            // Add them to the queue
            $this->pathQueue = array_merge($this->pathQueue, $dependentQuestions);
        }

        $this->nextQuestion();
    }

    public function nextQuestion()
    {
        if (empty($this->pathQueue)) {
            $this->finishOnboarding();

            return;
        }

        $nextId = array_shift($this->pathQueue);
        $this->currentQuestion = OnboardingQuestion::with('options')->find($nextId);
    }

    public function finishOnboarding()
    {
        // Save all responses
        $user = Auth::user();
        if ($user && ! empty($this->responses)) {
            foreach ($this->responses as $qId => $val) {
                UserOnboardingResponse::updateOrCreate(
                    ['user_id' => $user->id, 'question_id' => $qId],
                    ['response_value' => $val]
                );
            }
        }

        // Redirect to verification/start page
        return redirect()->route('start');
    }

    public function skip()
    {
        // User skipped
        return redirect()->route('start');
    }

    public function render()
    {
        return view('livewire.onboarding-wizard');
    }
}
