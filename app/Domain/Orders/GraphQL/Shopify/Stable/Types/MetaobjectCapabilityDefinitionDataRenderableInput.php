<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

/**
 * @property string|null $metaDescriptionKey
 * @property string|null $metaTitleKey
 */
class MetaobjectCapabilityDefinitionDataRenderableInput extends \Spawnia\Sailor\ObjectLike
{
    /**
     * @param string|null $metaDescriptionKey
     * @param string|null $metaTitleKey
     */
    public static function make(
        $metaDescriptionKey = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $metaTitleKey = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
    ): self {
        $instance = new self;

        if ($metaDescriptionKey !== self::UNDEFINED) {
            $instance->metaDescriptionKey = $metaDescriptionKey;
        }
        if ($metaTitleKey !== self::UNDEFINED) {
            $instance->metaTitleKey = $metaTitleKey;
        }

        return $instance;
    }

    protected function converters(): array
    {
        static $converters;

        return $converters ??= [
            'metaDescriptionKey' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\StringConverter),
            'metaTitleKey' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\StringConverter),
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
