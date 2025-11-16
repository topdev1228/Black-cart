<?php
declare(strict_types=1);

namespace App\Domain\Payments\Enums;

enum OrderLineItemStatus: string
{
    case OPEN = 'open';
    case ARCHIVED = 'archived';
    case DELIVERED = 'delivered';
    case IN_TRIAL = 'trial-in-progress';
    case INTERNAL = 'internal';
    case FULFILLED = 'fulfilled';
    case CANCELLED = 'cancelled';
    case INTERNAL_CANCELLED = 'internal-cancelled';
}
