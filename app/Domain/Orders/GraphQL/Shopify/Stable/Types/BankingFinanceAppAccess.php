<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

class BankingFinanceAppAccess
{
    public const MONEY_MOVEMENT_BLOCKED_MFA = 'MONEY_MOVEMENT_BLOCKED_MFA';
    public const MONEY_MOVEMENT_RESTRICTED = 'MONEY_MOVEMENT_RESTRICTED';
    public const MOVE_MONEY = 'MOVE_MONEY';
    public const READ_ACCESS = 'READ_ACCESS';

    public static function endpoint(): string
    {
        return 'shopify-orders-2024-01';
    }

    public static function config(): string
    {
        return \Safe\realpath(__DIR__ . '/../../../../../../../sailor.php');
    }
}
