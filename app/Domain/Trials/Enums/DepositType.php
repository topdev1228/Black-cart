<?php
declare(strict_types=1);

namespace App\Domain\Trials\Enums;

enum DepositType: string
{
    case FIXED = 'fixed';
    case PERCENTAGE = 'percentage';
}
