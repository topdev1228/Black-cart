<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

class SmartGridLayoutUserErrorCode
{
    public const BLANK = 'BLANK';
    public const BLANK_PAGE = 'BLANK_PAGE';
    public const DELETE_NOT_ALLOWED = 'DELETE_NOT_ALLOWED';
    public const INVALID_TILE = 'INVALID_TILE';
    public const INVALID_TILE_REFERENCEABLE = 'INVALID_TILE_REFERENCEABLE';
    public const INVALID_TILE_TYPE = 'INVALID_TILE_TYPE';
    public const LOCATION_NOT_FOUND = 'LOCATION_NOT_FOUND';
    public const NOT_FOUND = 'NOT_FOUND';
    public const TAKEN = 'TAKEN';
    public const TILES_DOES_NOT_MATCH_JSON_SCHEMA = 'TILES_DOES_NOT_MATCH_JSON_SCHEMA';

    public static function endpoint(): string
    {
        return 'shopify-orders-2024-01';
    }

    public static function config(): string
    {
        return \Safe\realpath(__DIR__ . '/../../../../../../../sailor.php');
    }
}
