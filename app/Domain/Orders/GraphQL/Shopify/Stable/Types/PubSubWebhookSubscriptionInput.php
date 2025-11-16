<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

/**
 * @property string $pubSubProject
 * @property string $pubSubTopic
 * @property string|null $format
 * @property array<string>|null $includeFields
 * @property array<string>|null $metafieldNamespaces
 */
class PubSubWebhookSubscriptionInput extends \Spawnia\Sailor\ObjectLike
{
    /**
     * @param string $pubSubProject
     * @param string $pubSubTopic
     * @param string|null $format
     * @param array<string>|null $includeFields
     * @param array<string>|null $metafieldNamespaces
     */
    public static function make(
        $pubSubProject,
        $pubSubTopic,
        $format = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $includeFields = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $metafieldNamespaces = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
    ): self {
        $instance = new self;

        if ($pubSubProject !== self::UNDEFINED) {
            $instance->pubSubProject = $pubSubProject;
        }
        if ($pubSubTopic !== self::UNDEFINED) {
            $instance->pubSubTopic = $pubSubTopic;
        }
        if ($format !== self::UNDEFINED) {
            $instance->format = $format;
        }
        if ($includeFields !== self::UNDEFINED) {
            $instance->includeFields = $includeFields;
        }
        if ($metafieldNamespaces !== self::UNDEFINED) {
            $instance->metafieldNamespaces = $metafieldNamespaces;
        }

        return $instance;
    }

    protected function converters(): array
    {
        static $converters;

        return $converters ??= [
            'pubSubProject' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\StringConverter),
            'pubSubTopic' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\StringConverter),
            'format' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\EnumConverter),
            'includeFields' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\ListConverter(new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\StringConverter))),
            'metafieldNamespaces' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\ListConverter(new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\StringConverter))),
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
