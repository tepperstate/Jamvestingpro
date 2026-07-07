<?php

namespace App\Models;

use App\Enums\Blockchain;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BlockchainScanCheckpoint extends Model
{
    use HasFactory;

    protected $fillable = [
        'blockchain',
        'network',
        'scanner_type',
        'address',
        'last_block_number',
        'last_block_hash',
        'last_tx_hash',
        'last_scan_at',
        'scan_count',
        'consecutive_errors',
        'last_error',
        'metadata',
    ];

    protected $casts = [
        'blockchain' => Blockchain::class,
        'last_scan_at' => 'datetime',
        'metadata' => 'array',
    ];
}
