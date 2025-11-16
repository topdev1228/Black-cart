<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

class ReturnDeclineReason
{
    public const FINAL_SALE = 'FINAL_SALE';
    public const OTHER = 'OTHER';
    public const RETURN_PERIOD_ENDED = 'RETURN_PERIOD_ENDED';

    public static function endpoint(): string
    {
        return 'shopify-orders-2024-01';
    }

    public static function config(): string
    {
        return \Safe\realpath(__DIR__ . '/../../../../../../../sailor.php');
    }
}
