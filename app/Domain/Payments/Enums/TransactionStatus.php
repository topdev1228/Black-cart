<?php
declare(strict_types=1);

namespace App\Domain\Payments\Enums;

use Str;

enum TransactionStatus: string
{
    case AWAITING_RESPONSE = 'awaiting_response';
    case ERROR = 'error';
    case FAILURE = 'failure';
    case PENDING = 'pending';
    case SUCCESS = 'success';
    case UNKNOWN = 'unknown';

    public static function fromValue(string $status): self
    {
        return self::from(Str::lower($status));
    }
}
