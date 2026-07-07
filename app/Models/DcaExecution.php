<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DcaExecution extends Model
{
    protected $fillable = [
        'dca_subscription_id', 'amount_usd', 'units_acquired', 'execution_price', 'market_price', 'spread_charged', 'status', 'executed_at',
    ];

    protected $casts = [
        'executed_at' => 'datetime',
    ];

    public function dcaSubscription()
    {
        return $this->belongsTo(DcaSubscription::class);
    }
}
