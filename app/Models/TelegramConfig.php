<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TelegramConfig extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'bot_token',
        'chat_id',
        'notification_types',
        'is_active',
    ];

    protected $casts = [
        'notification_types' => 'array',
        'is_active' => 'boolean',
    ];

    public static function getEventLabels(): array
    {
        return [
            'deposit_submitted' => 'Deposit Submission',
            'withdrawal_requested' => 'Withdrawal Request',
            'support_ticket_created' => 'Support Ticket Created',
            'trade_executed' => 'Trade Executed',
            'kyc_submitted' => 'KYC Submitted',
        ];
    }
}
