<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

class DiscountApplicationTargetSelection
{
    public const ALL = 'ALL';
    public const ENTITLED = 'ENTITLED';
    public const EXPLICIT = 'EXPLICIT';

    public static function endpoint(): string
    {
        return 'shopify-orders-2024-01';
    }

    public static function config(): string
    {
        return \Safe\realpath(__DIR__ . '/../../../../../../../sailor.php');
    }
}
