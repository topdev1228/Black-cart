<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

class MarketingActivityExtensionAppErrorCode
{
    public const API_ERROR = 'API_ERROR';
    public const INSTALL_REQUIRED_ERROR = 'INSTALL_REQUIRED_ERROR';
    public const NOT_ONBOARDED_ERROR = 'NOT_ONBOARDED_ERROR';
    public const PLATFORM_ERROR = 'PLATFORM_ERROR';
    public const PLATFORM_ERROR_CRITICAL = 'PLATFORM_ERROR_CRITICAL';
    public const PLATFORM_ERROR_INFO = 'PLATFORM_ERROR_INFO';
    public const PLATFORM_ERROR_WARNING = 'PLATFORM_ERROR_WARNING';
    public const VALIDATION_ERROR = 'VALIDATION_ERROR';

    public static function endpoint(): string
    {
        return 'shopify-orders-2024-01';
    }

    public static function config(): string
    {
        return \Safe\realpath(__DIR__ . '/../../../../../../../sailor.php');
    }
}
