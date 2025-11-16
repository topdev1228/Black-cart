<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Operations\RefundCreate\RefundCreate\UserErrors;

/**
 * @property string $message
 * @property string $__typename
 * @property array<int, string>|null $field
 */
class UserError extends \Spawnia\Sailor\ObjectLike
{
    /**
     * @param string $message
     * @param array<int, string>|null $field
     */
    public static function make(
        $message,
        $field = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
    ): self {
        $instance = new self;

        if ($message !== self::UNDEFINED) {
            $instance->message = $message;
        }
        $instance->__typename = 'UserError';
        if ($field !== self::UNDEFINED) {
            $instance->field = $field;
        }

        return $instance;
    }

    protected function converters(): array
    {
        static $converters;

        return $converters ??= [
            'message' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\StringConverter),
            '__typename' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\StringConverter),
            'field' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\ListConverter(new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\StringConverter))),
        ];
    }

    public static function endpoint(): string
    {
        return 'shopify-orders-2024-01';
    }

    public static function config(): string
    {
        return \Safe\realpath(__DIR__ . '/../../../../../../../../../../sailor.php');
    }
}
