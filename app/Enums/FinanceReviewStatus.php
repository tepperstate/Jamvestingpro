<?php

namespace App\Enums;

enum FinanceReviewStatus: string {
    case PENDING = 'pending';
    case REVIEWED = 'reviewed';
    case APPROVED = 'approved';
    case REJECTED = 'rejected';
}
