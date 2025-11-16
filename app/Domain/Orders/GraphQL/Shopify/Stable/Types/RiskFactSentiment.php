<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

class RiskFactSentiment
{
    public const NEGATIVE = 'NEGATIVE';
    public const NEUTRAL = 'NEUTRAL';
    public const POSITIVE = 'POSITIVE';

    public static function endpoint(): string
    {
        return 'shopify-orders-2024-01';
    }

    public static function config(): string
    {
        return \Safe\realpath(__DIR__ . '/../../../../../../../sailor.php');
    }
}
