<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

/**
 * @property mixed $feedbackGeneratedAt
 * @property int|string $productId
 * @property mixed $productUpdatedAt
 * @property string $state
 * @property array<string>|null $messages
 */
class ProductResourceFeedbackInput extends \Spawnia\Sailor\ObjectLike
{
    /**
     * @param mixed $feedbackGeneratedAt
     * @param int|string $productId
     * @param mixed $productUpdatedAt
     * @param string $state
     * @param array<string>|null $messages
     */
    public static function make(
        $feedbackGeneratedAt,
        $productId,
        $productUpdatedAt,
        $state,
        $messages = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
    ): self {
        $instance = new self;

        if ($feedbackGeneratedAt !== self::UNDEFINED) {
            $instance->feedbackGeneratedAt = $feedbackGeneratedAt;
        }
        if ($productId !== self::UNDEFINED) {
            $instance->productId = $productId;
        }
        if ($productUpdatedAt !== self::UNDEFINED) {
            $instance->productUpdatedAt = $productUpdatedAt;
        }
        if ($state !== self::UNDEFINED) {
            $instance->state = $state;
        }
        if ($messages !== self::UNDEFINED) {
            $instance->messages = $messages;
        }

        return $instance;
    }

    protected function converters(): array
    {
        static $converters;

        return $converters ??= [
            'feedbackGeneratedAt' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\ScalarConverter),
            'productId' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\IDConverter),
            'productUpdatedAt' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\ScalarConverter),
            'state' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\EnumConverter),
            'messages' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\ListConverter(new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\StringConverter))),
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
