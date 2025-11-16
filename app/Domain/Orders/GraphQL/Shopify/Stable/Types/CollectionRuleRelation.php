<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

class CollectionRuleRelation
{
    public const CONTAINS = 'CONTAINS';
    public const ENDS_WITH = 'ENDS_WITH';
    public const EQUALS = 'EQUALS';
    public const GREATER_THAN = 'GREATER_THAN';
    public const IS_NOT_SET = 'IS_NOT_SET';
    public const IS_SET = 'IS_SET';
    public const LESS_THAN = 'LESS_THAN';
    public const NOT_CONTAINS = 'NOT_CONTAINS';
    public const NOT_EQUALS = 'NOT_EQUALS';
    public const STARTS_WITH = 'STARTS_WITH';

    public static function endpoint(): string
    {
        return 'shopify-orders-2024-01';
    }

    public static function config(): string
    {
        return \Safe\realpath(__DIR__ . '/../../../../../../../sailor.php');
    }
}
