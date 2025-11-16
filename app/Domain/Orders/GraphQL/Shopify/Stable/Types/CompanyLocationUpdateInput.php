<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

/**
 * @property \App\Domain\Orders\GraphQL\Shopify\Stable\Types\BuyerExperienceConfigurationInput|null $buyerExperienceConfiguration
 * @property string|null $externalId
 * @property string|null $locale
 * @property string|null $name
 * @property string|null $note
 * @property string|null $phone
 */
class CompanyLocationUpdateInput extends \Spawnia\Sailor\ObjectLike
{
    /**
     * @param \App\Domain\Orders\GraphQL\Shopify\Stable\Types\BuyerExperienceConfigurationInput|null $buyerExperienceConfiguration
     * @param string|null $externalId
     * @param string|null $locale
     * @param string|null $name
     * @param string|null $note
     * @param string|null $phone
     */
    public static function make(
        $buyerExperienceConfiguration = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $externalId = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $locale = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $name = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $note = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $phone = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
    ): self {
        $instance = new self;

        if ($buyerExperienceConfiguration !== self::UNDEFINED) {
            $instance->buyerExperienceConfiguration = $buyerExperienceConfiguration;
        }
        if ($externalId !== self::UNDEFINED) {
            $instance->externalId = $externalId;
        }
        if ($locale !== self::UNDEFINED) {
            $instance->locale = $locale;
        }
        if ($name !== self::UNDEFINED) {
            $instance->name = $name;
        }
        if ($note !== self::UNDEFINED) {
            $instance->note = $note;
        }
        if ($phone !== self::UNDEFINED) {
            $instance->phone = $phone;
        }

        return $instance;
    }

    protected function converters(): array
    {
        static $converters;

        return $converters ??= [
            'buyerExperienceConfiguration' => new \Spawnia\Sailor\Convert\NullConverter(new \App\Domain\Orders\GraphQL\Shopify\Stable\Types\BuyerExperienceConfigurationInput),
            'externalId' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\StringConverter),
            'locale' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\StringConverter),
            'name' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\StringConverter),
            'note' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\StringConverter),
            'phone' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\StringConverter),
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
