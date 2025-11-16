<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

class AppRevenueAttributionType
{
    public const APPLICATION_PURCHASE = 'APPLICATION_PURCHASE';
    public const APPLICATION_SUBSCRIPTION = 'APPLICATION_SUBSCRIPTION';
    public const APPLICATION_USAGE = 'APPLICATION_USAGE';
    public const OTHER = 'OTHER';

    public static function endpoint(): string
    {
        return 'shopify-orders-2024-01';
    }

    public static function config(): string
    {
        return \Safe\realpath(__DIR__ . '/../../../../../../../sailor.php');
    }
}
