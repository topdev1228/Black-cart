<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

class DisputeStatus
{
    public const ACCEPTED = 'ACCEPTED';
    public const CHARGE_REFUNDED = 'CHARGE_REFUNDED';
    public const LOST = 'LOST';
    public const NEEDS_RESPONSE = 'NEEDS_RESPONSE';
    public const UNDER_REVIEW = 'UNDER_REVIEW';
    public const WON = 'WON';

    public static function endpoint(): string
    {
        return 'shopify-orders-2024-01';
    }

    public static function config(): string
    {
        return \Safe\realpath(__DIR__ . '/../../../../../../../sailor.php');
    }
}
