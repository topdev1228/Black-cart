<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

class HydrogenStorefrontCreateUserErrorCode
{
    public const BLANK = 'BLANK';
    public const CUSTOM_STOREFRONTS_NOT_INSTALLED = 'CUSTOM_STOREFRONTS_NOT_INSTALLED';
    public const FAILED_TO_CREATE = 'FAILED_TO_CREATE';
    public const HYDROGEN_NOT_SUPPORTED_ON_PLAN = 'HYDROGEN_NOT_SUPPORTED_ON_PLAN';
    public const INVALID = 'INVALID';
    public const JOB_NOT_ENQUEUED = 'JOB_NOT_ENQUEUED';
    public const TITLE_ALREADY_EXISTS = 'TITLE_ALREADY_EXISTS';
    public const TITLE_TOO_LONG = 'TITLE_TOO_LONG';

    public static function endpoint(): string
    {
        return 'shopify-orders-2024-01';
    }

    public static function config(): string
    {
        return \Safe\realpath(__DIR__ . '/../../../../../../../sailor.php');
    }
}
