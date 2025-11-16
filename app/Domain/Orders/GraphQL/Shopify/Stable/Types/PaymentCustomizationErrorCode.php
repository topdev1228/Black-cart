<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

class PaymentCustomizationErrorCode
{
    public const CUSTOM_APP_FUNCTION_NOT_ELIGIBLE = 'CUSTOM_APP_FUNCTION_NOT_ELIGIBLE';
    public const FUNCTION_DOES_NOT_IMPLEMENT = 'FUNCTION_DOES_NOT_IMPLEMENT';
    public const FUNCTION_ID_CANNOT_BE_CHANGED = 'FUNCTION_ID_CANNOT_BE_CHANGED';
    public const FUNCTION_NOT_FOUND = 'FUNCTION_NOT_FOUND';
    public const FUNCTION_PENDING_DELETION = 'FUNCTION_PENDING_DELETION';
    public const INVALID = 'INVALID';
    public const INVALID_METAFIELDS = 'INVALID_METAFIELDS';
    public const MAXIMUM_ACTIVE_PAYMENT_CUSTOMIZATIONS = 'MAXIMUM_ACTIVE_PAYMENT_CUSTOMIZATIONS';
    public const PAYMENT_CUSTOMIZATION_FUNCTION_NOT_ELIGIBLE = 'PAYMENT_CUSTOMIZATION_FUNCTION_NOT_ELIGIBLE';
    public const PAYMENT_CUSTOMIZATION_NOT_FOUND = 'PAYMENT_CUSTOMIZATION_NOT_FOUND';
    public const REQUIRED_INPUT_FIELD = 'REQUIRED_INPUT_FIELD';

    public static function endpoint(): string
    {
        return 'shopify-orders-2024-01';
    }

    public static function config(): string
    {
        return \Safe\realpath(__DIR__ . '/../../../../../../../sailor.php');
    }
}
