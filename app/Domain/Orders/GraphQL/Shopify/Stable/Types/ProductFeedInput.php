<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

/**
 * @property string $country
 * @property string $language
 */
class ProductFeedInput extends \Spawnia\Sailor\ObjectLike
{
    /**
     * @param string $country
     * @param string $language
     */
    public static function make($country, $language): self
    {
        $instance = new self;

        if ($country !== self::UNDEFINED) {
            $instance->country = $country;
        }
        if ($language !== self::UNDEFINED) {
            $instance->language = $language;
        }

        return $instance;
    }

    protected function converters(): array
    {
        static $converters;

        return $converters ??= [
            'country' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\EnumConverter),
            'language' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\EnumConverter),
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
