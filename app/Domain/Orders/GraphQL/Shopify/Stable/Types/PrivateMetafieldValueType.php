<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

class PrivateMetafieldValueType
{
    public const BOOLEAN = 'BOOLEAN';
    public const FLOAT = 'FLOAT';
    public const INTEGER = 'INTEGER';
    public const JSON_STRING = 'JSON_STRING';
    public const STRING = 'STRING';

    public static function endpoint(): string
    {
        return 'shopify-orders-2024-01';
    }

    public static function config(): string
    {
        return \Safe\realpath(__DIR__ . '/../../../../../../../sailor.php');
    }
}
