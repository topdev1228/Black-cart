<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

/**
 * @property mixed|null $callbackUrl
 * @property string|null $format
 * @property array<string>|null $includeFields
 * @property array<string>|null $metafieldNamespaces
 */
class WebhookSubscriptionInput extends \Spawnia\Sailor\ObjectLike
{
    /**
     * @param mixed|null $callbackUrl
     * @param string|null $format
     * @param array<string>|null $includeFields
     * @param array<string>|null $metafieldNamespaces
     */
    public static function make(
        $callbackUrl = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $format = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $includeFields = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $metafieldNamespaces = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
    ): self {
        $instance = new self;

        if ($callbackUrl !== self::UNDEFINED) {
            $instance->callbackUrl = $callbackUrl;
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
            'callbackUrl' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\ScalarConverter),
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
