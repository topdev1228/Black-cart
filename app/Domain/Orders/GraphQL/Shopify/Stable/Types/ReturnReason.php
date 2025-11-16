<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

class ReturnReason
{
    public const COLOR = 'COLOR';
    public const DEFECTIVE = 'DEFECTIVE';
    public const NOT_AS_DESCRIBED = 'NOT_AS_DESCRIBED';
    public const OTHER = 'OTHER';
    public const SIZE_TOO_LARGE = 'SIZE_TOO_LARGE';
    public const SIZE_TOO_SMALL = 'SIZE_TOO_SMALL';
    public const STYLE = 'STYLE';
    public const UNKNOWN = 'UNKNOWN';
    public const UNWANTED = 'UNWANTED';
    public const WRONG_ITEM = 'WRONG_ITEM';

    public static function endpoint(): string
    {
        return 'shopify-orders-2024-01';
    }

    public static function config(): string
    {
        return \Safe\realpath(__DIR__ . '/../../../../../../../sailor.php');
    }
}
