<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

class IncotermReason
{
    public const BUYER_CONFIGURED = 'BUYER_CONFIGURED';
    public const DEFAULT_DUTIES_AND_TAXES = 'DEFAULT_DUTIES_AND_TAXES';
    public const DUTY_AND_TAX_INCLUSIVE_PRICING = 'DUTY_AND_TAX_INCLUSIVE_PRICING';
    public const DUTY_INCLUSIVE_PRICING = 'DUTY_INCLUSIVE_PRICING';
    public const ERROR_OCCURED = 'ERROR_OCCURED';
    public const FLOW_CONFIGURED = 'FLOW_CONFIGURED';
    public const LOW_VALUE_GOODS_TAXES_APPLY = 'LOW_VALUE_GOODS_TAXES_APPLY';
    public const PRE_CONFIGURED = 'PRE_CONFIGURED';
    public const TAX_CALCULATION_FALLBACK_MISSING_DUTIES = 'TAX_CALCULATION_FALLBACK_MISSING_DUTIES';
    public const UNSUPPORTED = 'UNSUPPORTED';
    public const UNSUPPORTED_REGION = 'UNSUPPORTED_REGION';

    public static function endpoint(): string
    {
        return 'shopify-orders-2024-01';
    }

    public static function config(): string
    {
        return \Safe\realpath(__DIR__ . '/../../../../../../../sailor.php');
    }
}
