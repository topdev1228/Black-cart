<?php
declare(strict_types=1);

namespace Tests\Fixtures\Domains\Shopify\Traits;

trait ShopifyMetafieldsSetResponsesTestData
{
    public static function getShopifyMetafieldsSetForProgramSavedFixedDepositUnlimitedMaxTbybItemSuccessResponse(): array
    {
        return [
            'data' => [
                'metafieldsSet' => [
                    'metafields' => [
                        [
                            'id' => 'gid://shopify/Metafield/1',
                            'namespace' => 'blackcart',
                            'key' => 'program_name',
                            'value' => 'Try Before You Buy',
                            'type' => 'single_line_text_field',
                        ],
                        [
                            'id' => 'gid://shopify/Metafield/2',
                            'namespace' => 'blackcart',
                            'key' => 'selling_plan_group_id',
                            'value' => 'gid://shopify/SellingPlanGroup/12345',
                            'type' => 'single_line_text_field',
                        ],
                        [
                            'id' => 'gid://shopify/Metafield/3',
                            'namespace' => 'blackcart',
                            'key' => 'selling_plan_id',
                            'value' => 'gid://shopify/SellingPlan/56789',
                            'type' => 'single_line_text_field',
                        ],
                        [
                            'id' => 'gid://shopify/Metafield/4',
                            'namespace' => 'blackcart',
                            'key' => 'try_period_days',
                            'value' => '7',
                            'type' => 'number_integer',
                        ],
                        [
                            'id' => 'gid://shopify/Metafield/5',
                            'namespace' => 'blackcart',
                            'key' => 'min_tbyb_items',
                            'value' => '1',
                            'type' => 'single_line_text_field',
                        ],
                        [
                            'id' => 'gid://shopify/Metafield/6',
                            'namespace' => 'blackcart',
                            'key' => 'max_tbyb_items',
                            'value' => 'unlimited',
                            'type' => 'single_line_text_field',
                        ],
                        [
                            'id' => 'gid://shopify/Metafield/7',
                            'namespace' => 'blackcart',
                            'key' => 'deposit_type',
                            'value' => 'fixed',
                            'type' => 'single_line_text_field',
                        ],
                        [
                            'id' => 'gid://shopify/Metafield/8',
                            'namespace' => 'blackcart',
                            'key' => 'deposit_fixed',
                            'value' => '{"amount":"25.00","currency_code":"USD"}',
                            'type' => 'money',
                        ],
                        [
                            'id' => 'gid://shopify/Metafield/9',
                            'namespace' => 'blackcart',
                            'key' => 'deposit_percentage',
                            'value' => '0',
                            'type' => 'number_integer',
                        ],
                    ],
                    'userErrors' => [],
                ],
            ],
            'extensions' => [
                'cost' => [
                    'requestedQueryCost' => 10,
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

    public static function getShopifyMetafieldsSetForProgramSavedPercentageDepositLimitedMaxTbybItemSuccessResponse(): array
    {
        return [
            'data' => [
                'metafieldsSet' => [
                    'metafields' => [
                        [
                            'id' => 'gid://shopify/Metafield/1',
                            'namespace' => 'blackcart',
                            'key' => 'program_name',
                            'value' => 'Try Before You Buy',
                            'type' => 'single_line_text_field',
                        ],
                        [
                            'id' => 'gid://shopify/Metafield/2',
                            'namespace' => 'blackcart',
                            'key' => 'selling_plan_group_id',
                            'value' => 'gid://shopify/SellingPlanGroup/12345',
                            'type' => 'single_line_text_field',
                        ],
                        [
                            'id' => 'gid://shopify/Metafield/3',
                            'namespace' => 'blackcart',
                            'key' => 'selling_plan_id',
                            'value' => 'gid://shopify/SellingPlan/56789',
                            'type' => 'single_line_text_field',
                        ],
                        [
                            'id' => 'gid://shopify/Metafield/4',
                            'namespace' => 'blackcart',
                            'key' => 'try_period_days',
                            'value' => '7',
                            'type' => 'number_integer',
                        ],
                        [
                            'id' => 'gid://shopify/Metafield/5',
                            'namespace' => 'blackcart',
                            'key' => 'min_tbyb_items',
                            'value' => '1',
                            'type' => 'single_line_text_field',
                        ],
                        [
                            'id' => 'gid://shopify/Metafield/6',
                            'namespace' => 'blackcart',
                            'key' => 'max_tbyb_items',
                            'value' => '4',
                            'type' => 'single_line_text_field',
                        ],
                        [
                            'id' => 'gid://shopify/Metafield/7',
                            'namespace' => 'blackcart',
                            'key' => 'deposit_type',
                            'value' => 'percentage',
                            'type' => 'single_line_text_field',
                        ],
                        [
                            'id' => 'gid://shopify/Metafield/8',
                            'namespace' => 'blackcart',
                            'key' => 'deposit_fixed',
                            'value' => '{"amount":"0","currency_code":"USD"}',
                            'type' => 'money',
                        ],
                        [
                            'id' => 'gid://shopify/Metafield/9',
                            'namespace' => 'blackcart',
                            'key' => 'deposit_percentage',
                            'value' => '10',
                            'type' => 'number_integer',
                        ],
                    ],
                    'userErrors' => [],
                ],
            ],
            'extensions' => [
                'cost' => [
                    'requestedQueryCost' => 10,
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

    public static function getShopifyMetafieldsSetForStoreStatusChangedSuccessResponse(string $status = 'active'): array
    {
        return [
            'data' => [
                'metafieldsSet' => [
                    'metafields' => [
                        [
                            'id' => 'gid://shopify/Metafield/10',
                            'namespace' => 'blackcart',
                            'key' => 'store_status',
                            'value' => $status,
                            'type' => 'single_line_text_field',
                        ],
                    ],
                    'userErrors' => [],
                ],
            ],
            'extensions' => [
                'cost' => [
                    'requestedQueryCost' => 10,
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

    public static function getShopifyMetafieldsSetForSubscriptionStatusChangedSuccessResponse(string $status = 'active'): array
    {
        return [
            'data' => [
                'metafieldsSet' => [
                    'metafields' => [
                        [
                            'id' => 'gid://shopify/Metafield/11',
                            'namespace' => 'blackcart',
                            'key' => 'subscription_status',
                            'value' => $status,
                            'type' => 'single_line_text_field',
                        ],
                    ],
                    'userErrors' => [],
                ],
            ],
            'extensions' => [
                'cost' => [
                    'requestedQueryCost' => 10,
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

    public static function getShopifyMetafieldsSetErrorResponse(): array
    {
        return [
            'data' => [
                'metafieldsSet' => [
                    'metafields' => [],
                    'userErrors' => [
                        [
                            'field' => [
                                'metafields',
                                '0',
                                'value',
                            ],
                            'message' => "Value must be a stringified JSON object with amount (numeric) and currency_code (string matching the shop's currency) fields.",
                        ],
                    ],
                ],
            ],
            'extensions' => [
                'cost' => [
                    'requestedQueryCost' => 10,
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
