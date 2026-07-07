<?php

namespace App\Enums;

enum TransactionMatchStatus: string {
    case UNMATCHED = 'unmatched';
    case AUTO_MATCHED = 'auto_matched';
    case MANUAL_MATCHED = 'manual_matched';
    case NO_MATCH = 'no_match';
    case DUPLICATE = 'duplicate';
    case REJECTED = 'rejected';
}
