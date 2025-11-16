<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

class FulfillmentConstraintRuleCreateUserErrorCode
{
    public const CUSTOM_APP_FUNCTION_NOT_ELIGIBLE = 'CUSTOM_APP_FUNCTION_NOT_ELIGIBLE';
    public const FUNCTION_ALREADY_REGISTERED = 'FUNCTION_ALREADY_REGISTERED';
    public const FUNCTION_DOES_NOT_IMPLEMENT = 'FUNCTION_DOES_NOT_IMPLEMENT';
    public const FUNCTION_NOT_FOUND = 'FUNCTION_NOT_FOUND';
    public const FUNCTION_PENDING_DELETION = 'FUNCTION_PENDING_DELETION';
    public const INPUT_INVALID = 'INPUT_INVALID';

    public static function endpoint(): string
    {
        return 'shopify-orders-2024-01';
    }

    public static function config(): string
    {
        return \Safe\realpath(__DIR__ . '/../../../../../../../sailor.php');
    }
}
