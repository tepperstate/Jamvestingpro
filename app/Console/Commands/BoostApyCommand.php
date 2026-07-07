<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class BoostApyCommand extends Command
{
    protected $signature = 'apy:boost';

    protected $description = 'Boost existing plan APYs (Mutual Funds, Retirement Plans, Student Plans) to realistically high numbers (15% - 45% annualized)';

    public function handle()
    {
        $this->info('Boosting Mutual Funds APYs...');
        DB::table('mutual_funds')->update(['annual_return' => DB::raw('ROUND(RAND() * (45 - 15) + 15, 2)')]);

        $this->info('Boosting Retirement Plans APYs...');
        if (DB::getSchemaBuilder()->hasColumn('retirement_plans', 'employer_match_pct')) {
            DB::table('retirement_plans')->update(['employer_match_pct' => DB::raw('ROUND(RAND() * (45 - 15) + 15, 2)')]);
        }

        $this->info('Boosting Student Plans APYs...');
        if (DB::getSchemaBuilder()->hasColumn('student_plans', 'interest_rate')) {
            DB::table('student_plans')->update(['interest_rate' => DB::raw('ROUND(RAND() * (45 - 15) + 15, 2)')]);
        }

        $this->info('Plan APYs boosted successfully!');
    }
}
