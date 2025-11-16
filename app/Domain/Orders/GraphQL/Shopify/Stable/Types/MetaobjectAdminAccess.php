<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

class MetaobjectAdminAccess
{
    public const MERCHANT_READ = 'MERCHANT_READ';
    public const MERCHANT_READ_WRITE = 'MERCHANT_READ_WRITE';
    public const PRIVATE = 'PRIVATE';
    public const PUBLIC_READ = 'PUBLIC_READ';
    public const PUBLIC_READ_WRITE = 'PUBLIC_READ_WRITE';

    public static function endpoint(): string
    {
        return 'shopify-orders-2024-01';
    }

    public static function config(): string
    {
        return \Safe\realpath(__DIR__ . '/../../../../../../../sailor.php');
    }
}
