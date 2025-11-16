<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

class BulkProductResourceFeedbackCreateUserErrorCode
{
    public const BLANK = 'BLANK';
    public const INVALID = 'INVALID';
    public const LESS_THAN_OR_EQUAL_TO = 'LESS_THAN_OR_EQUAL_TO';
    public const MAXIMUM_FEEDBACK_LIMIT_EXCEEDED = 'MAXIMUM_FEEDBACK_LIMIT_EXCEEDED';
    public const OUTDATED_FEEDBACK = 'OUTDATED_FEEDBACK';
    public const PRESENT = 'PRESENT';
    public const PRODUCT_NOT_FOUND = 'PRODUCT_NOT_FOUND';

    public static function endpoint(): string
    {
        return 'shopify-orders-2024-01';
    }

    public static function config(): string
    {
        return \Safe\realpath(__DIR__ . '/../../../../../../../sailor.php');
    }
}
