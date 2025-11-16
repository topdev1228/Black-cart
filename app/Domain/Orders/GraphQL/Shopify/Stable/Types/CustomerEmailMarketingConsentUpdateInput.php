<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

/**
 * @property int|string $customerId
 * @property \App\Domain\Orders\GraphQL\Shopify\Stable\Types\CustomerEmailMarketingConsentInput $emailMarketingConsent
 */
class CustomerEmailMarketingConsentUpdateInput extends \Spawnia\Sailor\ObjectLike
{
    /**
     * @param int|string $customerId
     * @param \App\Domain\Orders\GraphQL\Shopify\Stable\Types\CustomerEmailMarketingConsentInput $emailMarketingConsent
     */
    public static function make($customerId, $emailMarketingConsent): self
    {
        $instance = new self;

        if ($customerId !== self::UNDEFINED) {
            $instance->customerId = $customerId;
        }
        if ($emailMarketingConsent !== self::UNDEFINED) {
            $instance->emailMarketingConsent = $emailMarketingConsent;
        }

        return $instance;
    }

    protected function converters(): array
    {
        static $converters;

        return $converters ??= [
            'customerId' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\IDConverter),
            'emailMarketingConsent' => new \Spawnia\Sailor\Convert\NonNullConverter(new \App\Domain\Orders\GraphQL\Shopify\Stable\Types\CustomerEmailMarketingConsentInput),
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
