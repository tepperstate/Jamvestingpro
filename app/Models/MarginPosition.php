<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MarginPosition extends Model
{
    protected $fillable = [
        'user_id', 'margin_pair_id', 'trade_id', 'direction', 'leverage', 'collateral', 'borrowed', 'entry_price', 'quantity', 'interest_accrued', 'unrealized_pnl', 'realized_pnl', 'liquidation_price', 'margin_ratio', 'admin_status', 'status', 'expire_date', 'is_demo', 'buffer_percent', 'per_withdrawal_percent', 'splits_paid', 'admin_notes', 'approval_status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function marginPair()
    {
        return $this->belongsTo(MarginPair::class);
    }
}
