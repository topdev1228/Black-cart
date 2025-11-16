<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

/**
 * @property int|string $gateConfigurationId
 * @property int|string $subject
 * @property bool|null $active
 */
class GateSubjectCreateInput extends \Spawnia\Sailor\ObjectLike
{
    /**
     * @param int|string $gateConfigurationId
     * @param int|string $subject
     * @param bool|null $active
     */
    public static function make(
        $gateConfigurationId,
        $subject,
        $active = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
    ): self {
        $instance = new self;

        if ($gateConfigurationId !== self::UNDEFINED) {
            $instance->gateConfigurationId = $gateConfigurationId;
        }
        if ($subject !== self::UNDEFINED) {
            $instance->subject = $subject;
        }
        if ($active !== self::UNDEFINED) {
            $instance->active = $active;
        }

        return $instance;
    }

    protected function converters(): array
    {
        static $converters;

        return $converters ??= [
            'gateConfigurationId' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\IDConverter),
            'subject' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\IDConverter),
            'active' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\BooleanConverter),
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
