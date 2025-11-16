<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

/**
 * @property array<string>|null $add
 * @property string|null $removeRegex
 */
class HydrogenStorefrontCustomerApplicationUrlsReplaceUriInput extends \Spawnia\Sailor\ObjectLike
{
    /**
     * @param array<string>|null $add
     * @param string|null $removeRegex
     */
    public static function make(
        $add = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $removeRegex = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
    ): self {
        $instance = new self;

        if ($add !== self::UNDEFINED) {
            $instance->add = $add;
        }
        if ($removeRegex !== self::UNDEFINED) {
            $instance->removeRegex = $removeRegex;
        }

        return $instance;
    }

    protected function converters(): array
    {
        static $converters;

        return $converters ??= [
            'add' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\ListConverter(new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\StringConverter))),
            'removeRegex' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\StringConverter),
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
