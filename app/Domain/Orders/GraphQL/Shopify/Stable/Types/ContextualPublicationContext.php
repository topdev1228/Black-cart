<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

/**
 * @property int|string|null $companyLocationId
 * @property string|null $country
 */
class ContextualPublicationContext extends \Spawnia\Sailor\ObjectLike
{
    /**
     * @param int|string|null $companyLocationId
     * @param string|null $country
     */
    public static function make(
        $companyLocationId = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $country = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
    ): self {
        $instance = new self;

        if ($companyLocationId !== self::UNDEFINED) {
            $instance->companyLocationId = $companyLocationId;
        }
        if ($country !== self::UNDEFINED) {
            $instance->country = $country;
        }

        return $instance;
    }

    protected function converters(): array
    {
        static $converters;

        return $converters ??= [
            'companyLocationId' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\IDConverter),
            'country' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\EnumConverter),
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
