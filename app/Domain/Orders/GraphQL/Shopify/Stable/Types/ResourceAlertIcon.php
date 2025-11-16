<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

class ResourceAlertIcon
{
    public const CHECKMARK_CIRCLE = 'CHECKMARK_CIRCLE';
    public const INFORMATION_CIRCLE = 'INFORMATION_CIRCLE';
    public const MARKETING_MINOR_OFF = 'MARKETING_MINOR_OFF';
    public const MARKETING_MINOR_ON = 'MARKETING_MINOR_ON';

    public static function endpoint(): string
    {
        return 'shopify-orders-2024-01';
    }

    public static function config(): string
    {
        return \Safe\realpath(__DIR__ . '/../../../../../../../sailor.php');
    }
}
