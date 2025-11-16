<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

/**
 * @property bool|null $autoPublish
 * @property array<int|string>|null $publishablesToAdd
 * @property array<int|string>|null $publishablesToRemove
 */
class PublicationUpdateInput extends \Spawnia\Sailor\ObjectLike
{
    /**
     * @param bool|null $autoPublish
     * @param array<int|string>|null $publishablesToAdd
     * @param array<int|string>|null $publishablesToRemove
     */
    public static function make(
        $autoPublish = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $publishablesToAdd = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $publishablesToRemove = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
    ): self {
        $instance = new self;

        if ($autoPublish !== self::UNDEFINED) {
            $instance->autoPublish = $autoPublish;
        }
        if ($publishablesToAdd !== self::UNDEFINED) {
            $instance->publishablesToAdd = $publishablesToAdd;
        }
        if ($publishablesToRemove !== self::UNDEFINED) {
            $instance->publishablesToRemove = $publishablesToRemove;
        }

        return $instance;
    }

    protected function converters(): array
    {
        static $converters;

        return $converters ??= [
            'autoPublish' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\BooleanConverter),
            'publishablesToAdd' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\ListConverter(new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\IDConverter))),
            'publishablesToRemove' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\ListConverter(new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\IDConverter))),
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
