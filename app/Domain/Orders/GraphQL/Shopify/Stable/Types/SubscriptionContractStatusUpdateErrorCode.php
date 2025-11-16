<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

class SubscriptionContractStatusUpdateErrorCode
{
    public const CONTRACT_TERMINATED = 'CONTRACT_TERMINATED';
    public const INVALID = 'INVALID';

    public static function endpoint(): string
    {
        return 'shopify-orders-2024-01';
    }

    public static function config(): string
    {
        return \Safe\realpath(__DIR__ . '/../../../../../../../sailor.php');
    }
}
