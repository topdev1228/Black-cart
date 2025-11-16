<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

class ReverseFulfillmentOrderDispositionType
{
    public const MISSING = 'MISSING';
    public const NOT_RESTOCKED = 'NOT_RESTOCKED';
    public const PROCESSING_REQUIRED = 'PROCESSING_REQUIRED';
    public const RESTOCKED = 'RESTOCKED';

    public static function endpoint(): string
    {
        return 'shopify-orders-2024-01';
    }

    public static function config(): string
    {
        return \Safe\realpath(__DIR__ . '/../../../../../../../sailor.php');
    }
}
