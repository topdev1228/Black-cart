<?php
declare(strict_types=1);

namespace App\Domain\Shopify\Enums;

enum DepositType: string
{
    case FIXED = 'fixed';
    case PERCENTAGE = 'percentage';
}
