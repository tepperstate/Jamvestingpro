<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DcaSubscription extends Model
{
    protected $fillable = [
        'user_id', 'dca_plan_id', 'amount_per_purchase', 'total_invested', 'total_units_acquired', 'avg_purchase_price', 'current_value', 'unrealized_pnl', 'executions_completed', 'next_execution', 'admin_price_override', 'admin_status', 'status', 'is_demo', 'buffer_percent', 'per_withdrawal_percent', 'splits_paid',
    ];

    protected $casts = [
        'next_execution' => 'datetime',
        'is_demo' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function dcaPlan()
    {
        return $this->belongsTo(DcaPlan::class);
    }

    public function dcaExecutions()
    {
        return $this->hasMany(DcaExecution::class);
    }
}
