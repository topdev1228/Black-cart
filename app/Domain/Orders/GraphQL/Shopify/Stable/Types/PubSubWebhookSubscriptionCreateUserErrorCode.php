<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

class PubSubWebhookSubscriptionCreateUserErrorCode
{
    public const INVALID_PARAMETERS = 'INVALID_PARAMETERS';
    public const TAKEN = 'TAKEN';

    public static function endpoint(): string
    {
        return 'shopify-orders-2024-01';
    }

    public static function config(): string
    {
        return \Safe\realpath(__DIR__ . '/../../../../../../../sailor.php');
    }
}
