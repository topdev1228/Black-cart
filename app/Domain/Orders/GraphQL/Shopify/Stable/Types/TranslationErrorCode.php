<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

class TranslationErrorCode
{
    public const BLANK = 'BLANK';
    public const FAILS_RESOURCE_VALIDATION = 'FAILS_RESOURCE_VALIDATION';
    public const INVALID = 'INVALID';
    public const INVALID_CODE = 'INVALID_CODE';
    public const INVALID_FORMAT = 'INVALID_FORMAT';
    public const INVALID_KEY_FOR_MODEL = 'INVALID_KEY_FOR_MODEL';
    public const INVALID_LOCALE_FOR_MARKET = 'INVALID_LOCALE_FOR_MARKET';
    public const INVALID_LOCALE_FOR_SHOP = 'INVALID_LOCALE_FOR_SHOP';
    public const INVALID_MARKET_LOCALIZABLE_CONTENT = 'INVALID_MARKET_LOCALIZABLE_CONTENT';
    public const INVALID_TRANSLATABLE_CONTENT = 'INVALID_TRANSLATABLE_CONTENT';
    public const INVALID_VALUE_FOR_HANDLE_TRANSLATION = 'INVALID_VALUE_FOR_HANDLE_TRANSLATION';
    public const MARKET_CUSTOM_CONTENT_NOT_ALLOWED = 'MARKET_CUSTOM_CONTENT_NOT_ALLOWED';
    public const MARKET_DOES_NOT_EXIST = 'MARKET_DOES_NOT_EXIST';
    public const MARKET_LOCALE_CREATION_FAILED = 'MARKET_LOCALE_CREATION_FAILED';
    public const RESOURCE_NOT_FOUND = 'RESOURCE_NOT_FOUND';
    public const RESOURCE_NOT_MARKET_CUSTOMIZABLE = 'RESOURCE_NOT_MARKET_CUSTOMIZABLE';
    public const RESOURCE_NOT_TRANSLATABLE = 'RESOURCE_NOT_TRANSLATABLE';
    public const TOO_MANY_KEYS_FOR_RESOURCE = 'TOO_MANY_KEYS_FOR_RESOURCE';

    public static function endpoint(): string
    {
        return 'shopify-orders-2024-01';
    }

    public static function config(): string
    {
        return \Safe\realpath(__DIR__ . '/../../../../../../../sailor.php');
    }
}
