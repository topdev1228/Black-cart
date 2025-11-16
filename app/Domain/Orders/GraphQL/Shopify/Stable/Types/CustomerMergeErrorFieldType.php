<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

class CustomerMergeErrorFieldType
{
    public const COMPANY_CONTACT = 'COMPANY_CONTACT';
    public const CUSTOMER_PAYMENT_METHODS = 'CUSTOMER_PAYMENT_METHODS';
    public const DELETED_AT = 'DELETED_AT';
    public const GIFT_CARDS = 'GIFT_CARDS';
    public const MERGE_IN_PROGRESS = 'MERGE_IN_PROGRESS';
    public const MULTIPASS_IDENTIFIER = 'MULTIPASS_IDENTIFIER';
    public const PENDING_DATA_REQUEST = 'PENDING_DATA_REQUEST';
    public const REDACTED_AT = 'REDACTED_AT';
    public const STORE_CREDIT = 'STORE_CREDIT';
    public const SUBSCRIPTIONS = 'SUBSCRIPTIONS';

    public static function endpoint(): string
    {
        return 'shopify-orders-2024-01';
    }

    public static function config(): string
    {
        return \Safe\realpath(__DIR__ . '/../../../../../../../sailor.php');
    }
}
