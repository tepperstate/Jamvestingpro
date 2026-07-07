<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\ApiKey;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ApiKeysController extends Controller
{
    public function index()
    {
        $api_keys = ApiKey::where('user_id', auth()->user()->id)->get();

        return view('user.api_keys.index', compact('api_keys'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'permissions' => 'nullable|array',
            'ip_whitelist' => 'nullable|string',
        ]);

        $apiKey = new ApiKey;
        $apiKey->user_id = auth()->user()->id;
        $apiKey->name = $request->name;
        $apiKey->api_key = Str::random(40);
        $apiKey->api_secret = Str::random(60);

        $permissions = $request->permissions ? $request->permissions : ['read'];
        $apiKey->permissions = $permissions;

        if ($request->ip_whitelist) {
            $ips = array_map('trim', explode(',', $request->ip_whitelist));
            $apiKey->ip_whitelist = $ips;
        }

        $apiKey->save();

        return back()->with('success', 'API Key generated successfully. Please copy the Secret Key now, it will not be shown again!')->with('new_api_secret', $apiKey->api_secret);
    }

    public function destroy($id)
    {
        $apiKey = ApiKey::where('user_id', auth()->user()->id)->findOrFail($id);
        $apiKey->delete();

        return back()->with('success', 'API Key deleted successfully.');
    }
}
