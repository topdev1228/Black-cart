<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

class PublicationUserErrorCode
{
    public const BLANK = 'BLANK';
    public const CANNOT_MODIFY_APP_CATALOG = 'CANNOT_MODIFY_APP_CATALOG';
    public const CANNOT_MODIFY_APP_CATALOG_PUBLICATION = 'CANNOT_MODIFY_APP_CATALOG_PUBLICATION';
    public const CANNOT_MODIFY_MARKET_CATALOG = 'CANNOT_MODIFY_MARKET_CATALOG';
    public const CANNOT_MODIFY_MARKET_CATALOG_PUBLICATION = 'CANNOT_MODIFY_MARKET_CATALOG_PUBLICATION';
    public const CATALOG_NOT_FOUND = 'CATALOG_NOT_FOUND';
    public const INVALID = 'INVALID';
    public const INVALID_PUBLISHABLE_ID = 'INVALID_PUBLISHABLE_ID';
    public const MARKET_NOT_FOUND = 'MARKET_NOT_FOUND';
    public const PRODUCT_TYPE_INCOMPATIBLE_WITH_CATALOG_CONTEXT_DRIVERS = 'PRODUCT_TYPE_INCOMPATIBLE_WITH_CATALOG_CONTEXT_DRIVERS';
    public const PRODUCT_TYPE_INCOMPATIBLE_WITH_CATALOG_TYPE = 'PRODUCT_TYPE_INCOMPATIBLE_WITH_CATALOG_TYPE';
    public const PUBLICATION_LOCKED = 'PUBLICATION_LOCKED';
    public const PUBLICATION_NOT_FOUND = 'PUBLICATION_NOT_FOUND';
    public const PUBLICATION_UPDATE_LIMIT_EXCEEDED = 'PUBLICATION_UPDATE_LIMIT_EXCEEDED';
    public const TAKEN = 'TAKEN';
    public const TOO_LONG = 'TOO_LONG';
    public const TOO_SHORT = 'TOO_SHORT';
    public const UNSUPPORTED_PUBLICATION_ACTION = 'UNSUPPORTED_PUBLICATION_ACTION';
    public const UNSUPPORTED_PUBLISHABLE_TYPE = 'UNSUPPORTED_PUBLISHABLE_TYPE';

    public static function endpoint(): string
    {
        return 'shopify-orders-2024-01';
    }

    public static function config(): string
    {
        return \Safe\realpath(__DIR__ . '/../../../../../../../sailor.php');
    }
}
