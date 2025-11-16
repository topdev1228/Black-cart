<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

/**
 * @property string $marketingChannelType
 * @property mixed $remoteUrl
 * @property string $tactic
 * @property string $title
 * @property \App\Domain\Orders\GraphQL\Shopify\Stable\Types\MoneyInput|null $adSpend
 * @property \App\Domain\Orders\GraphQL\Shopify\Stable\Types\MarketingActivityBudgetInput|null $budget
 * @property string|null $channelHandle
 * @property mixed|null $end
 * @property string|null $hierarchyLevel
 * @property int|string|null $parentActivityId
 * @property string|null $parentRemoteId
 * @property string|null $referringDomain
 * @property string|null $remoteId
 * @property mixed|null $remotePreviewImageUrl
 * @property mixed|null $scheduledEnd
 * @property mixed|null $scheduledStart
 * @property mixed|null $start
 * @property string|null $status
 * @property string|null $urlParameterValue
 * @property \App\Domain\Orders\GraphQL\Shopify\Stable\Types\UTMInput|null $utm
 */
class MarketingActivityCreateExternalInput extends \Spawnia\Sailor\ObjectLike
{
    /**
     * @param string $marketingChannelType
     * @param mixed $remoteUrl
     * @param string $tactic
     * @param string $title
     * @param \App\Domain\Orders\GraphQL\Shopify\Stable\Types\MoneyInput|null $adSpend
     * @param \App\Domain\Orders\GraphQL\Shopify\Stable\Types\MarketingActivityBudgetInput|null $budget
     * @param string|null $channelHandle
     * @param mixed|null $end
     * @param string|null $hierarchyLevel
     * @param int|string|null $parentActivityId
     * @param string|null $parentRemoteId
     * @param string|null $referringDomain
     * @param string|null $remoteId
     * @param mixed|null $remotePreviewImageUrl
     * @param mixed|null $scheduledEnd
     * @param mixed|null $scheduledStart
     * @param mixed|null $start
     * @param string|null $status
     * @param string|null $urlParameterValue
     * @param \App\Domain\Orders\GraphQL\Shopify\Stable\Types\UTMInput|null $utm
     */
    public static function make(
        $marketingChannelType,
        $remoteUrl,
        $tactic,
        $title,
        $adSpend = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $budget = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $channelHandle = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $end = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $hierarchyLevel = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $parentActivityId = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $parentRemoteId = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $referringDomain = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $remoteId = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $remotePreviewImageUrl = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $scheduledEnd = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $scheduledStart = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $start = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $status = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $urlParameterValue = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $utm = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
    ): self {
        $instance = new self;

        if ($marketingChannelType !== self::UNDEFINED) {
            $instance->marketingChannelType = $marketingChannelType;
        }
        if ($remoteUrl !== self::UNDEFINED) {
            $instance->remoteUrl = $remoteUrl;
        }
        if ($tactic !== self::UNDEFINED) {
            $instance->tactic = $tactic;
        }
        if ($title !== self::UNDEFINED) {
            $instance->title = $title;
        }
        if ($adSpend !== self::UNDEFINED) {
            $instance->adSpend = $adSpend;
        }
        if ($budget !== self::UNDEFINED) {
            $instance->budget = $budget;
        }
        if ($channelHandle !== self::UNDEFINED) {
            $instance->channelHandle = $channelHandle;
        }
        if ($end !== self::UNDEFINED) {
            $instance->end = $end;
        }
        if ($hierarchyLevel !== self::UNDEFINED) {
            $instance->hierarchyLevel = $hierarchyLevel;
        }
        if ($parentActivityId !== self::UNDEFINED) {
            $instance->parentActivityId = $parentActivityId;
        }
        if ($parentRemoteId !== self::UNDEFINED) {
            $instance->parentRemoteId = $parentRemoteId;
        }
        if ($referringDomain !== self::UNDEFINED) {
            $instance->referringDomain = $referringDomain;
        }
        if ($remoteId !== self::UNDEFINED) {
            $instance->remoteId = $remoteId;
        }
        if ($remotePreviewImageUrl !== self::UNDEFINED) {
            $instance->remotePreviewImageUrl = $remotePreviewImageUrl;
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
        if ($urlParameterValue !== self::UNDEFINED) {
            $instance->urlParameterValue = $urlParameterValue;
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
            'marketingChannelType' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\EnumConverter),
            'remoteUrl' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\ScalarConverter),
            'tactic' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\EnumConverter),
            'title' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\StringConverter),
            'adSpend' => new \Spawnia\Sailor\Convert\NullConverter(new \App\Domain\Orders\GraphQL\Shopify\Stable\Types\MoneyInput),
            'budget' => new \Spawnia\Sailor\Convert\NullConverter(new \App\Domain\Orders\GraphQL\Shopify\Stable\Types\MarketingActivityBudgetInput),
            'channelHandle' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\StringConverter),
            'end' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\ScalarConverter),
            'hierarchyLevel' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\EnumConverter),
            'parentActivityId' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\IDConverter),
            'parentRemoteId' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\StringConverter),
            'referringDomain' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\StringConverter),
            'remoteId' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\StringConverter),
            'remotePreviewImageUrl' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\ScalarConverter),
            'scheduledEnd' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\ScalarConverter),
            'scheduledStart' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\ScalarConverter),
            'start' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\ScalarConverter),
            'status' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\EnumConverter),
            'urlParameterValue' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\StringConverter),
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
