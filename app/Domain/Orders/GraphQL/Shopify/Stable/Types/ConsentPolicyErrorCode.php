<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

class ConsentPolicyErrorCode
{
    public const COUNTRY_CODE_REQUIRED = 'COUNTRY_CODE_REQUIRED';
    public const REGION_CODE_MUST_MATCH_COUNTRY_CODE = 'REGION_CODE_MUST_MATCH_COUNTRY_CODE';
    public const REGION_CODE_REQUIRED = 'REGION_CODE_REQUIRED';
    public const SHOPIFY_COOKIE_BANNER_NOT_DISABLED = 'SHOPIFY_COOKIE_BANNER_NOT_DISABLED';
    public const UNSUPORTED_CONSENT_POLICY = 'UNSUPORTED_CONSENT_POLICY';

    public static function endpoint(): string
    {
        return 'shopify-orders-2024-01';
    }

    public static function config(): string
    {
        return \Safe\realpath(__DIR__ . '/../../../../../../../sailor.php');
    }
}
