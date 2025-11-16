<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

/**
 * @property string $campaign
 * @property string $medium
 * @property string $source
 */
class UTMInput extends \Spawnia\Sailor\ObjectLike
{
    /**
     * @param string $campaign
     * @param string $medium
     * @param string $source
     */
    public static function make($campaign, $medium, $source): self
    {
        $instance = new self;

        if ($campaign !== self::UNDEFINED) {
            $instance->campaign = $campaign;
        }
        if ($medium !== self::UNDEFINED) {
            $instance->medium = $medium;
        }
        if ($source !== self::UNDEFINED) {
            $instance->source = $source;
        }

        return $instance;
    }

    protected function converters(): array
    {
        static $converters;

        return $converters ??= [
            'campaign' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\StringConverter),
            'medium' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\StringConverter),
            'source' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\StringConverter),
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
