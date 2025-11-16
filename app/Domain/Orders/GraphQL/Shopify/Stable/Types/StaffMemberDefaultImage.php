<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

class StaffMemberDefaultImage
{
    public const DEFAULT = 'DEFAULT';
    public const NOT_FOUND = 'NOT_FOUND';
    public const TRANSPARENT = 'TRANSPARENT';

    public static function endpoint(): string
    {
        return 'shopify-orders-2024-01';
    }

    public static function config(): string
    {
        return \Safe\realpath(__DIR__ . '/../../../../../../../sailor.php');
    }
}
