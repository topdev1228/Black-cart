<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

/**
 * @property string $newTitle
 * @property int|string $productId
 * @property bool|null $includeImages
 * @property string|null $newStatus
 */
class ProductDuplicateAsyncInput extends \Spawnia\Sailor\ObjectLike
{
    /**
     * @param string $newTitle
     * @param int|string $productId
     * @param bool|null $includeImages
     * @param string|null $newStatus
     */
    public static function make(
        $newTitle,
        $productId,
        $includeImages = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $newStatus = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
    ): self {
        $instance = new self;

        if ($newTitle !== self::UNDEFINED) {
            $instance->newTitle = $newTitle;
        }
        if ($productId !== self::UNDEFINED) {
            $instance->productId = $productId;
        }
        if ($includeImages !== self::UNDEFINED) {
            $instance->includeImages = $includeImages;
        }
        if ($newStatus !== self::UNDEFINED) {
            $instance->newStatus = $newStatus;
        }

        return $instance;
    }

    protected function converters(): array
    {
        static $converters;

        return $converters ??= [
            'newTitle' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\StringConverter),
            'productId' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\IDConverter),
            'includeImages' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\BooleanConverter),
            'newStatus' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\EnumConverter),
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
