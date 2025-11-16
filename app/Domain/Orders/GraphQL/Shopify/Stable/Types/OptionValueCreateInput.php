<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

/**
 * @property string|null $linkedMetafieldValue
 * @property string|null $name
 */
class OptionValueCreateInput extends \Spawnia\Sailor\ObjectLike
{
    /**
     * @param string|null $linkedMetafieldValue
     * @param string|null $name
     */
    public static function make(
        $linkedMetafieldValue = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $name = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
    ): self {
        $instance = new self;

        if ($linkedMetafieldValue !== self::UNDEFINED) {
            $instance->linkedMetafieldValue = $linkedMetafieldValue;
        }
        if ($name !== self::UNDEFINED) {
            $instance->name = $name;
        }

        return $instance;
    }

    protected function converters(): array
    {
        static $converters;

        return $converters ??= [
            'linkedMetafieldValue' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\StringConverter),
            'name' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\StringConverter),
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
