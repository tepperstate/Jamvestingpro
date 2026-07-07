<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\OnboardingQuestion;
use App\Models\User;
use App\Models\UserOnboardingResponse;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class QuestionController extends Controller
{
    public function index()
    {
        $questions = OnboardingQuestion::orderBy('sort_order')->get();

        return view('question', compact('questions'));
    }

    public function store(Request $request)
    {
        $questions = OnboardingQuestion::all();

        foreach ($questions as $q) {
            if ($request->has($q->question_key)) {
                UserOnboardingResponse::updateOrCreate(
                    ['user_id' => auth()->id(), 'question_id' => $q->id],
                    ['response_value' => is_array($request->input($q->question_key))
                                ? implode(', ', $request->input($q->question_key))
                                : $request->input($q->question_key)]
                );
            }
        }

        // Keep legacy sync for now if needed, or just turn off the flag
        User::where('id', auth()->id())->update([
            'question' => 'off',
        ]);

        return response()->json(['status' => 'Data submitted successfully'], 201);
    }

    public function skip()
    {
        User::where('id', auth()->id())->update([
            'question' => 'off',
        ]);

        return redirect()->route('dashboard.index')->with('success', 'Onboarding skipped. You can always manage your profile later.');
    }

    public function security(Request $request)
    {

        DB::table('security')->insert([
            'user_id' => auth()->user()->id,
            'question_one' => $request->question_one,
            'question_two' => $request->question_two,
            'question_three' => $request->question_three,
            'answer_one' => $request->answer_one,
            'answer_two' => $request->answer_two,
            'answer_three' => $request->answer_three,
            'created_at' => Carbon::now(),
        ]);

        User::where('id', auth()->user()->id)->update([
            'security' => 'yes',
        ]);

        return response()->json(['status' => 'Data submitted successfully'], 201);
    }
}
