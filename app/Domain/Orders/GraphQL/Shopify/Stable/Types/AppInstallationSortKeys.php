<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

class AppInstallationSortKeys
{
    public const APP_TITLE = 'APP_TITLE';
    public const ID = 'ID';
    public const INSTALLED_AT = 'INSTALLED_AT';
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
