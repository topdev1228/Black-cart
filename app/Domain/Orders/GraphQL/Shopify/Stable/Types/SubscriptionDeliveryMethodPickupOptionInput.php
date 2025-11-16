<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

/**
 * @property int|string $locationId
 * @property string|null $code
 * @property string|null $description
 * @property string|null $presentmentTitle
 * @property string|null $title
 */
class SubscriptionDeliveryMethodPickupOptionInput extends \Spawnia\Sailor\ObjectLike
{
    /**
     * @param int|string $locationId
     * @param string|null $code
     * @param string|null $description
     * @param string|null $presentmentTitle
     * @param string|null $title
     */
    public static function make(
        $locationId,
        $code = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $description = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $presentmentTitle = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $title = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
    ): self {
        $instance = new self;

        if ($locationId !== self::UNDEFINED) {
            $instance->locationId = $locationId;
        }
        if ($code !== self::UNDEFINED) {
            $instance->code = $code;
        }
        if ($description !== self::UNDEFINED) {
            $instance->description = $description;
        }
        if ($presentmentTitle !== self::UNDEFINED) {
            $instance->presentmentTitle = $presentmentTitle;
        }
        if ($title !== self::UNDEFINED) {
            $instance->title = $title;
        }

        return $instance;
    }

    protected function converters(): array
    {
        static $converters;

        return $converters ??= [
            'locationId' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\IDConverter),
            'code' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\StringConverter),
            'description' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\StringConverter),
            'presentmentTitle' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\StringConverter),
            'title' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\StringConverter),
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
