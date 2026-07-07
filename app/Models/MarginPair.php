<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MarginPair extends Model
{
    protected $fillable = [
        'symbol', 'mirror_symbol', 'max_leverage', 'borrow_rate_hourly', 'maintenance_margin', 'max_borrow', 'collateral_factor', 'mark_price', 'buffer_percent', 'per_withdrawal_percent', 'status',
    ];

    public function marginPositions()
    {
        return $this->hasMany(MarginPosition::class);
    }

    protected function getStockTrade()
    {
        if (! isset($this->stockTradeCache)) {
            $this->stockTradeCache = Stock_Trade::where('symbol', $this->symbol)->first();
        }

        return $this->stockTradeCache;
    }

    public function getBuyAttribute()
    {
        $st = $this->getStockTrade();

        return $st ? $st->buy : $this->mark_price;
    }

    public function getChangesAttribute()
    {
        $st = $this->getStockTrade();

        return $st ? $st->getAttribute('changes') : 0;
    }

    public function getNameAttribute()
    {
        $st = $this->getStockTrade();

        return $st ? $st->name : str_replace('USDT', '', $this->symbol);
    }
}
