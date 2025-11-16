<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

/**
 * @property string|null $number
 * @property mixed|null $url
 */
class ReverseDeliveryTrackingInput extends \Spawnia\Sailor\ObjectLike
{
    /**
     * @param string|null $number
     * @param mixed|null $url
     */
    public static function make(
        $number = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $url = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
    ): self {
        $instance = new self;

        if ($number !== self::UNDEFINED) {
            $instance->number = $number;
        }
        if ($url !== self::UNDEFINED) {
            $instance->url = $url;
        }

        return $instance;
    }

    protected function converters(): array
    {
        static $converters;

        return $converters ??= [
            'number' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\StringConverter),
            'url' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\ScalarConverter),
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
