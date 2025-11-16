<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

class MarketingActivityStatusBadgeType
{
    public const ATTENTION = 'ATTENTION';
    public const DEFAULT = 'DEFAULT';
    public const INFO = 'INFO';
    public const SUCCESS = 'SUCCESS';
    public const WARNING = 'WARNING';

    public static function endpoint(): string
    {
        return 'shopify-orders-2024-01';
    }

    public static function config(): string
    {
        return \Safe\realpath(__DIR__ . '/../../../../../../../sailor.php');
    }
}
