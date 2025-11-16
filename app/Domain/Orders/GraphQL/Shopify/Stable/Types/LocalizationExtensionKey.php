<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

class LocalizationExtensionKey
{
    public const SHIPPING_CREDENTIAL_BR = 'SHIPPING_CREDENTIAL_BR';
    public const SHIPPING_CREDENTIAL_CN = 'SHIPPING_CREDENTIAL_CN';
    public const SHIPPING_CREDENTIAL_KR = 'SHIPPING_CREDENTIAL_KR';
    public const TAX_CREDENTIAL_BR = 'TAX_CREDENTIAL_BR';
    public const TAX_CREDENTIAL_IT = 'TAX_CREDENTIAL_IT';
    public const TAX_EMAIL_IT = 'TAX_EMAIL_IT';

    public static function endpoint(): string
    {
        return 'shopify-orders-2024-01';
    }

    public static function config(): string
    {
        return \Safe\realpath(__DIR__ . '/../../../../../../../sailor.php');
    }
}
