<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

class PromiseSkuSettingUpsertUserErrorCode
{
    public const BLANK = 'BLANK';
    public const NOT_FOUND = 'NOT_FOUND';
    public const TOO_LONG = 'TOO_LONG';

    public static function endpoint(): string
    {
        return 'shopify-orders-2024-01';
    }

    public static function config(): string
    {
        return \Safe\realpath(__DIR__ . '/../../../../../../../sailor.php');
    }
}
