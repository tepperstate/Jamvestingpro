<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\RetirementPlan;
use Illuminate\Http\Request;

class RetirementPlanController extends Controller
{
    public function index()
    {
        $plans = RetirementPlan::orderBy('id', 'desc')->get();

        return view('admin.retirement_plans.index', compact('plans'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'employer_match_pct' => 'required|numeric',
            'min_contribution' => 'required|numeric',
            'max_contribution' => 'required|numeric',
        ]);

        $data = $request->except('_token');
        if ($request->hasFile('image')) {
            $filename = $request->file('image');
            $newfilename = time().'.'.$filename->getClientOriginalExtension();
            $request->file('image')->storeAs('image', $newfilename, 'public');
            $data['image'] = $newfilename;
        }

        RetirementPlan::create($data);

        return back()->with('status', 'Retirement Plan created successfully.');
    }

    public function update(Request $request)
    {
        $plan = RetirementPlan::findOrFail($request->id);

        $data = $request->except(['_token', 'id']);
        if ($request->hasFile('image')) {
            $filename = $request->file('image');
            $newfilename = time().'.'.$filename->getClientOriginalExtension();
            $request->file('image')->storeAs('image', $newfilename, 'public');
            $data['image'] = $newfilename;
        }

        $plan->update($data);

        return back()->with('status', 'Retirement Plan updated successfully.');
    }

    public function destroy($id)
    {
        RetirementPlan::findOrFail($id)->delete();

        return back()->with('status', 'Retirement Plan deleted.');
    }
}
