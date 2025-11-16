<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

class GateSubjectErrorCode
{
    public const GATE_CONFIGURATION_NOT_FOUND = 'GATE_CONFIGURATION_NOT_FOUND';
    public const GATE_SUBJECT_NOT_FOUND = 'GATE_SUBJECT_NOT_FOUND';
    public const INVALID = 'INVALID';
    public const INVALID_API_CLIENT = 'INVALID_API_CLIENT';
    public const INVALID_SUBJECT = 'INVALID_SUBJECT';
    public const SUBJECT_NOT_FOUND = 'SUBJECT_NOT_FOUND';
    public const TAKEN = 'TAKEN';

    public static function endpoint(): string
    {
        return 'shopify-orders-2024-01';
    }

    public static function config(): string
    {
        return \Safe\realpath(__DIR__ . '/../../../../../../../sailor.php');
    }
}
