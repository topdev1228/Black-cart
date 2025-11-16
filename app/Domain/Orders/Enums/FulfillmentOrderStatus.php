<?php
declare(strict_types=1);

namespace App\Domain\Orders\Enums;

enum FulfillmentOrderStatus: string
{
    case CANCELLED = 'cancelled';
    case CLOSED = 'closed';
    case INCOMPLETE = 'incomplete';
    case IN_PROGRESS = 'in-progress';
    case ON_HOLD = 'on-hold';
    case OPEN = 'open';
    case SCHEDULED = 'scheduled';
}
