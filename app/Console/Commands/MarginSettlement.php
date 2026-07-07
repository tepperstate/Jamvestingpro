<?php

namespace App\Console\Commands;

use App\Models\MarginPosition;
use Illuminate\Console\Command;

class MarginSettlement extends Command
{
    protected $signature = 'margin:settle';

    protected $description = 'Cron command for margin interest and liquidation';

    public function handle()
    {
        $positions = MarginPosition::where('status', 'open')->get();

        foreach ($positions as $position) {
            // accrual logic
            $position->increment('interest_accrued', 0.01); // Simplified

            if ($position->margin_ratio <= 0) {
                $position->update(['status' => 'liquidated', 'admin_status' => 'liquidated']);
            }
        }

        $this->info('Margin settled.');
    }
}
