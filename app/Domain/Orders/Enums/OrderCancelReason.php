<?php
declare(strict_types=1);

namespace App\Domain\Orders\Enums;

enum OrderCancelReason: string
{
    case CUSTOMER = 'CUSTOMER'; // The customer wanted to cancel the order.
    case DECLINED = 'DECLINED'; // Payment was declined.
    case FRAUD = 'FRAUD'; // The order was fraudulent.
    case INVENTORY = 'INVENTORY'; // There was insufficient inventory.
    case OTHER = 'OTHER'; // The order was canceled for an unlisted reason.
    case STAFF = 'STAFF'; //Staff made an error.
}
