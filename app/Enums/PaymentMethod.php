<?php

namespace App\Enums;

enum PaymentMethod: string {
    case OXAPAY = 'oxapay';
    case NOWPAYMENTS = 'nowpayments';
    case NOWPAYMENTS_CARD = 'nowpayments_card';
    case BANK_WIRE = 'bank_wire';
    
    public function displayName(): string
    {
        return match($this) {
            self::OXAPAY => 'OxaPay (Crypto)',
            self::NOWPAYMENTS => 'NowPayments (Crypto)',
            self::NOWPAYMENTS_CARD => 'NowPayments Card (Fiat)',
            self::BANK_WIRE => 'Bank Wire Transfer',
        };
    }
    
    public function requiresFinancialReview(): bool
    {
        return $this === PaymentMethod::BANK_WIRE;
    }
}
