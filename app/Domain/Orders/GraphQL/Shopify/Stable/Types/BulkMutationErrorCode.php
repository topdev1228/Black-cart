<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

class BulkMutationErrorCode
{
    public const INTERNAL_FILE_SERVER_ERROR = 'INTERNAL_FILE_SERVER_ERROR';
    public const INVALID_MUTATION = 'INVALID_MUTATION';
    public const INVALID_STAGED_UPLOAD_FILE = 'INVALID_STAGED_UPLOAD_FILE';
    public const NO_SUCH_FILE = 'NO_SUCH_FILE';
    public const OPERATION_IN_PROGRESS = 'OPERATION_IN_PROGRESS';

    public static function endpoint(): string
    {
        return 'shopify-orders-2024-01';
    }

    public static function config(): string
    {
        return \Safe\realpath(__DIR__ . '/../../../../../../../sailor.php');
    }
}
