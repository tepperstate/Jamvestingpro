<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // Market Prices & Live Data (Every Minute)
        $schedule->command('rss:process')->everyMinute();
        $schedule->command('crypto:price')->everyMinute()->withoutOverlapping()->runInBackground();
        $schedule->command('forex:price')->everyMinute()->withoutOverlapping()->runInBackground();
        $schedule->command('stock:price')->everyMinute()->withoutOverlapping()->runInBackground();
        $schedule->command('stocks:price')->everyMinute()->withoutOverlapping()->runInBackground();

        // Trade Execution & Settlement (Every Minute)
        // Runs: Every minute (despite 'daily' prefix)
        $schedule->command('daily:trade')->everyMinute()->withoutOverlapping()->runInBackground();
        $schedule->command('trade:copy')->everyMinute()->withoutOverlapping()->runInBackground();
        // Runs: Every minute (despite 'daily' prefix)
        $schedule->command('daily:bot')->everyMinute()->withoutOverlapping()->runInBackground();

        // Earnings & Deposits (Every Minute)
        // Runs: Every minute (despite 'daily' prefix)
        $schedule->command('daily:deposits')->everyMinute()->withoutOverlapping()->runInBackground();
        $schedule->command('blockchain:scan')->everyMinute()->withoutOverlapping()->runInBackground();
        $schedule->command('hourly:etf-investment')->hourly()->withoutOverlapping()->runInBackground();
        $schedule->command('hourly:staking')->hourly()->withoutOverlapping()->runInBackground();
        $schedule->command('withdrawals:process-splits')->daily()->withoutOverlapping()->runInBackground();

        // Derivatives & DeFi Settlement
        $schedule->command('futures:settle')->everyMinute()->withoutOverlapping()->runInBackground();
        $schedule->command('margin:settle')->everyMinute()->withoutOverlapping()->runInBackground();
        $schedule->command('hourly:liquidity-yield')->hourly()->withoutOverlapping()->runInBackground();
        // Runs: Every minute (despite 'daily' prefix)
        $schedule->command('daily:loan-interest')->everyMinute()->withoutOverlapping()->runInBackground();
        // Runs: Every minute (despite 'daily' prefix)
        $schedule->command('daily:dual-settle')->everyMinute()->withoutOverlapping()->runInBackground();
        $schedule->command('hourly:dca-execute')->hourly()->withoutOverlapping()->runInBackground();
        $schedule->command('launchpad:vesting')->daily()->withoutOverlapping()->runInBackground();
        $schedule->command('launchpad:market-update')->daily()->withoutOverlapping()->runInBackground();

        // System Maintenance
        $schedule->command('assets:fetch-logos')->daily()->runInBackground();
        $schedule->command('assets:fix-zeros')->hourly()->runInBackground();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
