<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

class ProductOptionUpdateVariantStrategy
{
    public const LEAVE_AS_IS = 'LEAVE_AS_IS';
    public const MANAGE = 'MANAGE';

    public static function endpoint(): string
    {
        return 'shopify-orders-2024-01';
    }

    public static function config(): string
    {
        return \Safe\realpath(__DIR__ . '/../../../../../../../sailor.php');
    }
}
