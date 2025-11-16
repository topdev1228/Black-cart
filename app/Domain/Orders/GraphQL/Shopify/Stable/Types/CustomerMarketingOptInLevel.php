<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

class CustomerMarketingOptInLevel
{
    public const CONFIRMED_OPT_IN = 'CONFIRMED_OPT_IN';
    public const SINGLE_OPT_IN = 'SINGLE_OPT_IN';
    public const UNKNOWN = 'UNKNOWN';

    public static function endpoint(): string
    {
        return 'shopify-orders-2024-01';
    }

    public static function config(): string
    {
        return \Safe\realpath(__DIR__ . '/../../../../../../../sailor.php');
    }
}
