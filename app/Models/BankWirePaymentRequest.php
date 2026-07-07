<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BankWirePaymentRequest extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'uuid',
        'user_id',
        'order_id',
        'payment_method',
        'currency',
        'amount',
        'fiat_currency',
        'exchange_rate',
        'fiat_amount',
        'bank_name',
        'account_holder_name',
        'account_number',
        'routing_number',
        'swift_bic',
        'iban',
        'bank_address',
        'bank_country',
        'bank_city',
        'bank_state',
        'bank_zip',
        'wire_reference',
        'payment_reference',
        'status',
        'finance_status',
        'payment_status',
        'initiated_at',
        'submitted_to_finance_at',
        'finance_reviewed_at',
        'payment_sent_at',
        'confirmed_at',
        'expires_at',
        'finance_notes',
        'user_notes',
        'metadata',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'exchange_rate' => 'decimal:10',
        'fiat_amount' => 'decimal:2',
        'initiated_at' => 'datetime',
        'submitted_to_finance_at' => 'datetime',
        'finance_reviewed_at' => 'datetime',
        'payment_sent_at' => 'datetime',
        'confirmed_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class)->nullable();
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeSubmittedToFinance($query)
    {
        return $query->where('finance_status', 'pending')->where('status', 'submitted_to_finance');
    }

    public function scopeUnderReview($query)
    {
        return $query->where('finance_status', 'reviewed');
    }

    public function scopeApproved($query)
    {
        return $query->where('finance_status', 'approved');
    }

    public function scopeRejected($query)
    {
        return $query->where('finance_status', 'rejected');
    }

    protected static function booted()
    {
        static::updated(function ($wireRequest) {
            if ($wireRequest->isDirty('finance_status')) {
                $original = $wireRequest->getOriginal('finance_status');
                $new = $wireRequest->finance_status;
                \Log::info("Audit Log: Bank Wire {$wireRequest->payment_reference} finance_status changed from {$original} to {$new} by user " . (auth()->id() ?? 'system'));
            }
            if ($wireRequest->isDirty('status')) {
                $original = $wireRequest->getOriginal('status');
                $new = $wireRequest->status;
                \Log::info("Audit Log: Bank Wire {$wireRequest->payment_reference} status changed from {$original} to {$new} by user " . (auth()->id() ?? 'system'));
            }
        });
    }
}
