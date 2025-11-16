<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

/**
 * @property mixed $expectedAt
 * @property string $fromName
 * @property string $toName
 */
class InventoryScheduledChangeInput extends \Spawnia\Sailor\ObjectLike
{
    /**
     * @param mixed $expectedAt
     * @param string $fromName
     * @param string $toName
     */
    public static function make($expectedAt, $fromName, $toName): self
    {
        $instance = new self;

        if ($expectedAt !== self::UNDEFINED) {
            $instance->expectedAt = $expectedAt;
        }
        if ($fromName !== self::UNDEFINED) {
            $instance->fromName = $fromName;
        }
        if ($toName !== self::UNDEFINED) {
            $instance->toName = $toName;
        }

        return $instance;
    }

    protected function converters(): array
    {
        static $converters;

        return $converters ??= [
            'expectedAt' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\ScalarConverter),
            'fromName' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\StringConverter),
            'toName' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\StringConverter),
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
