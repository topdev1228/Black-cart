<?php
declare(strict_types=1);

namespace App\Domain\Shopify\Enums;

enum MandatoryWebhookTopic: string
{
    case CUSTOMERS_DATA_REQUEST = 'customers/data_request';
    case CUSTOMERS_REDACT = 'customers/redact';
    case SHOP_REDACT = 'shop/redact';
}
