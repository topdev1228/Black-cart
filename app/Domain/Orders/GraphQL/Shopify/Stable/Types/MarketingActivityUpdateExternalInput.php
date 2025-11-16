<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

/**
 * @property \App\Domain\Orders\GraphQL\Shopify\Stable\Types\MoneyInput|null $adSpend
 * @property \App\Domain\Orders\GraphQL\Shopify\Stable\Types\MarketingActivityBudgetInput|null $budget
 * @property mixed|null $end
 * @property string|null $marketingChannelType
 * @property string|null $referringDomain
 * @property mixed|null $remotePreviewImageUrl
 * @property mixed|null $remoteUrl
 * @property mixed|null $scheduledEnd
 * @property mixed|null $scheduledStart
 * @property mixed|null $start
 * @property string|null $status
 * @property string|null $tactic
 * @property string|null $title
 */
class MarketingActivityUpdateExternalInput extends \Spawnia\Sailor\ObjectLike
{
    /**
     * @param \App\Domain\Orders\GraphQL\Shopify\Stable\Types\MoneyInput|null $adSpend
     * @param \App\Domain\Orders\GraphQL\Shopify\Stable\Types\MarketingActivityBudgetInput|null $budget
     * @param mixed|null $end
     * @param string|null $marketingChannelType
     * @param string|null $referringDomain
     * @param mixed|null $remotePreviewImageUrl
     * @param mixed|null $remoteUrl
     * @param mixed|null $scheduledEnd
     * @param mixed|null $scheduledStart
     * @param mixed|null $start
     * @param string|null $status
     * @param string|null $tactic
     * @param string|null $title
     */
    public static function make(
        $adSpend = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $budget = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $end = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $marketingChannelType = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $referringDomain = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $remotePreviewImageUrl = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $remoteUrl = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $scheduledEnd = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $scheduledStart = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $start = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $status = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $tactic = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $title = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
    ): self {
        $instance = new self;

        if ($adSpend !== self::UNDEFINED) {
            $instance->adSpend = $adSpend;
        }
        if ($budget !== self::UNDEFINED) {
            $instance->budget = $budget;
        }
        if ($end !== self::UNDEFINED) {
            $instance->end = $end;
        }
        if ($marketingChannelType !== self::UNDEFINED) {
            $instance->marketingChannelType = $marketingChannelType;
        }
        if ($referringDomain !== self::UNDEFINED) {
            $instance->referringDomain = $referringDomain;
        }
        if ($remotePreviewImageUrl !== self::UNDEFINED) {
            $instance->remotePreviewImageUrl = $remotePreviewImageUrl;
        }
        if ($remoteUrl !== self::UNDEFINED) {
            $instance->remoteUrl = $remoteUrl;
        }
        if ($scheduledEnd !== self::UNDEFINED) {
            $instance->scheduledEnd = $scheduledEnd;
        }
        if ($scheduledStart !== self::UNDEFINED) {
            $instance->scheduledStart = $scheduledStart;
        }
        if ($start !== self::UNDEFINED) {
            $instance->start = $start;
        }
        if ($status !== self::UNDEFINED) {
            $instance->status = $status;
        }
        if ($tactic !== self::UNDEFINED) {
            $instance->tactic = $tactic;
        }
        if ($title !== self::UNDEFINED) {
            $instance->title = $title;
        }

        return $instance;
    }

    protected function converters(): array
    {
        static $converters;

        return $converters ??= [
            'adSpend' => new \Spawnia\Sailor\Convert\NullConverter(new \App\Domain\Orders\GraphQL\Shopify\Stable\Types\MoneyInput),
            'budget' => new \Spawnia\Sailor\Convert\NullConverter(new \App\Domain\Orders\GraphQL\Shopify\Stable\Types\MarketingActivityBudgetInput),
            'end' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\ScalarConverter),
            'marketingChannelType' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\EnumConverter),
            'referringDomain' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\StringConverter),
            'remotePreviewImageUrl' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\ScalarConverter),
            'remoteUrl' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\ScalarConverter),
            'scheduledEnd' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\ScalarConverter),
            'scheduledStart' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\ScalarConverter),
            'start' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\ScalarConverter),
            'status' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\EnumConverter),
            'tactic' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\EnumConverter),
            'title' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\StringConverter),
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
