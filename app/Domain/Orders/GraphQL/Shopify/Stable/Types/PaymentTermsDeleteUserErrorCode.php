<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

class PaymentTermsDeleteUserErrorCode
{
    public const PAYMENT_TERMS_DELETE_UNSUCCESSFUL = 'PAYMENT_TERMS_DELETE_UNSUCCESSFUL';

    public static function endpoint(): string
    {
        return 'shopify-orders-2024-01';
    }

    public static function config(): string
    {
        return \Safe\realpath(__DIR__ . '/../../../../../../../sailor.php');
    }
}
