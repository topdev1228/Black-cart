<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

class ShopResourceFeedbackCreateUserErrorCode
{
    public const BLANK = 'BLANK';
    public const INVALID = 'INVALID';
    public const OUTDATED_FEEDBACK = 'OUTDATED_FEEDBACK';
    public const PRESENT = 'PRESENT';

    public static function endpoint(): string
    {
        return 'shopify-orders-2024-01';
    }

    public static function config(): string
    {
        return \Safe\realpath(__DIR__ . '/../../../../../../../sailor.php');
    }
}
