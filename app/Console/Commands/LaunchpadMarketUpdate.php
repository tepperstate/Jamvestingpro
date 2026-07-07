<?php

namespace App\Console\Commands;

use App\Models\LaunchpadParticipation;
use App\Models\LaunchpadProject;
use Illuminate\Console\Command;

class LaunchpadMarketUpdate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'launchpad:market-update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Updates Launchpad tokens value based on the daily increase percentage set by admin.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting Launchpad Market Update...');

        // Fetch projects that have a daily increase set
        $projects = LaunchpadProject::where('daily_increase_percentage', '>', 0)->get();

        $projectsUpdated = 0;
        $participationsUpdated = 0;

        foreach ($projects as $project) {
            $increaseMultiplier = 1 + ($project->daily_increase_percentage / 100);

            // Update the project's base price for future buyers
            $project->price_per_token = $project->price_per_token * $increaseMultiplier;

            // If the project is listed, also increase listing price
            if ($project->listing_price > 0) {
                $project->listing_price = $project->listing_price * $increaseMultiplier;
            }
            $project->save();
            $projectsUpdated++;

            // Update all participations for this project
            $participations = LaunchpadParticipation::where('launchpad_project_id', $project->id)->get();
            foreach ($participations as $part) {
                $oldValue = $part->current_value;
                $newValue = $oldValue * $increaseMultiplier;

                $part->current_value = $newValue;
                $part->pnl = $newValue - $part->amount_invested;
                $part->save();
                $participationsUpdated++;
            }
        }

        $this->info("Market update complete! Updated {$projectsUpdated} projects and {$participationsUpdated} user portfolios.");
    }
}
