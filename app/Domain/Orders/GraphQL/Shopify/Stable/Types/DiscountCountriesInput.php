<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

/**
 * @property array<string>|null $add
 * @property bool|null $includeRestOfWorld
 * @property array<string>|null $remove
 */
class DiscountCountriesInput extends \Spawnia\Sailor\ObjectLike
{
    /**
     * @param array<string>|null $add
     * @param bool|null $includeRestOfWorld
     * @param array<string>|null $remove
     */
    public static function make(
        $add = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $includeRestOfWorld = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $remove = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
    ): self {
        $instance = new self;

        if ($add !== self::UNDEFINED) {
            $instance->add = $add;
        }
        if ($includeRestOfWorld !== self::UNDEFINED) {
            $instance->includeRestOfWorld = $includeRestOfWorld;
        }
        if ($remove !== self::UNDEFINED) {
            $instance->remove = $remove;
        }

        return $instance;
    }

    protected function converters(): array
    {
        static $converters;

        return $converters ??= [
            'add' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\ListConverter(new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\EnumConverter))),
            'includeRestOfWorld' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\BooleanConverter),
            'remove' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\ListConverter(new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\EnumConverter))),
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
