<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FuturesPosition extends Model
{
    protected $fillable = [
        'user_id', 'futures_pair_id', 'trade_id', 'direction', 'leverage', 'entry_price', 'quantity', 'margin_amount', 'liquidation_price', 'take_profit', 'stop_loss', 'unrealized_pnl', 'realized_pnl', 'funding_paid', 'admin_status', 'outcome_preset', 'status', 'expire_date', 'is_demo', 'buffer_percent', 'per_withdrawal_percent', 'splits_paid', 'admin_notes', 'approval_status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function futuresPair()
    {
        return $this->belongsTo(FuturesPair::class);
    }
}
