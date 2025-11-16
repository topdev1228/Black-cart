<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

class CompanyContactSortKeys
{
    public const COMPANY_ID = 'COMPANY_ID';
    public const CREATED_AT = 'CREATED_AT';
    public const EMAIL = 'EMAIL';
    public const ID = 'ID';
    public const NAME = 'NAME';
    public const NAME_EMAIL = 'NAME_EMAIL';
    public const RELEVANCE = 'RELEVANCE';
    public const TITLE = 'TITLE';
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
