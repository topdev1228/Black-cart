<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

class CollectionAddProductsV2UserErrorCode
{
    public const CANT_ADD_TO_SMART_COLLECTION = 'CANT_ADD_TO_SMART_COLLECTION';
    public const COLLECTION_DOES_NOT_EXIST = 'COLLECTION_DOES_NOT_EXIST';

    public static function endpoint(): string
    {
        return 'shopify-orders-2024-01';
    }

    public static function config(): string
    {
        return \Safe\realpath(__DIR__ . '/../../../../../../../sailor.php');
    }
}
