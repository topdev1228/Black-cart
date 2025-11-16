<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

class FilesErrorCode
{
    public const ALT_VALUE_LIMIT_EXCEEDED = 'ALT_VALUE_LIMIT_EXCEEDED';
    public const BLANK_SEARCH = 'BLANK_SEARCH';
    public const FILENAME_ALREADY_EXISTS = 'FILENAME_ALREADY_EXISTS';
    public const FILE_DOES_NOT_EXIST = 'FILE_DOES_NOT_EXIST';
    public const FILE_LOCKED = 'FILE_LOCKED';
    public const INVALID = 'INVALID';
    public const INVALID_DUPLICATE_MODE_FOR_TYPE = 'INVALID_DUPLICATE_MODE_FOR_TYPE';
    public const INVALID_FILENAME = 'INVALID_FILENAME';
    public const INVALID_FILENAME_EXTENSION = 'INVALID_FILENAME_EXTENSION';
    public const INVALID_IMAGE_SOURCE_URL = 'INVALID_IMAGE_SOURCE_URL';
    public const INVALID_QUERY = 'INVALID_QUERY';
    public const MISMATCHED_FILENAME_AND_ORIGINAL_SOURCE = 'MISMATCHED_FILENAME_AND_ORIGINAL_SOURCE';
    public const MISSING_ARGUMENTS = 'MISSING_ARGUMENTS';
    public const MISSING_FILENAME_FOR_DUPLICATE_MODE_REPLACE = 'MISSING_FILENAME_FOR_DUPLICATE_MODE_REPLACE';
    public const NON_IMAGE_MEDIA_PER_SHOP_LIMIT_EXCEEDED = 'NON_IMAGE_MEDIA_PER_SHOP_LIMIT_EXCEEDED';
    public const NON_READY_STATE = 'NON_READY_STATE';
    public const PRODUCT_MEDIA_LIMIT_EXCEEDED = 'PRODUCT_MEDIA_LIMIT_EXCEEDED';
    public const TOO_MANY_ARGUMENTS = 'TOO_MANY_ARGUMENTS';
    public const UNACCEPTABLE_ASSET = 'UNACCEPTABLE_ASSET';
    public const UNACCEPTABLE_TRIAL_ASSET = 'UNACCEPTABLE_TRIAL_ASSET';
    public const UNACCEPTABLE_UNVERIFIED_TRIAL_ASSET = 'UNACCEPTABLE_UNVERIFIED_TRIAL_ASSET';
    public const UNSUPPORTED_MEDIA_TYPE_FOR_FILENAME_UPDATE = 'UNSUPPORTED_MEDIA_TYPE_FOR_FILENAME_UPDATE';

    public static function endpoint(): string
    {
        return 'shopify-orders-2024-01';
    }

    public static function config(): string
    {
        return \Safe\realpath(__DIR__ . '/../../../../../../../sailor.php');
    }
}
