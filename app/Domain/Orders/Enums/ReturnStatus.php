<?php
declare(strict_types=1);

namespace App\Domain\Orders\Enums;

enum ReturnStatus: string
{
    case CANCELED = 'canceled';
    case CLOSED = 'closed';
    case DECLINED = 'declined';
    case OPEN = 'open';
    case REQUESTED = 'requested';
}
