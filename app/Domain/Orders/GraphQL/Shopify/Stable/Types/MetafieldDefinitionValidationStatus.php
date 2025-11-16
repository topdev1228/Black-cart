<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

class MetafieldDefinitionValidationStatus
{
    public const ALL_VALID = 'ALL_VALID';
    public const IN_PROGRESS = 'IN_PROGRESS';
    public const SOME_INVALID = 'SOME_INVALID';

    public static function endpoint(): string
    {
        return 'shopify-orders-2024-01';
    }

    public static function config(): string
    {
        return \Safe\realpath(__DIR__ . '/../../../../../../../sailor.php');
    }
}
