<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

/**
 * @property bool $showInternationalShipping
 * @property bool $showSkuAndBarcode
 */
class ProductPreferencesInput extends \Spawnia\Sailor\ObjectLike
{
    /**
     * @param bool $showInternationalShipping
     * @param bool $showSkuAndBarcode
     */
    public static function make($showInternationalShipping, $showSkuAndBarcode): self
    {
        $instance = new self;

        if ($showInternationalShipping !== self::UNDEFINED) {
            $instance->showInternationalShipping = $showInternationalShipping;
        }
        if ($showSkuAndBarcode !== self::UNDEFINED) {
            $instance->showSkuAndBarcode = $showSkuAndBarcode;
        }

        return $instance;
    }

    protected function converters(): array
    {
        static $converters;

        return $converters ??= [
            'showInternationalShipping' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\BooleanConverter),
            'showSkuAndBarcode' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\BooleanConverter),
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
