<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

/**
 * @property string $key
 * @property string $value
 * @property bool|null $isSecret
 */
class HydrogenStorefrontEnvironmentVariableInput extends \Spawnia\Sailor\ObjectLike
{
    /**
     * @param string $key
     * @param string $value
     * @param bool|null $isSecret
     */
    public static function make(
        $key,
        $value,
        $isSecret = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
    ): self {
        $instance = new self;

        if ($key !== self::UNDEFINED) {
            $instance->key = $key;
        }
        if ($value !== self::UNDEFINED) {
            $instance->value = $value;
        }
        if ($isSecret !== self::UNDEFINED) {
            $instance->isSecret = $isSecret;
        }

        return $instance;
    }

    protected function converters(): array
    {
        static $converters;

        return $converters ??= [
            'key' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\StringConverter),
            'value' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\StringConverter),
            'isSecret' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\BooleanConverter),
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
