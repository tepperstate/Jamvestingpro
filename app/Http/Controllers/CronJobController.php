<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Artisan;

class CronJobController extends Controller
{
    public function is_trade()
    {

        return Artisan::call('daily:trade');
    }

    public function is_deposit()
    {

        return Artisan::call('daily:deposits');
    }

    public function is_trade_copy()
    {

        return Artisan::call('trade:copy');
    }

    public function is_bot()
    {

        return Artisan::call('daily:bot');
    }

    public function is_forex()
    {

        return Artisan::call('forex:price');
    }

    public function is_crypto()
    {

        return Artisan::call('crypto:price');
    }

    public function is_stock()
    {

        return Artisan::call('stock:price');
    }

    public function is_stocks()
    {
        return Artisan::call('stocks:price');
    }

    public function is_logo_sync()
    {
        return Artisan::call('assets:fetch-logos');
    }

    public function runMasterCron()
    {
        try {
            Artisan::call('schedule:run');

            return response()->json([
                'status' => 'success',
                'message' => 'Master cron executed successfully. All scheduled tasks have been processed.',
                'output' => Artisan::output(),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to execute master cron.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}

// wget -O /dev/null https://nanostockhood.com/
