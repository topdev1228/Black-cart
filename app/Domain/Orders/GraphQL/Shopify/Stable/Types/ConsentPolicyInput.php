<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

/**
 * @property bool|null $consentRequired
 * @property string|null $countryCode
 * @property bool|null $dataSaleOptOutRequired
 * @property string|null $regionCode
 */
class ConsentPolicyInput extends \Spawnia\Sailor\ObjectLike
{
    /**
     * @param bool|null $consentRequired
     * @param string|null $countryCode
     * @param bool|null $dataSaleOptOutRequired
     * @param string|null $regionCode
     */
    public static function make(
        $consentRequired = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $countryCode = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $dataSaleOptOutRequired = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $regionCode = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
    ): self {
        $instance = new self;

        if ($consentRequired !== self::UNDEFINED) {
            $instance->consentRequired = $consentRequired;
        }
        if ($countryCode !== self::UNDEFINED) {
            $instance->countryCode = $countryCode;
        }
        if ($dataSaleOptOutRequired !== self::UNDEFINED) {
            $instance->dataSaleOptOutRequired = $dataSaleOptOutRequired;
        }
        if ($regionCode !== self::UNDEFINED) {
            $instance->regionCode = $regionCode;
        }

        return $instance;
    }

    protected function converters(): array
    {
        static $converters;

        return $converters ??= [
            'consentRequired' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\BooleanConverter),
            'countryCode' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\EnumConverter),
            'dataSaleOptOutRequired' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\BooleanConverter),
            'regionCode' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\EnumConverter),
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
