<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LoanPosition extends Model
{
    protected $fillable = [
        'user_id', 'loan_plan_id', 'loan_id', 'collateral_amount', 'collateral_value', 'loan_amount', 'current_ltv', 'interest_accrued', 'total_repaid', 'remaining_balance', 'liquidation_price', 'admin_status', 'status', 'start_date', 'maturity_date', 'is_demo', 'buffer_percent', 'per_withdrawal_percent', 'splits_paid', 'admin_notes',
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'maturity_date' => 'datetime',
        'is_demo' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function loanPlan()
    {
        return $this->belongsTo(LoanPlan::class);
    }
}
