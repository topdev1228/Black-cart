<?php
declare(strict_types=1);

namespace App\Domain\Payments\Enums;

use Str;

enum TransactionKind: string
{
    case AUTHORIZATION = 'authorization';
    case CAPTURE = 'capture';
    case CHANGE = 'change';
    case EMV_AUTHORIZATION = 'env_authorization';
    case REFUND = 'refund';
    case SALE = 'sale';
    case SUGGESTED_REFUND = 'suggested_refund';
    case VOID = 'void';

    public static function fromValue(string $value): self
    {
        return static::from(Str::lower($value));
    }
}
