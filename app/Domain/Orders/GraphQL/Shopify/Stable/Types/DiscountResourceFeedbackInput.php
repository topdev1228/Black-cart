<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

/**
 * @property int|string $discountId
 * @property mixed $discountUpdatedAt
 * @property mixed $feedbackGeneratedAt
 * @property string $state
 * @property array<string>|null $messages
 */
class DiscountResourceFeedbackInput extends \Spawnia\Sailor\ObjectLike
{
    /**
     * @param int|string $discountId
     * @param mixed $discountUpdatedAt
     * @param mixed $feedbackGeneratedAt
     * @param string $state
     * @param array<string>|null $messages
     */
    public static function make(
        $discountId,
        $discountUpdatedAt,
        $feedbackGeneratedAt,
        $state,
        $messages = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
    ): self {
        $instance = new self;

        if ($discountId !== self::UNDEFINED) {
            $instance->discountId = $discountId;
        }
        if ($discountUpdatedAt !== self::UNDEFINED) {
            $instance->discountUpdatedAt = $discountUpdatedAt;
        }
        if ($feedbackGeneratedAt !== self::UNDEFINED) {
            $instance->feedbackGeneratedAt = $feedbackGeneratedAt;
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
            'discountId' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\IDConverter),
            'discountUpdatedAt' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\ScalarConverter),
            'feedbackGeneratedAt' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\ScalarConverter),
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
