<?php

namespace App\Console\Commands;

use App\Jobs\ProcessFeedImportJob;
use App\Models\FeedSource;
use Carbon\Carbon;
use Illuminate\Console\Command;

class ProcessRSSFeeds extends Command
{
    protected $signature = 'rss:process';

    protected $description = 'Process RSS feeds based on their cron schedules';

    public function handle()
    {
        $sources = FeedSource::where('active', true)->get();
        $now = Carbon::now();

        foreach ($sources as $source) {
            $shouldRun = false;
            $lastRun = $source->last_run ? Carbon::parse($source->last_run) : null;

            if (! $lastRun) {
                $shouldRun = true;
            } else {
                $diffInMinutes = $lastRun->diffInMinutes($now);
                switch ($source->cron_schedule) {
                    case '5_minutes':
                        if ($diffInMinutes >= 5) {
                            $shouldRun = true;
                        }
                        break;
                    case '10_minutes':
                        if ($diffInMinutes >= 10) {
                            $shouldRun = true;
                        }
                        break;
                    case '15_minutes':
                        if ($diffInMinutes >= 15) {
                            $shouldRun = true;
                        }
                        break;
                    case '30_minutes':
                        if ($diffInMinutes >= 30) {
                            $shouldRun = true;
                        }
                        break;
                    case '45_minutes':
                        if ($diffInMinutes >= 45) {
                            $shouldRun = true;
                        }
                        break;
                    case 'hourly':
                        if ($diffInMinutes >= 60) {
                            $shouldRun = true;
                        }
                        break;
                    case '2_hours':
                        if ($diffInMinutes >= 120) {
                            $shouldRun = true;
                        }
                        break;
                    case '4_hours':
                        if ($diffInMinutes >= 240) {
                            $shouldRun = true;
                        }
                        break;
                    case '6_hours':
                        if ($diffInMinutes >= 360) {
                            $shouldRun = true;
                        }
                        break;
                    case '12_hours':
                        if ($diffInMinutes >= 720) {
                            $shouldRun = true;
                        }
                        break;
                    case 'daily':
                        if ($diffInMinutes >= 1440) {
                            $shouldRun = true;
                        }
                        break;
                    default:
                        $shouldRun = true;
                }
            }

            if ($shouldRun) {
                $this->info("Dispatching import for {$source->name}");
                ProcessFeedImportJob::dispatch($source->id);
                $source->last_run = $now;
                $source->save();
            }
        }
    }
}
