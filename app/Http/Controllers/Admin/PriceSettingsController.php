<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PriceProvider;
use Illuminate\Http\Request;

class PriceSettingsController extends Controller
{
    public function index()
    {
        $premiumProviders = PriceProvider::where('category', 'premium')
            ->orderBy('priority', 'desc')
            ->get();

        $publicProviders = PriceProvider::where('category', 'public')
            ->orderBy('priority', 'desc')
            ->get();

        return view('admin.price_settings', [
            'premium' => $premiumProviders,
            'public' => $publicProviders,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'provider_type' => 'required',
            'asset_type' => 'required',
            'category' => 'required|in:public,premium',
        ]);

        PriceProvider::create($request->all());
        notify()->success('Price provider added successfully.');

        return back();
    }

    public function update(Request $request, $id)
    {
        $provider = PriceProvider::findOrFail($id);
        $provider->update($request->all());

        return response()->json(['status' => true, 'message' => 'Provider updated.']);
    }

    public function toggle($id)
    {
        $provider = PriceProvider::findOrFail($id);
        $provider->is_active = ! $provider->is_active;
        $provider->save();
        notify()->success('Provider status toggled.');

        return back();
    }

    public function delete($id)
    {
        PriceProvider::findOrFail($id)->delete();
        notify()->success('Provider deleted.');

        return back();
    }
}
