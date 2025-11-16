<?php
declare(strict_types=1);

namespace App\Domain\Payments\Enums;

enum OrderStatus: string
{
    case OPEN = 'open';
    case ARCHIVED = 'archived';
    case CANCELLED = 'canceled';
    case PAYMENT_PENDING = 'pending';
    case PAYMENT_AUTHORIZED = 'authorized';
    case PAYMENT_OVERDUE = 'overdue';
    case PAYMENT_EXPIRING = 'expiring';
    case PAYMENT_EXPIRED = 'expired';
    case PAYMENT_PAID = 'paid';
    case PAYMENT_REFUNDED = 'refunded';
    case PAYMENT_PARTIALLY_REFUNDED = 'partially refunded';
    case PAYMENT_PARTIALLY_PAID = 'partially paid';
    case PAYMENT_VOIDED = 'voided';
    case PAYMENT_UNPAID = 'unpaid';

    case FULFILLMENT_FULFILLED = 'fulfilled';
    case FULFILLMENT_UNFULFILLED = 'unfulfilled';
    case FULFILLMENT_PARTIALLY_FULFILLED = 'partially fulfilled';
    case FULFILLMENT_SCHEDULED = 'scheduled';
    case FULFILLMENT_ON_HOLD = 'on hold';

    case RETURN_REQUESTED = 'return requested';
    case RETURN_IN_PROGRESS = 'return in progress';
    case RETURN_COMPLETED = 'returned';
    case RETURN_INSPECTION_COMPLETE = 'inspection complete';
    case RETURN_FAILED = 'return failed';

    case IN_TRIAL = 'trial-in-progress';
    case COMPLETED = 'completed';
}
