<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

class ShopifyPaymentsDisputeEvidenceFileType
{
    public const CANCELLATION_POLICY_FILE = 'CANCELLATION_POLICY_FILE';
    public const CUSTOMER_COMMUNICATION_FILE = 'CUSTOMER_COMMUNICATION_FILE';
    public const REFUND_POLICY_FILE = 'REFUND_POLICY_FILE';
    public const SERVICE_DOCUMENTATION_FILE = 'SERVICE_DOCUMENTATION_FILE';
    public const SHIPPING_DOCUMENTATION_FILE = 'SHIPPING_DOCUMENTATION_FILE';
    public const UNCATEGORIZED_FILE = 'UNCATEGORIZED_FILE';

    public static function endpoint(): string
    {
        return 'shopify-orders-2024-01';
    }

    public static function config(): string
    {
        return \Safe\realpath(__DIR__ . '/../../../../../../../sailor.php');
    }
}
