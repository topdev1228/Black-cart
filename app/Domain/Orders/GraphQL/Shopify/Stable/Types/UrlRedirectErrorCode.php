<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

class UrlRedirectErrorCode
{
    public const CREATE_FAILED = 'CREATE_FAILED';
    public const DELETE_FAILED = 'DELETE_FAILED';
    public const DOES_NOT_EXIST = 'DOES_NOT_EXIST';
    public const UPDATE_FAILED = 'UPDATE_FAILED';

    public static function endpoint(): string
    {
        return 'shopify-orders-2024-01';
    }

    public static function config(): string
    {
        return \Safe\realpath(__DIR__ . '/../../../../../../../sailor.php');
    }
}
