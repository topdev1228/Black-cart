<?php
declare(strict_types=1);

namespace App\Domain\Payments\Enums;

enum PaymentStatus: string
{
    case PURCHASED = 'PURCHASED';
    case AUTHORIZED = 'AUTHORIZED';
    case REDIRECT_REQUIRED = 'REDIRECT_REQUIRED';
    case ERROR = 'ERROR';
    case VOIDED = 'VOIDED';
    case PROCESSING = 'PROCESSING';
}
