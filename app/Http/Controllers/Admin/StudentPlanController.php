<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\StudentPlan;
use Illuminate\Http\Request;

class StudentPlanController extends Controller
{
    public function index()
    {
        $plans = StudentPlan::orderBy('id', 'desc')->get();

        return view('admin.student_plans.index', compact('plans'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'interest_rate' => 'required|numeric',
            'min_amount' => 'required|numeric',
            'max_amount' => 'required|numeric',
            'duration_months' => 'required|integer',
        ]);

        $data = $request->except('_token');
        if ($request->hasFile('image')) {
            $filename = $request->file('image');
            $newfilename = time().'.'.$filename->getClientOriginalExtension();
            $request->file('image')->storeAs('image', $newfilename, 'public');
            $data['image'] = $newfilename;
        }

        StudentPlan::create($data);

        return back()->with('status', 'Student Plan created successfully.');
    }

    public function update(Request $request)
    {
        $plan = StudentPlan::findOrFail($request->id);

        $data = $request->except(['_token', 'id']);
        if ($request->hasFile('image')) {
            $filename = $request->file('image');
            $newfilename = time().'.'.$filename->getClientOriginalExtension();
            $request->file('image')->storeAs('image', $newfilename, 'public');
            $data['image'] = $newfilename;
        }

        $plan->update($data);

        return back()->with('status', 'Student Plan updated successfully.');
    }

    public function destroy($id)
    {
        StudentPlan::findOrFail($id)->delete();

        return back()->with('status', 'Student Plan deleted.');
    }
}
