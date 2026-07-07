<?php

namespace App\Models;

use App\Enums\Blockchain;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BlockchainScanLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'blockchain',
        'event_type',
        'severity',
        'message',
        'tx_hash',
        'payment_request_id',
        'blockchain_transaction_id',
        'context',
    ];

    protected $casts = [
        'blockchain' => Blockchain::class,
        'context' => 'array',
    ];
}
