<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

/**
 * @property int|string $customerId
 * @property \App\Domain\Orders\GraphQL\Shopify\Stable\Types\CustomerSmsMarketingConsentInput $smsMarketingConsent
 */
class CustomerSmsMarketingConsentUpdateInput extends \Spawnia\Sailor\ObjectLike
{
    /**
     * @param int|string $customerId
     * @param \App\Domain\Orders\GraphQL\Shopify\Stable\Types\CustomerSmsMarketingConsentInput $smsMarketingConsent
     */
    public static function make($customerId, $smsMarketingConsent): self
    {
        $instance = new self;

        if ($customerId !== self::UNDEFINED) {
            $instance->customerId = $customerId;
        }
        if ($smsMarketingConsent !== self::UNDEFINED) {
            $instance->smsMarketingConsent = $smsMarketingConsent;
        }

        return $instance;
    }

    protected function converters(): array
    {
        static $converters;

        return $converters ??= [
            'customerId' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\IDConverter),
            'smsMarketingConsent' => new \Spawnia\Sailor\Convert\NonNullConverter(new \App\Domain\Orders\GraphQL\Shopify\Stable\Types\CustomerSmsMarketingConsentInput),
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
