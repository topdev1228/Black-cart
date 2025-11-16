<?php
declare(strict_types=1);

namespace App\Domain\Orders\Enums;

enum LineItemStatusUpdatedBy: string
{
    case SHOPIFY = 'shopify';
    case ASSUMED_DELIVERY = 'assumed_delivery';
}
