<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

class CustomerEmailAddressOpenTrackingLevel
{
    public const OPTED_IN = 'OPTED_IN';
    public const OPTED_OUT = 'OPTED_OUT';
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
