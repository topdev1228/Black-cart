<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

class PerformanceEventType
{
    public const APP_INSTALL = 'APP_INSTALL';
    public const APP_UNINSTALL = 'APP_UNINSTALL';
    public const THEME_LIVE_EDIT = 'THEME_LIVE_EDIT';
    public const THEME_PUBLICATION = 'THEME_PUBLICATION';

    public static function endpoint(): string
    {
        return 'shopify-orders-2024-01';
    }

    public static function config(): string
    {
        return \Safe\realpath(__DIR__ . '/../../../../../../../sailor.php');
    }
}
