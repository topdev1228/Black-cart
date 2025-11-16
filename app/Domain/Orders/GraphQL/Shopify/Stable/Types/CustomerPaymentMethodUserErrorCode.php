<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

class CustomerPaymentMethodUserErrorCode
{
    public const INVALID = 'INVALID';
    public const PRESENT = 'PRESENT';
    public const TAKEN = 'TAKEN';

    public static function endpoint(): string
    {
        return 'shopify-orders-2024-01';
    }

    public static function config(): string
    {
        return \Safe\realpath(__DIR__ . '/../../../../../../../sailor.php');
    }
}
