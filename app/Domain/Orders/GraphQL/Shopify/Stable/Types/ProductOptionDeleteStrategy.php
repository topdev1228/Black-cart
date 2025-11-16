<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

class ProductOptionDeleteStrategy
{
    public const DEFAULT = 'DEFAULT';
    public const NON_DESTRUCTIVE = 'NON_DESTRUCTIVE';
    public const POSITION = 'POSITION';

    public static function endpoint(): string
    {
        return 'shopify-orders-2024-01';
    }

    public static function config(): string
    {
        return \Safe\realpath(__DIR__ . '/../../../../../../../sailor.php');
    }
}
