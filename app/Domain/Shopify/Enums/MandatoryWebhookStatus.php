<?php
declare(strict_types=1);

namespace App\Domain\Shopify\Enums;

enum MandatoryWebhookStatus: string
{
    case PENDING = 'pending';
    case COMPLETED = 'completed';
}
