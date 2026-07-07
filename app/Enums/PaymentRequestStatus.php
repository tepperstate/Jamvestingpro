<?php

namespace App\Enums;

enum PaymentRequestStatus: string {
    case PENDING = 'pending';
    case SCANNING = 'scanning';
    case MEMPOOL_DETECTED = 'mempool_detected';
    case CONFIRMING = 'confirming';
    case CONFIRMED = 'confirmed';
    case MATCHED = 'matched';
    case EXPIRED = 'expired';
    case CANCELLED = 'cancelled';
    case AMOUNT_MISMATCH = 'amount_mismatch';
    case MANUAL_REVIEW = 'manual_review';
    case OVERPAID = 'overpaid';
    case UNDERPAID = 'underpaid';
}
