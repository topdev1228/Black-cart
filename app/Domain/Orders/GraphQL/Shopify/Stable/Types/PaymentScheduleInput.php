<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

/**
 * @property mixed|null $dueAt
 * @property mixed|null $issuedAt
 */
class PaymentScheduleInput extends \Spawnia\Sailor\ObjectLike
{
    /**
     * @param mixed|null $dueAt
     * @param mixed|null $issuedAt
     */
    public static function make(
        $dueAt = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $issuedAt = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
    ): self {
        $instance = new self;

        if ($dueAt !== self::UNDEFINED) {
            $instance->dueAt = $dueAt;
        }
        if ($issuedAt !== self::UNDEFINED) {
            $instance->issuedAt = $issuedAt;
        }

        return $instance;
    }

    protected function converters(): array
    {
        static $converters;

        return $converters ??= [
            'dueAt' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\ScalarConverter),
            'issuedAt' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\ScalarConverter),
        ];
    }

    public static function endpoint(): string
    {
        return 'shopify-orders-2024-01';
    }

    public static function config(): string
    {
        return \Safe\realpath(__DIR__ . '/../../../../../../../sailor.php');
    }
}
