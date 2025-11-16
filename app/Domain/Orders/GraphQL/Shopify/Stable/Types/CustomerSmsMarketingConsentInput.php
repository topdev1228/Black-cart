<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

/**
 * @property string $marketingState
 * @property mixed|null $consentUpdatedAt
 * @property string|null $marketingOptInLevel
 */
class CustomerSmsMarketingConsentInput extends \Spawnia\Sailor\ObjectLike
{
    /**
     * @param string $marketingState
     * @param mixed|null $consentUpdatedAt
     * @param string|null $marketingOptInLevel
     */
    public static function make(
        $marketingState,
        $consentUpdatedAt = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $marketingOptInLevel = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
    ): self {
        $instance = new self;

        if ($marketingState !== self::UNDEFINED) {
            $instance->marketingState = $marketingState;
        }
        if ($consentUpdatedAt !== self::UNDEFINED) {
            $instance->consentUpdatedAt = $consentUpdatedAt;
        }
        if ($marketingOptInLevel !== self::UNDEFINED) {
            $instance->marketingOptInLevel = $marketingOptInLevel;
        }

        return $instance;
    }

    protected function converters(): array
    {
        static $converters;

        return $converters ??= [
            'marketingState' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\EnumConverter),
            'consentUpdatedAt' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\ScalarConverter),
            'marketingOptInLevel' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\EnumConverter),
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
