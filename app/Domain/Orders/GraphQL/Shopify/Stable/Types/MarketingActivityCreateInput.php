<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

/**
 * @property int|string $marketingActivityExtensionId
 * @property string $status
 * @property \App\Domain\Orders\GraphQL\Shopify\Stable\Types\MarketingActivityBudgetInput|null $budget
 * @property string|null $context
 * @property string|null $formData
 * @property string|null $marketingActivityTitle
 * @property \App\Domain\Orders\GraphQL\Shopify\Stable\Types\UTMInput|null $utm
 */
class MarketingActivityCreateInput extends \Spawnia\Sailor\ObjectLike
{
    /**
     * @param int|string $marketingActivityExtensionId
     * @param string $status
     * @param \App\Domain\Orders\GraphQL\Shopify\Stable\Types\MarketingActivityBudgetInput|null $budget
     * @param string|null $context
     * @param string|null $formData
     * @param string|null $marketingActivityTitle
     * @param \App\Domain\Orders\GraphQL\Shopify\Stable\Types\UTMInput|null $utm
     */
    public static function make(
        $marketingActivityExtensionId,
        $status,
        $budget = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $context = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $formData = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $marketingActivityTitle = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $utm = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
    ): self {
        $instance = new self;

        if ($marketingActivityExtensionId !== self::UNDEFINED) {
            $instance->marketingActivityExtensionId = $marketingActivityExtensionId;
        }
        if ($status !== self::UNDEFINED) {
            $instance->status = $status;
        }
        if ($budget !== self::UNDEFINED) {
            $instance->budget = $budget;
        }
        if ($context !== self::UNDEFINED) {
            $instance->context = $context;
        }
        if ($formData !== self::UNDEFINED) {
            $instance->formData = $formData;
        }
        if ($marketingActivityTitle !== self::UNDEFINED) {
            $instance->marketingActivityTitle = $marketingActivityTitle;
        }
        if ($utm !== self::UNDEFINED) {
            $instance->utm = $utm;
        }

        return $instance;
    }

    protected function converters(): array
    {
        static $converters;

        return $converters ??= [
            'marketingActivityExtensionId' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\IDConverter),
            'status' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\EnumConverter),
            'budget' => new \Spawnia\Sailor\Convert\NullConverter(new \App\Domain\Orders\GraphQL\Shopify\Stable\Types\MarketingActivityBudgetInput),
            'context' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\StringConverter),
            'formData' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\StringConverter),
            'marketingActivityTitle' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\StringConverter),
            'utm' => new \Spawnia\Sailor\Convert\NullConverter(new \App\Domain\Orders\GraphQL\Shopify\Stable\Types\UTMInput),
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
