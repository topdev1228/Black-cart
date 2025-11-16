<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

class OrderPaymentStatusResult
{
    public const AUTHORIZED = 'AUTHORIZED';
    public const CAPTURED = 'CAPTURED';
    public const ERROR = 'ERROR';
    public const INITIATED = 'INITIATED';
    public const PENDING = 'PENDING';
    public const PROCESSING = 'PROCESSING';
    public const PURCHASED = 'PURCHASED';
    public const REDIRECT_REQUIRED = 'REDIRECT_REQUIRED';
    public const REFUNDED = 'REFUNDED';
    public const RETRYABLE = 'RETRYABLE';
    public const SUCCESS = 'SUCCESS';
    public const UNKNOWN = 'UNKNOWN';
    public const VOIDED = 'VOIDED';

    public static function endpoint(): string
    {
        return 'shopify-orders-2024-01';
    }

    public static function config(): string
    {
        return \Safe\realpath(__DIR__ . '/../../../../../../../sailor.php');
    }
}
