<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

class SellingPlanCategory
{
    public const OTHER = 'OTHER';
    public const PRE_ORDER = 'PRE_ORDER';
    public const SUBSCRIPTION = 'SUBSCRIPTION';
    public const TRY_BEFORE_YOU_BUY = 'TRY_BEFORE_YOU_BUY';

    public static function endpoint(): string
    {
        return 'shopify-orders-2024-01';
    }

    public static function config(): string
    {
        return \Safe\realpath(__DIR__ . '/../../../../../../../sailor.php');
    }
}
