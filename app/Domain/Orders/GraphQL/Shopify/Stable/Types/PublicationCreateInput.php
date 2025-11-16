<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

/**
 * @property bool|null $autoPublish
 * @property int|string|null $catalogId
 * @property string|null $defaultState
 */
class PublicationCreateInput extends \Spawnia\Sailor\ObjectLike
{
    /**
     * @param bool|null $autoPublish
     * @param int|string|null $catalogId
     * @param string|null $defaultState
     */
    public static function make(
        $autoPublish = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $catalogId = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $defaultState = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
    ): self {
        $instance = new self;

        if ($autoPublish !== self::UNDEFINED) {
            $instance->autoPublish = $autoPublish;
        }
        if ($catalogId !== self::UNDEFINED) {
            $instance->catalogId = $catalogId;
        }
        if ($defaultState !== self::UNDEFINED) {
            $instance->defaultState = $defaultState;
        }

        return $instance;
    }

    protected function converters(): array
    {
        static $converters;

        return $converters ??= [
            'autoPublish' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\BooleanConverter),
            'catalogId' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\IDConverter),
            'defaultState' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\EnumConverter),
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
