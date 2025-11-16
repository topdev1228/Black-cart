<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

/**
 * @property string|null $company
 * @property string|null $number
 * @property array<string>|null $numbers
 * @property mixed|null $url
 * @property array<mixed>|null $urls
 */
class FulfillmentTrackingInput extends \Spawnia\Sailor\ObjectLike
{
    /**
     * @param string|null $company
     * @param string|null $number
     * @param array<string>|null $numbers
     * @param mixed|null $url
     * @param array<mixed>|null $urls
     */
    public static function make(
        $company = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $number = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $numbers = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $url = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $urls = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
    ): self {
        $instance = new self;

        if ($company !== self::UNDEFINED) {
            $instance->company = $company;
        }
        if ($number !== self::UNDEFINED) {
            $instance->number = $number;
        }
        if ($numbers !== self::UNDEFINED) {
            $instance->numbers = $numbers;
        }
        if ($url !== self::UNDEFINED) {
            $instance->url = $url;
        }
        if ($urls !== self::UNDEFINED) {
            $instance->urls = $urls;
        }

        return $instance;
    }

    protected function converters(): array
    {
        static $converters;

        return $converters ??= [
            'company' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\StringConverter),
            'number' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\StringConverter),
            'numbers' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\ListConverter(new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\StringConverter))),
            'url' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\ScalarConverter),
            'urls' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\ListConverter(new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\ScalarConverter))),
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
