<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

class StandardMetafieldDefinitionsEnableUserErrorCode
{
    public const INVALID = 'INVALID';
    public const LESS_THAN_OR_EQUAL_TO = 'LESS_THAN_OR_EQUAL_TO';
    public const LIMIT_EXCEEDED = 'LIMIT_EXCEEDED';
    public const TAKEN = 'TAKEN';
    public const TEMPLATE_NOT_FOUND = 'TEMPLATE_NOT_FOUND';
    public const UNSTRUCTURED_ALREADY_EXISTS = 'UNSTRUCTURED_ALREADY_EXISTS';

    public static function endpoint(): string
    {
        return 'shopify-orders-2024-01';
    }

    public static function config(): string
    {
        return \Safe\realpath(__DIR__ . '/../../../../../../../sailor.php');
    }
}
