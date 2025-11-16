<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

class LocationActivateUserErrorCode
{
    public const GENERIC_ERROR = 'GENERIC_ERROR';
    public const HAS_NON_UNIQUE_NAME = 'HAS_NON_UNIQUE_NAME';
    public const HAS_ONGOING_RELOCATION = 'HAS_ONGOING_RELOCATION';
    public const LOCATION_LIMIT = 'LOCATION_LIMIT';
    public const LOCATION_NOT_FOUND = 'LOCATION_NOT_FOUND';

    public static function endpoint(): string
    {
        return 'shopify-orders-2024-01';
    }

    public static function config(): string
    {
        return \Safe\realpath(__DIR__ . '/../../../../../../../sailor.php');
    }
}
