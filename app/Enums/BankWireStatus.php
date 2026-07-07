<?php

namespace App\Enums;

enum BankWireStatus: string {
    case PENDING = 'pending';
    case SUBMITTED_TO_FINANCE = 'submitted_to_finance';
    case FINANCE_REVIEW = 'finance_review';
    case FINANCE_APPROVED = 'finance_approved';
    case FINANCE_REJECTED = 'finance_rejected';
    case PAYMENT_SENT = 'payment_sent';
    case CONFIRMED = 'confirmed';
    case EXPIRED = 'expired';
    case CANCELLED = 'cancelled';
}
