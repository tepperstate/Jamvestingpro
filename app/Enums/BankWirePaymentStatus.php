<?php

namespace App\Enums;

enum BankWirePaymentStatus: string {
    case PENDING = 'pending';
    case SENT = 'sent';
    case CONFIRMED = 'confirmed';
    case FAILED = 'failed';
}
