<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

class PriceListFixedPricesByProductBulkUpdateUserErrorCode
{
    public const DUPLICATE_ID_IN_INPUT = 'DUPLICATE_ID_IN_INPUT';
    public const ID_MUST_BE_MUTUALLY_EXCLUSIVE = 'ID_MUST_BE_MUTUALLY_EXCLUSIVE';
    public const NO_UPDATE_OPERATIONS_SPECIFIED = 'NO_UPDATE_OPERATIONS_SPECIFIED';
    public const PRICES_TO_ADD_CURRENCY_MISMATCH = 'PRICES_TO_ADD_CURRENCY_MISMATCH';
    public const PRICE_LIMIT_EXCEEDED = 'PRICE_LIMIT_EXCEEDED';
    public const PRICE_LIST_DOES_NOT_EXIST = 'PRICE_LIST_DOES_NOT_EXIST';
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
