<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Operations\RefundCreate;

class RefundCreateErrorFreeResult extends \Spawnia\Sailor\ErrorFreeResult
{
    public RefundCreate $data;

    public static function endpoint(): string
    {
        return 'shopify-orders-2024-01';
    }

    public static function config(): string
    {
        return \Safe\realpath(__DIR__ . '/../../../../../../../../sailor.php');
    }
}
