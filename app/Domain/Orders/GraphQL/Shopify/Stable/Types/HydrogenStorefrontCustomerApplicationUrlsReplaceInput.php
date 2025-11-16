<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

/**
 * @property \App\Domain\Orders\GraphQL\Shopify\Stable\Types\HydrogenStorefrontCustomerApplicationUrlsReplaceUriInput|null $javascriptOrigin
 * @property \App\Domain\Orders\GraphQL\Shopify\Stable\Types\HydrogenStorefrontCustomerApplicationUrlsReplaceUriInput|null $logoutUris
 * @property \App\Domain\Orders\GraphQL\Shopify\Stable\Types\HydrogenStorefrontCustomerApplicationUrlsReplaceUriInput|null $redirectUri
 */
class HydrogenStorefrontCustomerApplicationUrlsReplaceInput extends \Spawnia\Sailor\ObjectLike
{
    /**
     * @param \App\Domain\Orders\GraphQL\Shopify\Stable\Types\HydrogenStorefrontCustomerApplicationUrlsReplaceUriInput|null $javascriptOrigin
     * @param \App\Domain\Orders\GraphQL\Shopify\Stable\Types\HydrogenStorefrontCustomerApplicationUrlsReplaceUriInput|null $logoutUris
     * @param \App\Domain\Orders\GraphQL\Shopify\Stable\Types\HydrogenStorefrontCustomerApplicationUrlsReplaceUriInput|null $redirectUri
     */
    public static function make(
        $javascriptOrigin = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $logoutUris = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $redirectUri = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
    ): self {
        $instance = new self;

        if ($javascriptOrigin !== self::UNDEFINED) {
            $instance->javascriptOrigin = $javascriptOrigin;
        }
        if ($logoutUris !== self::UNDEFINED) {
            $instance->logoutUris = $logoutUris;
        }
        if ($redirectUri !== self::UNDEFINED) {
            $instance->redirectUri = $redirectUri;
        }

        return $instance;
    }

    protected function converters(): array
    {
        static $converters;

        return $converters ??= [
            'javascriptOrigin' => new \Spawnia\Sailor\Convert\NullConverter(new \App\Domain\Orders\GraphQL\Shopify\Stable\Types\HydrogenStorefrontCustomerApplicationUrlsReplaceUriInput),
            'logoutUris' => new \Spawnia\Sailor\Convert\NullConverter(new \App\Domain\Orders\GraphQL\Shopify\Stable\Types\HydrogenStorefrontCustomerApplicationUrlsReplaceUriInput),
            'redirectUri' => new \Spawnia\Sailor\Convert\NullConverter(new \App\Domain\Orders\GraphQL\Shopify\Stable\Types\HydrogenStorefrontCustomerApplicationUrlsReplaceUriInput),
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
