<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

class OrderRiskAssessmentCreateUserErrorCode
{
    public const INVALID = 'INVALID';
    public const NOT_FOUND = 'NOT_FOUND';
    public const ORDER_ALREADY_FULFILLED = 'ORDER_ALREADY_FULFILLED';
    public const TOO_MANY_FACTS = 'TOO_MANY_FACTS';

    public static function endpoint(): string
    {
        return 'shopify-orders-2024-01';
    }

    public static function config(): string
    {
        return \Safe\realpath(__DIR__ . '/../../../../../../../sailor.php');
    }
}
