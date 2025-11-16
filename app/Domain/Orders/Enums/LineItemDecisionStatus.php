<?php
declare(strict_types=1);

namespace App\Domain\Orders\Enums;

enum LineItemDecisionStatus: string
{
    case KEPT = 'kept';
    case RETURNED = 'returned';
    case INTERNAL = 'internal';
}
