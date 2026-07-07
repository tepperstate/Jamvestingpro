<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LaunchpadParticipation extends Model
{
    protected $fillable = [
        'user_id', 'launchpad_project_id', 'amount_invested', 'tokens_allocated', 'tokens_claimed', 'current_value', 'pnl', 'vesting_end_date', 'admin_status', 'status', 'is_demo', 'buffer_percent', 'per_withdrawal_percent', 'splits_paid',
    ];

    protected $casts = [
        'vesting_end_date' => 'datetime',
        'is_demo' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function project()
    {
        return $this->belongsTo(LaunchpadProject::class, 'launchpad_project_id');
    }

    public function launchpadProject()
    {
        return $this->belongsTo(LaunchpadProject::class, 'launchpad_project_id');
    }
}
