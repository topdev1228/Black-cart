<?php
declare(strict_types=1);

namespace Tests\Fixtures\Domains\Billings\Traits;

use App\Domain\Billings\Enums\SubscriptionStatus;

trait WebhookShopifyAppSubscriptionsUpdateTestData
{
    public static function getAppSubscriptionsUpdateAllStatusesProvider(): array
    {
        return [
            'pending' => [
                'gid://shopify/AppSubscription/1',
                SubscriptionStatus::PENDING,
                static::getAppSubscriptionsUpdateByStatus('gid://shopify/AppSubscription/1', 'pending'),
                '',
                '',
            ],
            'active' => [
                'gid://shopify/AppSubscription/2',
                SubscriptionStatus::ACTIVE,
                static::getAppSubscriptionsUpdateByStatus('gid://shopify/AppSubscription/2', 'active'),
                'activated_at',
                '2023-12-19 19:00:00',
            ],
            'cancelled' => [
                'gid://shopify/AppSubscription/3',
                SubscriptionStatus::CANCELLED,
                static::getAppSubscriptionsUpdateByStatus('gid://shopify/AppSubscription/3', 'cancelled'),
                'deactivated_at',
                '2023-12-19 19:00:00',
            ],
            'declined' => [
                'gid://shopify/AppSubscription/4',
                SubscriptionStatus::DECLINED,
                static::getAppSubscriptionsUpdateByStatus('gid://shopify/AppSubscription/4', 'declined'),
                'deactivated_at',
                '2023-12-19 19:00:00',
            ],
            'expired' => [
                'gid://shopify/AppSubscription/5',
                SubscriptionStatus::EXPIRED,
                static::getAppSubscriptionsUpdateByStatus('gid://shopify/AppSubscription/5', 'expired'),
                'deactivated_at',
                '2023-12-19 19:00:00',
            ],
            'frozen' => [
                'gid://shopify/AppSubscription/6',
                SubscriptionStatus::FROZEN,
                static::getAppSubscriptionsUpdateByStatus('gid://shopify/AppSubscription/6', 'frozen'),
                'deactivated_at',
                '2023-12-19 19:00:00',
            ],
        ];
    }

    protected static function getAppSubscriptionsUpdateByStatus(string $shopifyAppSubscriptionId, string $status): array
    {
        return [
            'admin_graphql_api_id' => $shopifyAppSubscriptionId,
            'name' => 'Shopify app_subscriptions/update webook test subscription plan',
            'status' => $status,
            'admin_graphql_api_shop_id' => 'gid://shopify/Shop/548380009',
            'created_at' => '2023-12-10T19:00:00-00:00',
            'updated_at' => '2023-12-19T19:00:00-00:00',
            'currency' => 'USD',
            'capped_amount' => '20.0',
        ];
    }
}
