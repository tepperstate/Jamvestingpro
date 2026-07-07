<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Balance;
use App\Models\RetirementAccount;
use App\Models\StakingPosition;
use App\Models\StudentSaving;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WealthOversightController extends Controller
{
    // ==========================================
    // DEFI YIELD VAULTS & STAKING
    // ==========================================
    public function stakingIndex()
    {
        $stakings = StakingPosition::with(['user', 'plan'])->latest()->paginate(20);

        return view('admin.staking_oversight', compact('stakings'));
    }

    public function stakingUpdate(Request $request)
    {
        $action = $request->action;
        $id = $request->id;
        $position = StakingPosition::findOrFail($id);

        if ($action === 'delete') {
            $position->delete();

            return response()->json(['status' => 'Staking record deleted successfully.']);
        }

        if ($action === 'pause') {
            $position->update(['status' => 'paused']);

            return response()->json(['status' => 'Staking paused.']);
        }

        if ($action === 'resume') {
            $position->update(['status' => 'active']);

            return response()->json(['status' => 'Staking resumed.']);
        }

        if ($action === 'force_complete') {
            $user = $position->user;
            $isDemo = $position->is_demo;
            $balanceColumn = $isDemo ? 'demo' : 'amount';

            DB::transaction(function () use ($position, $user, $balanceColumn) {
                $refundAmount = $position->amount + $position->earned;
                Balance::where('user_id', $user->id)->where('symbol', 'usd')->increment($balanceColumn, $refundAmount);
                $position->update(['status' => 'completed']);
            });

            return response()->json(['status' => 'Staking forced complete and funds refunded.']);
        }

        if ($action === 'edit') {
            $earned = (float) $request->earned;
            $position->update(['earned' => $earned]);

            return response()->json(['status' => 'Yield parameter updated.']);
        }

        return response()->json(['error' => 'Invalid action.'], 400);
    }

    // ==========================================
    // MINOR TRUST (UTMA/UGMA)
    // ==========================================
    public function trustIndex()
    {
        $trusts = StudentSaving::with(['user', 'plan'])->latest()->paginate(20);

        return view('admin.trust_oversight', compact('trusts'));
    }

    public function trustUpdate(Request $request)
    {
        $action = $request->action;
        $id = $request->id;
        $trust = StudentSaving::findOrFail($id);

        if ($action === 'delete') {
            $trust->delete();

            return response()->json(['status' => 'Trust record deleted successfully.']);
        }

        if ($action === 'pause') {
            $trust->update(['status' => 'paused']);

            return response()->json(['status' => 'Trust accumulation paused.']);
        }

        if ($action === 'resume') {
            $trust->update(['status' => 'active']);

            return response()->json(['status' => 'Trust accumulation resumed.']);
        }

        if ($action === 'force_complete') {
            $user = $trust->user;
            $isDemo = $trust->is_demo;
            $balanceColumn = $isDemo ? 'demo' : 'amount';

            DB::transaction(function () use ($trust, $user, $balanceColumn) {
                $refundAmount = $trust->amount + $trust->earned;
                Balance::where('user_id', $user->id)->where('symbol', 'usd')->increment($balanceColumn, $refundAmount);
                $trust->update(['status' => 'completed']);
            });

            return response()->json(['status' => 'Trust forced complete and funds refunded.']);
        }

        if ($action === 'edit') {
            $earned = (float) $request->earned;
            $trust->update(['earned' => $earned]);

            return response()->json(['status' => 'Accumulated yield parameter updated.']);
        }

        return response()->json(['error' => 'Invalid action.'], 400);
    }

    // ==========================================
    // TAX-ADVANTAGED RETIREMENT (IRA)
    // ==========================================
    public function iraIndex()
    {
        $iras = RetirementAccount::with(['user', 'plan'])->latest()->paginate(20);

        return view('admin.ira_oversight', compact('iras'));
    }

    public function iraUpdate(Request $request)
    {
        $action = $request->action;
        $id = $request->id;
        $ira = RetirementAccount::findOrFail($id);

        if ($action === 'delete') {
            $ira->delete();

            return response()->json(['status' => 'IRA record deleted successfully.']);
        }

        if ($action === 'pause') {
            $ira->update(['status' => 'paused']);

            return response()->json(['status' => 'IRA accumulation paused.']);
        }

        if ($action === 'resume') {
            $ira->update(['status' => 'active']);

            return response()->json(['status' => 'IRA accumulation resumed.']);
        }

        if ($action === 'force_complete') {
            $user = $ira->user;
            $isDemo = $ira->is_demo;
            $balanceColumn = $isDemo ? 'demo' : 'amount';

            DB::transaction(function () use ($ira, $user, $balanceColumn) {
                // Return total balance minus anything? For IRA, returning the total balance.
                Balance::where('user_id', $user->id)->where('symbol', 'usd')->increment($balanceColumn, $ira->balance);
                $ira->update(['status' => 'completed']);
            });

            return response()->json(['status' => 'IRA forced complete and balance refunded.']);
        }

        if ($action === 'edit') {
            $employer_contributions = (float) $request->employer_contributions;
            $ira->update([
                'employer_contributions' => $employer_contributions,
                'balance' => $ira->employee_contributions + $employer_contributions,
            ]);

            return response()->json(['status' => 'Employer match adjusted and balance recalculated.']);
        }

        return response()->json(['error' => 'Invalid action.'], 400);
    }
}
