<?php
declare(strict_types=1);

namespace App\Domain\Orders\Enums;

enum TransactionStatus: string
{
    case AWAITING_RESPONSE = 'awaiting_response';
    case ERROR = 'error';
    case FAILURE = 'failure';
    case PENDING = 'pending';
    case SUCCESS = 'success';
    case UNKNOWN = 'unknown';
}
