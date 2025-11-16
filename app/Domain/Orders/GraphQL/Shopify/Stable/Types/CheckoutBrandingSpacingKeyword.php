<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

class CheckoutBrandingSpacingKeyword
{
    public const BASE = 'BASE';
    public const LARGE = 'LARGE';
    public const LARGE_100 = 'LARGE_100';
    public const LARGE_200 = 'LARGE_200';
    public const LARGE_300 = 'LARGE_300';
    public const LARGE_400 = 'LARGE_400';
    public const LARGE_500 = 'LARGE_500';
    public const NONE = 'NONE';
    public const SMALL = 'SMALL';
    public const SMALL_100 = 'SMALL_100';
    public const SMALL_200 = 'SMALL_200';
    public const SMALL_300 = 'SMALL_300';
    public const SMALL_400 = 'SMALL_400';
    public const SMALL_500 = 'SMALL_500';

    public static function endpoint(): string
    {
        return 'shopify-orders-2024-01';
    }

    public static function config(): string
    {
        return \Safe\realpath(__DIR__ . '/../../../../../../../sailor.php');
    }
}
