<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

class CustomerPredictedSpendTier
{
    public const HIGH = 'HIGH';
    public const LOW = 'LOW';
    public const MEDIUM = 'MEDIUM';

    public static function endpoint(): string
    {
        return 'shopify-orders-2024-01';
    }

    public static function config(): string
    {
        return \Safe\realpath(__DIR__ . '/../../../../../../../sailor.php');
    }
}
