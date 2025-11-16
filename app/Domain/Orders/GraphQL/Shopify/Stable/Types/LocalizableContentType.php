<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

class LocalizableContentType
{
    public const FILE_REFERENCE = 'FILE_REFERENCE';
    public const HTML = 'HTML';
    public const INLINE_RICH_TEXT = 'INLINE_RICH_TEXT';
    public const JSON = 'JSON';
    public const JSON_STRING = 'JSON_STRING';
    public const LIST_FILE_REFERENCE = 'LIST_FILE_REFERENCE';
    public const LIST_MULTI_LINE_TEXT_FIELD = 'LIST_MULTI_LINE_TEXT_FIELD';
    public const LIST_SINGLE_LINE_TEXT_FIELD = 'LIST_SINGLE_LINE_TEXT_FIELD';
    public const LIST_URL = 'LIST_URL';
    public const MULTI_LINE_TEXT_FIELD = 'MULTI_LINE_TEXT_FIELD';
    public const RICH_TEXT_FIELD = 'RICH_TEXT_FIELD';
    public const SINGLE_LINE_TEXT_FIELD = 'SINGLE_LINE_TEXT_FIELD';
    public const STRING = 'STRING';
    public const URI = 'URI';
    public const URL = 'URL';

    public static function endpoint(): string
    {
        return 'shopify-orders-2024-01';
    }

    public static function config(): string
    {
        return \Safe\realpath(__DIR__ . '/../../../../../../../sailor.php');
    }
}
