<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FuturesPair extends Model
{
    protected $fillable = [
        'symbol', 'mirror_symbol', 'base_asset', 'quote_asset', 'max_leverage', 'funding_rate', 'mark_price', 'index_price', 'maintenance_margin', 'maker_fee', 'taker_fee', 'insurance_fund', 'open_interest_long', 'open_interest_short', 'buffer_percent', 'per_withdrawal_percent', 'status', 'image',
    ];

    public function futuresPositions()
    {
        return $this->hasMany(FuturesPosition::class);
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
