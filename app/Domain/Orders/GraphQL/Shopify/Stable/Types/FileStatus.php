<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

class FileStatus
{
    public const FAILED = 'FAILED';
    public const PROCESSING = 'PROCESSING';
    public const READY = 'READY';
    public const UPLOADED = 'UPLOADED';

    public static function endpoint(): string
    {
        return 'shopify-orders-2024-01';
    }

    public static function config(): string
    {
        return \Safe\realpath(__DIR__ . '/../../../../../../../sailor.php');
    }
}
