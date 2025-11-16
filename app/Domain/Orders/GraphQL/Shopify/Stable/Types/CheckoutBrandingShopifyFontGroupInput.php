<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

/**
 * @property string $name
 * @property int|null $baseWeight
 * @property int|null $boldWeight
 * @property string|null $loadingStrategy
 */
class CheckoutBrandingShopifyFontGroupInput extends \Spawnia\Sailor\ObjectLike
{
    /**
     * @param string $name
     * @param int|null $baseWeight
     * @param int|null $boldWeight
     * @param string|null $loadingStrategy
     */
    public static function make(
        $name,
        $baseWeight = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $boldWeight = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $loadingStrategy = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
    ): self {
        $instance = new self;

        if ($name !== self::UNDEFINED) {
            $instance->name = $name;
        }
        if ($baseWeight !== self::UNDEFINED) {
            $instance->baseWeight = $baseWeight;
        }
        if ($boldWeight !== self::UNDEFINED) {
            $instance->boldWeight = $boldWeight;
        }
        if ($loadingStrategy !== self::UNDEFINED) {
            $instance->loadingStrategy = $loadingStrategy;
        }

        return $instance;
    }

    protected function converters(): array
    {
        static $converters;

        return $converters ??= [
            'name' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\StringConverter),
            'baseWeight' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\IntConverter),
            'boldWeight' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\IntConverter),
            'loadingStrategy' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\EnumConverter),
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
