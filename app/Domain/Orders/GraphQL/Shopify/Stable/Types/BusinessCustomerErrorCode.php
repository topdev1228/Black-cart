<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

class BusinessCustomerErrorCode
{
    public const BLANK = 'BLANK';
    public const FAILED_TO_DELETE = 'FAILED_TO_DELETE';
    public const INTERNAL_ERROR = 'INTERNAL_ERROR';
    public const INVALID = 'INVALID';
    public const INVALID_INPUT = 'INVALID_INPUT';
    public const LIMIT_REACHED = 'LIMIT_REACHED';
    public const NO_INPUT = 'NO_INPUT';
    public const REQUIRED = 'REQUIRED';
    public const RESOURCE_NOT_FOUND = 'RESOURCE_NOT_FOUND';
    public const TAKEN = 'TAKEN';
    public const TOO_LONG = 'TOO_LONG';
    public const UNEXPECTED_TYPE = 'UNEXPECTED_TYPE';

    public static function endpoint(): string
    {
        return 'shopify-orders-2024-01';
    }

    public static function config(): string
    {
        return \Safe\realpath(__DIR__ . '/../../../../../../../sailor.php');
    }
}
