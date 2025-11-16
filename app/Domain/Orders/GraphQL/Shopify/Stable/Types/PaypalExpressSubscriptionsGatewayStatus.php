<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

class PaypalExpressSubscriptionsGatewayStatus
{
    public const DISABLED = 'DISABLED';
    public const ENABLED = 'ENABLED';
    public const PENDING = 'PENDING';

    public static function endpoint(): string
    {
        return 'shopify-orders-2024-01';
    }

    public static function config(): string
    {
        return \Safe\realpath(__DIR__ . '/../../../../../../../sailor.php');
    }
}
