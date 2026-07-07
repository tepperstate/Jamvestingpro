<?php

namespace App\Models;

use App\Enums\Blockchain;
use App\Enums\PaymentRequestStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PendingPaymentRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'uuid',
        'user_id',
        'order_id',
        'gateway',
        'gateway_payment_id',
        'blockchain',
        'currency',
        'expected_amount',
        'fiat_amount',
        'fiat_currency',
        'exchange_rate',
        'deposit_address',
        'monitored_wallet_id',
        'amount_tolerance_pct',
        'amount_min',
        'amount_max',
        'initiated_at',
        'expires_at',
        'ttl_minutes',
        'status',
        'matched_transaction_id',
        'matched_at',
        'confirmed_at',
        'metadata',
    ];

    protected $casts = [
        'blockchain' => Blockchain::class,
        'status' => PaymentRequestStatus::class,
        'expected_amount' => 'decimal:10',
        'fiat_amount' => 'decimal:2',
        'exchange_rate' => 'decimal:10',
        'amount_min' => 'decimal:10',
        'amount_max' => 'decimal:10',
        'initiated_at' => 'datetime',
        'expires_at' => 'datetime',
        'matched_at' => 'datetime',
        'confirmed_at' => 'datetime',
        'metadata' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function monitoredWallet()
    {
        return $this->belongsTo(MonitoredWallet::class);
    }

    public function matchedTransaction()
    {
        return $this->belongsTo(BlockchainTransaction::class, 'matched_transaction_id');
    }
}
