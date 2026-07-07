<?php

namespace App\Models;

use App\Enums\Blockchain;
use App\Enums\TransactionMatchStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BlockchainTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'blockchain',
        'network',
        'tx_hash',
        'block_number',
        'block_hash',
        'block_timestamp',
        'from_address',
        'to_address',
        'currency',
        'token_contract',
        'amount',
        'amount_decimal',
        'token_decimals',
        'fee',
        'fee_currency',
        'confirmations',
        'required_confirmations',
        'is_confirmed',
        'first_seen_at',
        'confirmed_at',
        'matched_payment_id',
        'match_status',
        'match_confidence',
        'scan_source',
        'is_processed',
        'raw_data',
        'metadata',
    ];

    protected $casts = [
        'blockchain' => Blockchain::class,
        'match_status' => TransactionMatchStatus::class,
        'amount' => 'decimal:18',
        'amount_decimal' => 'decimal:10',
        'fee' => 'decimal:10',
        'is_confirmed' => 'boolean',
        'is_processed' => 'boolean',
        'block_timestamp' => 'datetime',
        'first_seen_at' => 'datetime',
        'confirmed_at' => 'datetime',
        'raw_data' => 'array',
        'metadata' => 'array',
    ];

    public function matchedPayment()
    {
        return $this->belongsTo(PendingPaymentRequest::class, 'matched_payment_id');
    }
}
