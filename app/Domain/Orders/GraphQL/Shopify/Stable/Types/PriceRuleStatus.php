<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

class PriceRuleStatus
{
    public const ACTIVE = 'ACTIVE';
    public const EXPIRED = 'EXPIRED';
    public const SCHEDULED = 'SCHEDULED';

    public static function endpoint(): string
    {
        return 'shopify-orders-2024-01';
    }

    public static function config(): string
    {
        return \Safe\realpath(__DIR__ . '/../../../../../../../sailor.php');
    }
}
