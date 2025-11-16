<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

class ProductBundleComponentOptionSelectionStatus
{
    public const DESELECTED = 'DESELECTED';
    public const NEW = 'NEW';
    public const SELECTED = 'SELECTED';
    public const UNAVAILABLE = 'UNAVAILABLE';

    public static function endpoint(): string
    {
        return 'shopify-orders-2024-01';
    }

    public static function config(): string
    {
        return \Safe\realpath(__DIR__ . '/../../../../../../../sailor.php');
    }
}
