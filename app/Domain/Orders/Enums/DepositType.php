<?php
declare(strict_types=1);

namespace App\Domain\Orders\Enums;

enum DepositType: string
{
    case FIXED = 'fixed';
    case PERCENTAGE = 'percentage';
}
