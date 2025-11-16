<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

/**
 * @property array<string>|null $countryCodes
 * @property bool|null $includeRestOfWorld
 * @property bool|null $targetAllShippingLines
 */
class PriceRuleShippingEntitlementsInput extends \Spawnia\Sailor\ObjectLike
{
    /**
     * @param array<string>|null $countryCodes
     * @param bool|null $includeRestOfWorld
     * @param bool|null $targetAllShippingLines
     */
    public static function make(
        $countryCodes = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $includeRestOfWorld = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $targetAllShippingLines = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
    ): self {
        $instance = new self;

        if ($countryCodes !== self::UNDEFINED) {
            $instance->countryCodes = $countryCodes;
        }
        if ($includeRestOfWorld !== self::UNDEFINED) {
            $instance->includeRestOfWorld = $includeRestOfWorld;
        }
        if ($targetAllShippingLines !== self::UNDEFINED) {
            $instance->targetAllShippingLines = $targetAllShippingLines;
        }

        return $instance;
    }

    protected function converters(): array
    {
        static $converters;

        return $converters ??= [
            'countryCodes' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\ListConverter(new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\EnumConverter))),
            'includeRestOfWorld' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\BooleanConverter),
            'targetAllShippingLines' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\BooleanConverter),
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
