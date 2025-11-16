<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

class ReverseFulfillmentOrderThirdPartyConfirmationStatus
{
    public const ACCEPTED = 'ACCEPTED';
    public const CANCEL_ACCEPTED = 'CANCEL_ACCEPTED';
    public const CANCEL_REJECTED = 'CANCEL_REJECTED';
    public const PENDING_ACCEPTANCE = 'PENDING_ACCEPTANCE';
    public const PENDING_CANCELATION = 'PENDING_CANCELATION';
    public const REJECTED = 'REJECTED';

    public static function endpoint(): string
    {
        return 'shopify-orders-2024-01';
    }

    public static function config(): string
    {
        return \Safe\realpath(__DIR__ . '/../../../../../../../sailor.php');
    }
}
