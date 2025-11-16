<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

class DigitalWallet
{
    public const AMAZON_PAY = 'AMAZON_PAY';
    public const ANDROID_PAY = 'ANDROID_PAY';
    public const APPLE_PAY = 'APPLE_PAY';
    public const FACEBOOK_PAY = 'FACEBOOK_PAY';
    public const GOOGLE_PAY = 'GOOGLE_PAY';
    public const SHOPIFY_PAY = 'SHOPIFY_PAY';

    public static function endpoint(): string
    {
        return 'shopify-orders-2024-01';
    }

    public static function config(): string
    {
        return \Safe\realpath(__DIR__ . '/../../../../../../../sailor.php');
    }
}
