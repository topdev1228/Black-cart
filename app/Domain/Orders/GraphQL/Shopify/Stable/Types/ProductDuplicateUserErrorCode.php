<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

class ProductDuplicateUserErrorCode
{
    public const BUNDLES_ERROR = 'BUNDLES_ERROR';
    public const EMPTY_TITLE = 'EMPTY_TITLE';
    public const EMPTY_VARIANT = 'EMPTY_VARIANT';
    public const FAILED_TO_SAVE = 'FAILED_TO_SAVE';
    public const GENERIC_ERROR = 'GENERIC_ERROR';
    public const PRODUCT_DOES_NOT_EXIST = 'PRODUCT_DOES_NOT_EXIST';

    public static function endpoint(): string
    {
        return 'shopify-orders-2024-01';
    }

    public static function config(): string
    {
        return \Safe\realpath(__DIR__ . '/../../../../../../../sailor.php');
    }
}
