<?php
declare(strict_types=1);

namespace Tests\Fixtures\Domains\Billings\Traits;

trait ShopifyAppSubscriptionCreateResponsesTestData
{
    public static function getShopifyAppSubscriptionCreateSuccessResponse(): array
    {
        return [
            'data' => [
                'appSubscriptionCreate' => [
                    'confirmationUrl' => 'https://xxx.myshopify.com/admin/charges/4028497976/confirm_recurring_application_charge?signature=BAh7BzoHaWRsKwc4AB7wOhJhdXRvX2FjdGl2YXRlVA%3D%3D--987b3537018fdd69c50f13d6cbd3fba468e0e9a6',
                    'appSubscription' => [
                        'id' => 'gid://shopify/AppSubscription/1234567890',
                        'currentPeriodEnd' => null,
                        'status' => 'PENDING',
                        'test' => false,
                        'trialDays' => 30,
                        'lineItems' => [
                            [
                                'id' => 'gid://shopify/AppSubscriptionLineItem/recurring1313131313?v=1&index=0',
                                'plan' => [
                                    'pricingDetails' => [
                                        '__typename' => 'AppRecurringPricing',
                                    ],
                                ],
                            ],
                            [
                                'id' => 'gid://shopify/AppSubscriptionLineItem/usage1212121212?v=1&index=0',
                                'plan' => [
                                    'pricingDetails' => [
                                        '__typename' => 'AppUsagePricing',
                                    ],
                                ],
                            ],
                        ],
                    ],
                    'userErrors' => [],
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

    public static function getShopifyAppSubscriptionCreateErrorResponse(): array
    {
        return [
            'data' => [
                'appSubscriptionCreate' => [
                    'confirmationUrl' => null,
                    'appSubscription' => null,
                    'userErrors' => [
                        [
                            'field' => [
                                'input',
                                'appSubscriptionCreate',
                                'lineItems',
                                '0',
                                'plan',
                                'pricingDetails',
                                'cappedAmount',
                                'currencyCode',
                            ],
                            'message' => 'Currency code must be ISO 4217',
                        ],
                    ],
                ],
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
