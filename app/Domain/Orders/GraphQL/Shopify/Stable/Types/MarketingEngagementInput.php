<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

/**
 * @property bool $isCumulative
 * @property mixed $occurredOn
 * @property mixed $utcOffset
 * @property \App\Domain\Orders\GraphQL\Shopify\Stable\Types\MoneyInput|null $adSpend
 * @property int|null $clicksCount
 * @property int|null $commentsCount
 * @property int|null $complaintsCount
 * @property int|null $failsCount
 * @property int|null $favoritesCount
 * @property mixed|null $firstTimeCustomers
 * @property int|null $impressionsCount
 * @property mixed|null $orders
 * @property mixed|null $returningCustomers
 * @property \App\Domain\Orders\GraphQL\Shopify\Stable\Types\MoneyInput|null $sales
 * @property int|null $sendsCount
 * @property int|null $sessionsCount
 * @property int|null $sharesCount
 * @property int|null $uniqueClicksCount
 * @property int|null $uniqueViewsCount
 * @property int|null $unsubscribesCount
 * @property int|null $viewsCount
 */
class MarketingEngagementInput extends \Spawnia\Sailor\ObjectLike
{
    /**
     * @param bool $isCumulative
     * @param mixed $occurredOn
     * @param mixed $utcOffset
     * @param \App\Domain\Orders\GraphQL\Shopify\Stable\Types\MoneyInput|null $adSpend
     * @param int|null $clicksCount
     * @param int|null $commentsCount
     * @param int|null $complaintsCount
     * @param int|null $failsCount
     * @param int|null $favoritesCount
     * @param mixed|null $firstTimeCustomers
     * @param int|null $impressionsCount
     * @param mixed|null $orders
     * @param mixed|null $returningCustomers
     * @param \App\Domain\Orders\GraphQL\Shopify\Stable\Types\MoneyInput|null $sales
     * @param int|null $sendsCount
     * @param int|null $sessionsCount
     * @param int|null $sharesCount
     * @param int|null $uniqueClicksCount
     * @param int|null $uniqueViewsCount
     * @param int|null $unsubscribesCount
     * @param int|null $viewsCount
     */
    public static function make(
        $isCumulative,
        $occurredOn,
        $utcOffset,
        $adSpend = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $clicksCount = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $commentsCount = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $complaintsCount = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $failsCount = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $favoritesCount = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $firstTimeCustomers = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $impressionsCount = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $orders = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $returningCustomers = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $sales = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $sendsCount = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $sessionsCount = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $sharesCount = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $uniqueClicksCount = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $uniqueViewsCount = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $unsubscribesCount = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $viewsCount = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
    ): self {
        $instance = new self;

        if ($isCumulative !== self::UNDEFINED) {
            $instance->isCumulative = $isCumulative;
        }
        if ($occurredOn !== self::UNDEFINED) {
            $instance->occurredOn = $occurredOn;
        }
        if ($utcOffset !== self::UNDEFINED) {
            $instance->utcOffset = $utcOffset;
        }
        if ($adSpend !== self::UNDEFINED) {
            $instance->adSpend = $adSpend;
        }
        if ($clicksCount !== self::UNDEFINED) {
            $instance->clicksCount = $clicksCount;
        }
        if ($commentsCount !== self::UNDEFINED) {
            $instance->commentsCount = $commentsCount;
        }
        if ($complaintsCount !== self::UNDEFINED) {
            $instance->complaintsCount = $complaintsCount;
        }
        if ($failsCount !== self::UNDEFINED) {
            $instance->failsCount = $failsCount;
        }
        if ($favoritesCount !== self::UNDEFINED) {
            $instance->favoritesCount = $favoritesCount;
        }
        if ($firstTimeCustomers !== self::UNDEFINED) {
            $instance->firstTimeCustomers = $firstTimeCustomers;
        }
        if ($impressionsCount !== self::UNDEFINED) {
            $instance->impressionsCount = $impressionsCount;
        }
        if ($orders !== self::UNDEFINED) {
            $instance->orders = $orders;
        }
        if ($returningCustomers !== self::UNDEFINED) {
            $instance->returningCustomers = $returningCustomers;
        }
        if ($sales !== self::UNDEFINED) {
            $instance->sales = $sales;
        }
        if ($sendsCount !== self::UNDEFINED) {
            $instance->sendsCount = $sendsCount;
        }
        if ($sessionsCount !== self::UNDEFINED) {
            $instance->sessionsCount = $sessionsCount;
        }
        if ($sharesCount !== self::UNDEFINED) {
            $instance->sharesCount = $sharesCount;
        }
        if ($uniqueClicksCount !== self::UNDEFINED) {
            $instance->uniqueClicksCount = $uniqueClicksCount;
        }
        if ($uniqueViewsCount !== self::UNDEFINED) {
            $instance->uniqueViewsCount = $uniqueViewsCount;
        }
        if ($unsubscribesCount !== self::UNDEFINED) {
            $instance->unsubscribesCount = $unsubscribesCount;
        }
        if ($viewsCount !== self::UNDEFINED) {
            $instance->viewsCount = $viewsCount;
        }

        return $instance;
    }

    protected function converters(): array
    {
        static $converters;

        return $converters ??= [
            'isCumulative' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\BooleanConverter),
            'occurredOn' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\ScalarConverter),
            'utcOffset' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\ScalarConverter),
            'adSpend' => new \Spawnia\Sailor\Convert\NullConverter(new \App\Domain\Orders\GraphQL\Shopify\Stable\Types\MoneyInput),
            'clicksCount' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\IntConverter),
            'commentsCount' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\IntConverter),
            'complaintsCount' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\IntConverter),
            'failsCount' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\IntConverter),
            'favoritesCount' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\IntConverter),
            'firstTimeCustomers' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\ScalarConverter),
            'impressionsCount' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\IntConverter),
            'orders' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\ScalarConverter),
            'returningCustomers' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\ScalarConverter),
            'sales' => new \Spawnia\Sailor\Convert\NullConverter(new \App\Domain\Orders\GraphQL\Shopify\Stable\Types\MoneyInput),
            'sendsCount' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\IntConverter),
            'sessionsCount' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\IntConverter),
            'sharesCount' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\IntConverter),
            'uniqueClicksCount' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\IntConverter),
            'uniqueViewsCount' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\IntConverter),
            'unsubscribesCount' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\IntConverter),
            'viewsCount' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\IntConverter),
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
