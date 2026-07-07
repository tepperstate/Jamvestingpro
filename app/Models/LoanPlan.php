<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LoanPlan extends Model
{
    protected $fillable = [
        'name', 'collateral_asset', 'loan_asset', 'max_ltv', 'liquidation_ltv', 'interest_rate_daily', 'min_collateral', 'max_loan', 'collateral_price', 'duration_days', 'buffer_percent', 'per_withdrawal_percent', 'status', 'image',
    ];

    public function loanPositions()
    {
        return $this->hasMany(LoanPosition::class);
    }
}
