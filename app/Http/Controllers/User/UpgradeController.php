<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Package;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UpgradeController extends Controller
{
    /**
     * Display the upgrade page with packages.
     */
    public function index()
    {
        $packages = Package::whereBetween('id', [1, 6])->orderBy('amount', 'asc')->get();
        $user = Auth::user();
        $currentPackage = $user->package;

        return view('user.upgrade', [
            'packages' => $packages,
            'user' => $user,
            'currentPackage' => $currentPackage,
        ]);
    }

    /**
     * Handle a user request for Basic plan access.
     * Sets basic_plan_approved to 2 (pending).
     */
    public function requestBasicPlan(Request $request)
    {
        $user = Auth::user();

        // Only allow request if not already approved or pending
        if ($user->basic_plan_approved == 1) {
            return response()->json(['status' => false, 'message' => 'Already approved']);
        }

        if ($user->basic_plan_approved == 2) {
            return response()->json(['status' => false, 'message' => 'Request already pending']);
        }

        $user->update(['basic_plan_approved' => 2]);

        return response()->json(['status' => true, 'message' => 'Request submitted successfully']);
    }
}
