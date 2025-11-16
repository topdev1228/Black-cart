<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

/**
 * @property int|string $locationId
 * @property string $name
 * @property string|null $ledgerDocumentUri
 */
class InventoryMoveQuantityTerminalInput extends \Spawnia\Sailor\ObjectLike
{
    /**
     * @param int|string $locationId
     * @param string $name
     * @param string|null $ledgerDocumentUri
     */
    public static function make(
        $locationId,
        $name,
        $ledgerDocumentUri = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
    ): self {
        $instance = new self;

        if ($locationId !== self::UNDEFINED) {
            $instance->locationId = $locationId;
        }
        if ($name !== self::UNDEFINED) {
            $instance->name = $name;
        }
        if ($ledgerDocumentUri !== self::UNDEFINED) {
            $instance->ledgerDocumentUri = $ledgerDocumentUri;
        }

        return $instance;
    }

    protected function converters(): array
    {
        static $converters;

        return $converters ??= [
            'locationId' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\IDConverter),
            'name' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\StringConverter),
            'ledgerDocumentUri' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\StringConverter),
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
