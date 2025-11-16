<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

class MarketingChannel
{
    public const DISPLAY = 'DISPLAY';
    public const EMAIL = 'EMAIL';
    public const REFERRAL = 'REFERRAL';
    public const SEARCH = 'SEARCH';
    public const SOCIAL = 'SOCIAL';

    public static function endpoint(): string
    {
        return 'shopify-orders-2024-01';
    }

    public static function config(): string
    {
        return \Safe\realpath(__DIR__ . '/../../../../../../../sailor.php');
    }
}
