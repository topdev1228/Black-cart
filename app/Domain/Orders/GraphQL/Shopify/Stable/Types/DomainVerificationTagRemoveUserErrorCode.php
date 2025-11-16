<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

class DomainVerificationTagRemoveUserErrorCode
{
    public const CODE_NOT_FOUND = 'CODE_NOT_FOUND';
    public const INVALID = 'INVALID';

    public static function endpoint(): string
    {
        return 'shopify-orders-2024-01';
    }

    public static function config(): string
    {
        return \Safe\realpath(__DIR__ . '/../../../../../../../sailor.php');
    }
}
