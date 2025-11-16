<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

/**
 * @property int|string|null $publicationId
 * @property mixed|null $publishDate
 */
class ProductPublicationInput extends \Spawnia\Sailor\ObjectLike
{
    /**
     * @param int|string|null $publicationId
     * @param mixed|null $publishDate
     */
    public static function make(
        $publicationId = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $publishDate = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
    ): self {
        $instance = new self;

        if ($publicationId !== self::UNDEFINED) {
            $instance->publicationId = $publicationId;
        }
        if ($publishDate !== self::UNDEFINED) {
            $instance->publishDate = $publishDate;
        }

        return $instance;
    }

    protected function converters(): array
    {
        static $converters;

        return $converters ??= [
            'publicationId' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\IDConverter),
            'publishDate' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\ScalarConverter),
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
