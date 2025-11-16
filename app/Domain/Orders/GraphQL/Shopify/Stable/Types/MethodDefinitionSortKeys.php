<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

class MethodDefinitionSortKeys
{
    public const ID = 'ID';
    public const RATE_PROVIDER_TYPE = 'RATE_PROVIDER_TYPE';
    public const RELEVANCE = 'RELEVANCE';

    public static function endpoint(): string
    {
        return 'shopify-orders-2024-01';
    }

    public static function config(): string
    {
        return \Safe\realpath(__DIR__ . '/../../../../../../../sailor.php');
    }
}
