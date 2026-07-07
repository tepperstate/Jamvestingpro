<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Site_setting;

class SiteSettingsController extends Controller
{
    /**
     * Get global site settings for the frontend.
     */
    public function globalSettings()
    {
        $settings = Site_setting::first();

        if (! $settings) {
            return response()->json([
                'success' => false,
                'message' => 'Settings not found',
            ], 404);
        }

        // Return only the fields needed by the frontend for SEO & branding
        return response()->json([
            'success' => true,
            'data' => [
                'site_name' => $settings->name,
                'logo_url' => $settings->logo ? asset('storage/'.$settings->logo) : null,
                'favicon_url' => $settings->favicon ? asset('storage/'.$settings->favicon) : null,
                'meta_description' => $settings->meta,
            ],
        ]);
    }
}
