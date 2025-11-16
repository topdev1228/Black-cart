<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

class ResourceFeedbackState
{
    public const ACCEPTED = 'ACCEPTED';
    public const REQUIRES_ACTION = 'REQUIRES_ACTION';

    public static function endpoint(): string
    {
        return 'shopify-orders-2024-01';
    }

    public static function config(): string
    {
        return \Safe\realpath(__DIR__ . '/../../../../../../../sailor.php');
    }
}
