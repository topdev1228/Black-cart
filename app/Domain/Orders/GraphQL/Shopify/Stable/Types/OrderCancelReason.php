<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

class OrderCancelReason
{
    public const CUSTOMER = 'CUSTOMER';
    public const DECLINED = 'DECLINED';
    public const FRAUD = 'FRAUD';
    public const INVENTORY = 'INVENTORY';
    public const OTHER = 'OTHER';
    public const STAFF = 'STAFF';

    public static function endpoint(): string
    {
        return 'shopify-orders-2024-01';
    }

    public static function config(): string
    {
        return \Safe\realpath(__DIR__ . '/../../../../../../../sailor.php');
    }
}
