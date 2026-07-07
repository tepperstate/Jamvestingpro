<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DcaPlan extends Model
{
    protected $fillable = [
        'name', 'asset', 'frequency', 'min_amount', 'max_amount', 'spread_markup', 'execution_hour', 'execution_day', 'buffer_percent', 'per_withdrawal_percent', 'status', 'image', 'description',
    ];

    public function dcaSubscriptions()
    {
        return $this->hasMany(DcaSubscription::class);
    }
}
