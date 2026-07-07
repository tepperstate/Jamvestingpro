<?php

namespace App\Models;

use App\Enums\Blockchain;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MonitoredWallet extends Model
{
    use HasFactory;

    protected $fillable = [
        'blockchain',
        'network',
        'address',
        'address_type',
        'label',
        'currency',
        'token_contract',
        'token_standard',
        'is_active',
        'derivation_path',
        'user_id',
        'metadata',
    ];

    protected $casts = [
        'blockchain' => Blockchain::class,
        'is_active' => 'boolean',
        'metadata' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
