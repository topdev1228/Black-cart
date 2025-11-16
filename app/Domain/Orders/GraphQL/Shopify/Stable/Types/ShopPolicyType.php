<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

class ShopPolicyType
{
    public const CONTACT_INFORMATION = 'CONTACT_INFORMATION';
    public const LEGAL_NOTICE = 'LEGAL_NOTICE';
    public const PRIVACY_POLICY = 'PRIVACY_POLICY';
    public const REFUND_POLICY = 'REFUND_POLICY';
    public const SHIPPING_POLICY = 'SHIPPING_POLICY';
    public const SUBSCRIPTION_POLICY = 'SUBSCRIPTION_POLICY';
    public const TERMS_OF_SALE = 'TERMS_OF_SALE';
    public const TERMS_OF_SERVICE = 'TERMS_OF_SERVICE';

    public static function endpoint(): string
    {
        return 'shopify-orders-2024-01';
    }

    public static function config(): string
    {
        return \Safe\realpath(__DIR__ . '/../../../../../../../sailor.php');
    }
}
