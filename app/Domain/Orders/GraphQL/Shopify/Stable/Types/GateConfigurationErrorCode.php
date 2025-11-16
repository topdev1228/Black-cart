<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

class GateConfigurationErrorCode
{
    public const APP_NOT_AUTHORIZED = 'APP_NOT_AUTHORIZED';
    public const BLANK = 'BLANK';
    public const CAPABILITY_VIOLATION = 'CAPABILITY_VIOLATION';
    public const DISALLOWED_OWNER_TYPE = 'DISALLOWED_OWNER_TYPE';
    public const GATE_CONFIGURATION_RECORD_NOT_FOUND = 'GATE_CONFIGURATION_RECORD_NOT_FOUND';
    public const INCLUSION = 'INCLUSION';
    public const INVALID = 'INVALID';
    public const INVALID_JSON = 'INVALID_JSON';
    public const INVALID_TYPE = 'INVALID_TYPE';
    public const INVALID_VALUE = 'INVALID_VALUE';
    public const PRESENT = 'PRESENT';
    public const TAKEN = 'TAKEN';
    public const TOO_LONG = 'TOO_LONG';
    public const TOO_SHORT = 'TOO_SHORT';
    public const UNSTRUCTURED_RESERVED_NAMESPACE = 'UNSTRUCTURED_RESERVED_NAMESPACE';

    public static function endpoint(): string
    {
        return 'shopify-orders-2024-01';
    }

    public static function config(): string
    {
        return \Safe\realpath(__DIR__ . '/../../../../../../../sailor.php');
    }
}
