<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

class SellingPlanFulfillmentTrigger
{
    public const ANCHOR = 'ANCHOR';
    public const ASAP = 'ASAP';
    public const EXACT_TIME = 'EXACT_TIME';
    public const UNKNOWN = 'UNKNOWN';

    public static function endpoint(): string
    {
        return 'shopify-orders-2024-01';
    }

    public static function config(): string
    {
        return \Safe\realpath(__DIR__ . '/../../../../../../../sailor.php');
    }
}
