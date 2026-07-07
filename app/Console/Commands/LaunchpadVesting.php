<?php

namespace App\Console\Commands;

use App\Models\LaunchpadParticipation;
use Carbon\Carbon;
use Illuminate\Console\Command;

class LaunchpadVesting extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'launchpad:vesting';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check for launchpad token vesting unlocks';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $participations = LaunchpadParticipation::where('status', 'vesting')
            ->whereNotNull('vesting_end_date')
            ->where('vesting_end_date', '<=', Carbon::now())
            ->get();

        foreach ($participations as $part) {
            $part->status = 'claimable';
            $part->save();
            $this->info("Unlocked participation #{$part->id}");
        }

        $this->info('Launchpad vesting check completed.');

        return 0;
    }
}
