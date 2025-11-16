<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

class DelegateAccessTokenDestroyUserErrorCode
{
    public const ACCESS_DENIED = 'ACCESS_DENIED';
    public const ACCESS_TOKEN_NOT_FOUND = 'ACCESS_TOKEN_NOT_FOUND';
    public const CAN_ONLY_DELETE_DELEGATE_TOKENS = 'CAN_ONLY_DELETE_DELEGATE_TOKENS';
    public const PERSISTENCE_FAILED = 'PERSISTENCE_FAILED';

    public static function endpoint(): string
    {
        return 'shopify-orders-2024-01';
    }

    public static function config(): string
    {
        return \Safe\realpath(__DIR__ . '/../../../../../../../sailor.php');
    }
}
