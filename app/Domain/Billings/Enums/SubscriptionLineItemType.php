<?php
declare(strict_types=1);

namespace App\Domain\Billings\Enums;

enum SubscriptionLineItemType: string
{
    case RECURRING = 'recurring';
    case USAGE = 'usage';
}
