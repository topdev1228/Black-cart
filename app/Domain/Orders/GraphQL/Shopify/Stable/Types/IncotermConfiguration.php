<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

class IncotermConfiguration
{
    public const DAP = 'DAP';
    public const DDP = 'DDP';
    public const DDU = 'DDU';
    public const UNSUPPORTED = 'UNSUPPORTED';

    public static function endpoint(): string
    {
        return 'shopify-orders-2024-01';
    }

    public static function config(): string
    {
        return \Safe\realpath(__DIR__ . '/../../../../../../../sailor.php');
    }
}
