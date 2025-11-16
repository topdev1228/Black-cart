<?php
declare(strict_types=1);

namespace Tests\Fixtures\Domains\Billings\Traits;

trait ShopifyQueryAppSubscriptionResponsesTestData
{
    public static function getShopifyQueryAppSubscriptionCurrentPeriodEndSuccessResponse(
        string $shopifyAppSubscriptionId = 'gid://shopify/AppSubscription/1',
        string $currentPeriodEnd = '2024-02-19T17:00:00Z'
    ): array {
        return [
            'data' => [
                'node' => [
                    'id' => $shopifyAppSubscriptionId,
                    'currentPeriodEnd' => $currentPeriodEnd,
                ],
            ],
            'extensions' => [
                'cost' => [
                    'requestedQueryCost' => 62,
                    'actualQueryCost' => 17,
                ],
            ],
        ];
    }

    public static function getShopifyQueryAppSubscriptionNullCurrentPeriodEndSuccessResponse(): array
    {
        return [
            'data' => [
                'node' => [
                    'id' => 'gid://shopify/AppSubscription/4028497976',
                    'currentPeriodEnd' => null,
                ],
            ],
            'extensions' => [
                'cost' => [
                    'requestedQueryCost' => 62,
                    'actualQueryCost' => 17,
                ],
            ],
        ];
    }

    public static function getShopifyQueryAppSubscriptionNullResponse(): array
    {
        return [
            'data' => [
                'node' => null,
            ],
            'extensions' => [
                'cost' => [
                    'requestedQueryCost' => 13,
                    'actualQueryCost' => 10,
                    'throttleStatus' => [
                        'maximumAvailable' => 1000.0,
                        'currentlyAvailable' => 990,
                        'restoreRate' => 50.0,
                    ],
                ],
            ],
        ];
    }
}
