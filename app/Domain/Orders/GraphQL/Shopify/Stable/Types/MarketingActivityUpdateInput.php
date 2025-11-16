<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

/**
 * @property int|string $id
 * @property \App\Domain\Orders\GraphQL\Shopify\Stable\Types\MarketingActivityBudgetInput|null $budget
 * @property mixed|null $errorOccurredAt
 * @property mixed|null $errors
 * @property string|null $eventContext
 * @property string|null $formData
 * @property array<int|string>|null $marketedResources
 * @property int|string|null $marketingRecommendationId
 * @property mixed|null $scheduledToEndAt
 * @property mixed|null $scheduledToStartAt
 * @property string|null $status
 * @property string|null $targetStatus
 * @property string|null $title
 * @property bool|null $trackingOpens
 * @property bool|null $useExternalEditor
 * @property \App\Domain\Orders\GraphQL\Shopify\Stable\Types\UTMInput|null $utm
 */
class MarketingActivityUpdateInput extends \Spawnia\Sailor\ObjectLike
{
    /**
     * @param int|string $id
     * @param \App\Domain\Orders\GraphQL\Shopify\Stable\Types\MarketingActivityBudgetInput|null $budget
     * @param mixed|null $errorOccurredAt
     * @param mixed|null $errors
     * @param string|null $eventContext
     * @param string|null $formData
     * @param array<int|string>|null $marketedResources
     * @param int|string|null $marketingRecommendationId
     * @param mixed|null $scheduledToEndAt
     * @param mixed|null $scheduledToStartAt
     * @param string|null $status
     * @param string|null $targetStatus
     * @param string|null $title
     * @param bool|null $trackingOpens
     * @param bool|null $useExternalEditor
     * @param \App\Domain\Orders\GraphQL\Shopify\Stable\Types\UTMInput|null $utm
     */
    public static function make(
        $id,
        $budget = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $errorOccurredAt = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $errors = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $eventContext = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $formData = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $marketedResources = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $marketingRecommendationId = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $scheduledToEndAt = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $scheduledToStartAt = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $status = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $targetStatus = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $title = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $trackingOpens = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $useExternalEditor = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $utm = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
    ): self {
        $instance = new self;

        if ($id !== self::UNDEFINED) {
            $instance->id = $id;
        }
        if ($budget !== self::UNDEFINED) {
            $instance->budget = $budget;
        }
        if ($errorOccurredAt !== self::UNDEFINED) {
            $instance->errorOccurredAt = $errorOccurredAt;
        }
        if ($errors !== self::UNDEFINED) {
            $instance->errors = $errors;
        }
        if ($eventContext !== self::UNDEFINED) {
            $instance->eventContext = $eventContext;
        }
        if ($formData !== self::UNDEFINED) {
            $instance->formData = $formData;
        }
        if ($marketedResources !== self::UNDEFINED) {
            $instance->marketedResources = $marketedResources;
        }
        if ($marketingRecommendationId !== self::UNDEFINED) {
            $instance->marketingRecommendationId = $marketingRecommendationId;
        }
        if ($scheduledToEndAt !== self::UNDEFINED) {
            $instance->scheduledToEndAt = $scheduledToEndAt;
        }
        if ($scheduledToStartAt !== self::UNDEFINED) {
            $instance->scheduledToStartAt = $scheduledToStartAt;
        }
        if ($status !== self::UNDEFINED) {
            $instance->status = $status;
        }
        if ($targetStatus !== self::UNDEFINED) {
            $instance->targetStatus = $targetStatus;
        }
        if ($title !== self::UNDEFINED) {
            $instance->title = $title;
        }
        if ($trackingOpens !== self::UNDEFINED) {
            $instance->trackingOpens = $trackingOpens;
        }
        if ($useExternalEditor !== self::UNDEFINED) {
            $instance->useExternalEditor = $useExternalEditor;
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
            'id' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\IDConverter),
            'budget' => new \Spawnia\Sailor\Convert\NullConverter(new \App\Domain\Orders\GraphQL\Shopify\Stable\Types\MarketingActivityBudgetInput),
            'errorOccurredAt' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\ScalarConverter),
            'errors' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\ScalarConverter),
            'eventContext' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\StringConverter),
            'formData' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\StringConverter),
            'marketedResources' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\ListConverter(new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\IDConverter))),
            'marketingRecommendationId' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\IDConverter),
            'scheduledToEndAt' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\ScalarConverter),
            'scheduledToStartAt' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\ScalarConverter),
            'status' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\EnumConverter),
            'targetStatus' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\EnumConverter),
            'title' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\StringConverter),
            'trackingOpens' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\BooleanConverter),
            'useExternalEditor' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\BooleanConverter),
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
