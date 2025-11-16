<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

/**
 * @property int|string $companyContactId
 * @property int|string $companyId
 * @property int|string $companyLocationId
 */
class PurchasingCompanyInput extends \Spawnia\Sailor\ObjectLike
{
    /**
     * @param int|string $companyContactId
     * @param int|string $companyId
     * @param int|string $companyLocationId
     */
    public static function make($companyContactId, $companyId, $companyLocationId): self
    {
        $instance = new self;

        if ($companyContactId !== self::UNDEFINED) {
            $instance->companyContactId = $companyContactId;
        }
        if ($companyId !== self::UNDEFINED) {
            $instance->companyId = $companyId;
        }
        if ($companyLocationId !== self::UNDEFINED) {
            $instance->companyLocationId = $companyLocationId;
        }

        return $instance;
    }

    protected function converters(): array
    {
        static $converters;

        return $converters ??= [
            'companyContactId' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\IDConverter),
            'companyId' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\IDConverter),
            'companyLocationId' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\IDConverter),
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
