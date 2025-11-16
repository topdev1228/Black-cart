<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

class DelegateAccessTokenCreateUserErrorCode
{
    public const DELEGATE_ACCESS_TOKEN = 'DELEGATE_ACCESS_TOKEN';
    public const EMPTY_ACCESS_SCOPE = 'EMPTY_ACCESS_SCOPE';
    public const EXPIRES_AFTER_PARENT = 'EXPIRES_AFTER_PARENT';
    public const NEGATIVE_EXPIRES_IN = 'NEGATIVE_EXPIRES_IN';
    public const PERSISTENCE_FAILED = 'PERSISTENCE_FAILED';
    public const REFRESH_TOKEN = 'REFRESH_TOKEN';
    public const UNKNOWN_SCOPES = 'UNKNOWN_SCOPES';

    public static function endpoint(): string
    {
        return 'shopify-orders-2024-01';
    }

    public static function config(): string
    {
        return \Safe\realpath(__DIR__ . '/../../../../../../../sailor.php');
    }
}
