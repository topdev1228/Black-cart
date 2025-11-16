<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

/**
 * @property string|null $templateSuffix
 */
class MetaobjectCapabilityDataOnlineStoreInput extends \Spawnia\Sailor\ObjectLike
{
    /**
     * @param string|null $templateSuffix
     */
    public static function make(
        $templateSuffix = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
    ): self {
        $instance = new self;

        if ($templateSuffix !== self::UNDEFINED) {
            $instance->templateSuffix = $templateSuffix;
        }

        return $instance;
    }

    protected function converters(): array
    {
        static $converters;

        return $converters ??= [
            'templateSuffix' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\StringConverter),
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
