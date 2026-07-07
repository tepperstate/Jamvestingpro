<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\OnboardingOption;
use App\Models\OnboardingQuestion;
use Illuminate\Http\Request;

class OnboardingController extends Controller
{
    public function index()
    {
        $questions = OnboardingQuestion::with('options')->orderBy('sort_order')->get();

        return view('admin.onboarding.index', compact('questions'));
    }

    public function create()
    {
        $allOptions = OnboardingOption::all();

        return view('admin.onboarding.create', compact('allOptions'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'subtitle' => 'nullable|string',
            'depends_on' => 'nullable|string',
            'sort_order' => 'required|integer',
            'section' => 'required|integer',
            'question_key' => 'required|string|max:255',
            'options' => 'array',
            'options.*.label' => 'required|string|max:255',
            'options.*.value' => 'required|string|max:255',
            'options.*.icon' => 'nullable|string|max:255',
        ]);

        $question = OnboardingQuestion::create([
            'title' => $request->title,
            'subtitle' => $request->subtitle,
            'depends_on' => $request->depends_on,
            'sort_order' => $request->sort_order,
            'section' => $request->section,
            'question_key' => $request->question_key,
        ]);

        if ($request->has('options')) {
            foreach ($request->options as $index => $opt) {
                OnboardingOption::create([
                    'question_id' => $question->id,
                    'label' => $opt['label'],
                    'value' => $opt['value'],
                    'icon' => $opt['icon'],
                    'sort_order' => $index,
                ]);
            }
        }

        return redirect()->route('admin.onboarding.index')->with('status', 'Question created successfully');
    }

    public function edit(OnboardingQuestion $question)
    {
        $question->load('options');
        $allOptions = OnboardingOption::all();

        return view('admin.onboarding.edit', compact('question', 'allOptions'));
    }

    public function update(Request $request, OnboardingQuestion $question)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'subtitle' => 'nullable|string',
            'depends_on' => 'nullable|string',
            'sort_order' => 'required|integer',
            'section' => 'required|integer',
            'question_key' => 'required|string|max:255',
            'options' => 'array',
            'options.*.label' => 'required|string|max:255',
            'options.*.value' => 'required|string|max:255',
            'options.*.icon' => 'nullable|string|max:255',
        ]);

        $question->update([
            'title' => $request->title,
            'subtitle' => $request->subtitle,
            'depends_on' => $request->depends_on,
            'sort_order' => $request->sort_order,
            'section' => $request->section,
            'question_key' => $request->question_key,
        ]);

        // Sync options: delete old, create new
        $question->options()->delete();

        if ($request->has('options')) {
            foreach ($request->options as $index => $opt) {
                OnboardingOption::create([
                    'question_id' => $question->id,
                    'label' => $opt['label'],
                    'value' => $opt['value'],
                    'icon' => $opt['icon'],
                    'sort_order' => $index,
                ]);
            }
        }

        return redirect()->route('admin.onboarding.index')->with('status', 'Question updated successfully');
    }

    public function destroy(OnboardingQuestion $question)
    {
        $question->options()->delete();
        $question->delete();

        return redirect()->route('admin.onboarding.index')->with('status', 'Question deleted successfully');
    }
}
