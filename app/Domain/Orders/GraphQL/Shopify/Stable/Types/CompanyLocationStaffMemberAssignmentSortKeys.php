<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

class CompanyLocationStaffMemberAssignmentSortKeys
{
    public const CREATED_AT = 'CREATED_AT';
    public const ID = 'ID';
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
