<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

/**
 * @property int|string $companyContactRoleId
 * @property int|string $companyLocationId
 */
class CompanyContactRoleAssign extends \Spawnia\Sailor\ObjectLike
{
    /**
     * @param int|string $companyContactRoleId
     * @param int|string $companyLocationId
     */
    public static function make($companyContactRoleId, $companyLocationId): self
    {
        $instance = new self;

        if ($companyContactRoleId !== self::UNDEFINED) {
            $instance->companyContactRoleId = $companyContactRoleId;
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
            'companyContactRoleId' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\IDConverter),
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
