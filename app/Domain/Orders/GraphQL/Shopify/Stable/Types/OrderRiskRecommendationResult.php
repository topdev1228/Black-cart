<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

class OrderRiskRecommendationResult
{
    public const ACCEPT = 'ACCEPT';
    public const CANCEL = 'CANCEL';
    public const INVESTIGATE = 'INVESTIGATE';
    public const NONE = 'NONE';

    public static function endpoint(): string
    {
        return 'shopify-orders-2024-01';
    }

    public static function config(): string
    {
        return \Safe\realpath(__DIR__ . '/../../../../../../../sailor.php');
    }
}
