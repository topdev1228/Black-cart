<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

class MarketingActivityHierarchyLevel
{
    public const AD = 'AD';
    public const AD_GROUP = 'AD_GROUP';
    public const CAMPAIGN = 'CAMPAIGN';

    public static function endpoint(): string
    {
        return 'shopify-orders-2024-01';
    }

    public static function config(): string
    {
        return \Safe\realpath(__DIR__ . '/../../../../../../../sailor.php');
    }
}
