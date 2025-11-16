<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

/**
 * @property string $phone
 * @property string|null $code
 * @property string|null $description
 * @property string|null $instructions
 * @property string|null $presentmentTitle
 * @property string|null $title
 */
class SubscriptionDeliveryMethodLocalDeliveryOptionInput extends \Spawnia\Sailor\ObjectLike
{
    /**
     * @param string $phone
     * @param string|null $code
     * @param string|null $description
     * @param string|null $instructions
     * @param string|null $presentmentTitle
     * @param string|null $title
     */
    public static function make(
        $phone,
        $code = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $description = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $instructions = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $presentmentTitle = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $title = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
    ): self {
        $instance = new self;

        if ($phone !== self::UNDEFINED) {
            $instance->phone = $phone;
        }
        if ($code !== self::UNDEFINED) {
            $instance->code = $code;
        }
        if ($description !== self::UNDEFINED) {
            $instance->description = $description;
        }
        if ($instructions !== self::UNDEFINED) {
            $instance->instructions = $instructions;
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
            'phone' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\StringConverter),
            'code' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\StringConverter),
            'description' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\StringConverter),
            'instructions' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\StringConverter),
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
