<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

class CustomerSmsMarketingConsentErrorCode
{
    public const INCLUSION = 'INCLUSION';
    public const INTERNAL_ERROR = 'INTERNAL_ERROR';
    public const INVALID = 'INVALID';
    public const MISSING_ARGUMENT = 'MISSING_ARGUMENT';

    public static function endpoint(): string
    {
        return 'shopify-orders-2024-01';
    }

    public static function config(): string
    {
        return \Safe\realpath(__DIR__ . '/../../../../../../../sailor.php');
    }
}
