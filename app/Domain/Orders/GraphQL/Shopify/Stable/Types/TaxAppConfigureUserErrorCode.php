<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

class TaxAppConfigureUserErrorCode
{
    public const TAX_PARTNER_ALREADY_ACTIVE = 'TAX_PARTNER_ALREADY_ACTIVE';
    public const TAX_PARTNER_NOT_FOUND = 'TAX_PARTNER_NOT_FOUND';
    public const TAX_PARTNER_STATE_UPDATE_FAILED = 'TAX_PARTNER_STATE_UPDATE_FAILED';

    public static function endpoint(): string
    {
        return 'shopify-orders-2024-01';
    }

    public static function config(): string
    {
        return \Safe\realpath(__DIR__ . '/../../../../../../../sailor.php');
    }
}
