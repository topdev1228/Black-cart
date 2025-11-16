<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

class CompanyLocationSortKeys
{
    public const COMPANY_AND_LOCATION_NAME = 'COMPANY_AND_LOCATION_NAME';
    public const COMPANY_ID = 'COMPANY_ID';
    public const CREATED_AT = 'CREATED_AT';
    public const ID = 'ID';
    public const NAME = 'NAME';
    public const RELEVANCE = 'RELEVANCE';
    public const UPDATED_AT = 'UPDATED_AT';

    public static function endpoint(): string
    {
        return 'shopify-orders-2024-01';
    }

    public static function config(): string
    {
        return \Safe\realpath(__DIR__ . '/../../../../../../../sailor.php');
    }
}
