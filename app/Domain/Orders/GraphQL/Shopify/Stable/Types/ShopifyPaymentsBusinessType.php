<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

class ShopifyPaymentsBusinessType
{
    public const CORPORATION = 'CORPORATION';
    public const GOVERNMENT = 'GOVERNMENT';
    public const LLC = 'LLC';
    public const NON_PROFIT = 'NON_PROFIT';
    public const NOT_SET = 'NOT_SET';
    public const PARTNERSHIP = 'PARTNERSHIP';
    public const SOLE_PROP = 'SOLE_PROP';

    public static function endpoint(): string
    {
        return 'shopify-orders-2024-01';
    }

    public static function config(): string
    {
        return \Safe\realpath(__DIR__ . '/../../../../../../../sailor.php');
    }
}
