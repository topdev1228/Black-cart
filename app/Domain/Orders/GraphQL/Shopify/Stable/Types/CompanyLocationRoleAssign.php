<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

/**
 * @property int|string $companyContactId
 * @property int|string $companyContactRoleId
 */
class CompanyLocationRoleAssign extends \Spawnia\Sailor\ObjectLike
{
    /**
     * @param int|string $companyContactId
     * @param int|string $companyContactRoleId
     */
    public static function make($companyContactId, $companyContactRoleId): self
    {
        $instance = new self;

        if ($companyContactId !== self::UNDEFINED) {
            $instance->companyContactId = $companyContactId;
        }
        if ($companyContactRoleId !== self::UNDEFINED) {
            $instance->companyContactRoleId = $companyContactRoleId;
        }

        return $instance;
    }

    protected function converters(): array
    {
        static $converters;

        return $converters ??= [
            'companyContactId' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\IDConverter),
            'companyContactRoleId' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\IDConverter),
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
