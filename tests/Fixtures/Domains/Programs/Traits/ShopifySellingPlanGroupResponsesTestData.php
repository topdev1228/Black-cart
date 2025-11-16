<?php
declare(strict_types=1);

namespace Tests\Fixtures\Domains\Programs\Traits;

trait ShopifySellingPlanGroupResponsesTestData
{
    private static function getShopifySellingPlanGroupCreateSuccessResponse(): array
    {
        return [
            'data' => [
                'sellingPlanGroupCreate' => [
                    'sellingPlanGroup' => [
                        'id' => 'gid://shopify/SellingPlanGroup/12345',
                        'sellingPlans' => [
                            'edges' => [
                                [
                                    'node' => [
                                        'id' => 'gid://shopify/SellingPlan/56789',
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

    private static function getShopifySellingPlanGroupCreateUserErrorResponse(): array
    {
        return [
            'data' => [
                'sellingPlanGroupCreate' => [
                    'sellingPlanGroup' => null,
                    'userErrors' => [
                        [
                            'field' => [
                                'input',
                                'sellingPlansToCreate',
                                '0',
                                'billingPolicy',
                                'fixed',
                                'checkoutCharge',
                                'value',
                            ],
                            'message' => 'Checkout charge value must be equal to or larger than 0 when checkout_charge_type is PRICE',
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

    private static function getShopifySellingPlanGroupUpdateSuccessResponse(): array
    {
        return [
            'data' => [
                'sellingPlanGroupUpdate' => [
                    'sellingPlanGroup' => [
                        'id' => 'gid://shopify/SellingPlanGroup/12345',
                        'sellingPlans' => [
                            'edges' => [
                                [
                                    'node' => [
                                        'id' => 'gid://shopify/SellingPlan/56789',
                                        'name' => '14-day trial',
                                        'options' => [
                                            'Try for 14 days',
                                        ],
                                        'billingPolicy' => [
                                            'checkoutCharge' => [
                                                'type' => 'PRICE',
                                                'value' => [
                                                    'amount' => '100.0',
                                                    'currencyCode' => 'CAD',
                                                ],
                                            ],
                                            'remainingBalanceChargeExactTime' => null,
                                            'remainingBalanceChargeTimeAfterCheckout' => 'P14D',
                                            'remainingBalanceChargeTrigger' => 'TIME_AFTER_CHECKOUT',
                                        ],
                                        'deliveryPolicy' => [
                                            'anchors' => [],
                                            'cutoff' => null,
                                            'fulfillmentExactTime' => null,
                                            'fulfillmentTrigger' => 'ASAP',
                                            'intent' => 'FULFILLMENT_BEGIN',
                                            'preAnchorBehavior' => 'ASAP',
                                        ],
                                        'inventoryPolicy' => [
                                            'reserve' => 'ON_SALE',
                                        ],
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

    private static function getShopifySellingPlanGroupUpdateUserErrorResponse(): array
    {
        return [
            'data' => [
                'sellingPlanGroupUpdate' => [
                    'sellingPlanGroup' => [
                        'id' => 'gid://shopify/SellingPlanGroup/12345',
                        'sellingPlans' => [
                            'edges' => [
                                [
                                    'node' => [
                                        'id' => 'gid://shopify/SellingPlan/56789',
                                    ],
                                ],
                            ],
                        ],
                    ],
                    'userErrors' => [
                        [
                            'field' => [
                                'input',
                                'sellingPlansToUpdate',
                                '0',
                                'billingPolicy',
                                'fixed',
                                'checkoutCharge',
                                'value',
                            ],
                            'message' => 'Checkout charge value must be equal to or larger than 0 when checkout_charge_type is PRICE',
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

    private static function getShopifySellingPlanGroupAddProductsSuccessResponse(): array
    {
        return [
            'data' => [
                'sellingPlanGroupAddProducts' => [
                    'sellingPlanGroup' => [
                        'id' => 'gid://shopify/SellingPlanGroup/12345',
                        'productCount' => 3,
                        'productVariantCount' => 3,
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

    private static function getShopifySellingPlanGroupAddProductsErrorResponse(): array
    {
        return [
            'data' => [
                'sellingPlanGroupAddProductVariants' => [
                    'sellingPlanGroup' => [
                        'id' => 'gid://shopify/SellingPlanGroup/12345',
                        'productCount' => 3,
                        'productVariantCount' => 4,
                    ],
                    'userErrors' => [
                        [
                            'field' => [
                                'input',
                                'sellingPlanGroupAddProducts',
                                'productIds',
                            ],
                            'message' => 'Invalid product ids',
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

    private static function getShopifySellingPlanGroupAddProductVariantsSuccessResponse(): array
    {
        return [
            'data' => [
                'sellingPlanGroupAddProductVariants' => [
                    'sellingPlanGroup' => [
                        'id' => 'gid://shopify/SellingPlanGroup/12345',
                        'productCount' => 3,
                        'productVariantCount' => 3,
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

    private static function getShopifySellingPlanGroupAddProductVariantsErrorResponse(): array
    {
        return [
            'data' => [
                'sellingPlanGroupAddProductVariants' => [
                    'sellingPlanGroup' => [
                        'id' => 'gid://shopify/SellingPlanGroup/12345',
                        'productCount' => 3,
                        'productVariantCount' => 4,
                    ],
                    'userErrors' => [
                        [
                            'field' => [
                                'input',
                                'sellingPlanGroupAddProductVariants',
                                'productIds',
                            ],
                            'message' => 'Invalid product variant ids',
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

    private static function getShopifySellingPlanGroupRemoveProductsSuccessResponse(): array
    {
        return [
            'data' => [
                'sellingPlanGroupRemoveProducts' => [
                    'sellingPlanGroup' => [
                        'id' => 'gid://shopify/SellingPlanGroup/12345',
                        'productCount' => 3,
                        'productVariantCount' => 3,
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

    private static function getShopifySellingPlanGroupRemoveProductsErrorResponse(): array
    {
        return [
            'data' => [
                'sellingPlanGroupRemoveProductVariants' => [
                    'sellingPlanGroup' => [
                        'id' => 'gid://shopify/SellingPlanGroup/12345',
                        'productCount' => 3,
                        'productVariantCount' => 4,
                    ],
                    'userErrors' => [
                        [
                            'field' => [
                                'input',
                                'sellingPlanGroupRemoveProducts',
                                'productIds',
                            ],
                            'message' => 'Invalid product ids',
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

    private static function getShopifySellingPlanGroupRemoveProductVariantsSuccessResponse(): array
    {
        return [
            'data' => [
                'sellingPlanGroupRemoveProductVariants' => [
                    'sellingPlanGroup' => [
                        'id' => 'gid://shopify/SellingPlanGroup/12345',
                        'productCount' => 3,
                        'productVariantCount' => 3,
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

    private static function getShopifySellingPlanGroupRemoveProductVariantsErrorResponse(): array
    {
        return [
            'data' => [
                'sellingPlanGroupRemoveProductVariants' => [
                    'sellingPlanGroup' => [
                        'id' => 'gid://shopify/SellingPlanGroup/12345',
                        'productCount' => 3,
                        'productVariantCount' => 4,
                    ],
                    'userErrors' => [
                        [
                            'field' => [
                                'input',
                                'sellingPlanGroupRemoveProductVariants',
                                'productIds',
                            ],
                            'message' => 'Invalid product variant ids',
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

    private static function getVariantInSellingPlanGroupResponse(): array
    {
        return [
            'data' => [
                'sellingPlanGroup' => [
                    'id' => 'gid://shopify/SellingPlanGroup/417824907',
                    'productVariant1' => true,
                    'productVariant2' => false,
                ],
            ],
            'extensions' => [
                'cost' => [
                    'requestedQueryCost' => 1,
                    'actualQueryCost' => 1,
                    'throttleStatus' => [
                        'maximumAvailable' => 1000.0,
                        'currentlyAvailable' => 999,
                        'restoreRate' => 50.0,
                    ],
                ],
            ],
        ];
    }

    private static function getSellingPlanProductResponse(): array
    {
        return [
            'data' => [
                'sellingPlanGroup' => [
                    'id' => 'gid://shopify/SellingPlanGroup/548176011',
                    'name' => 'QA TBYB',
                    'products' => [
                        'edges' => [
                            [
                                'node' => [
                                    'id' => 'gid://shopify/Product/7223659724939',
                                    'handle' => '7-shakra-bracelet',
                                ],
                            ],
                        ],
                    ],
                ],
            ],
            'extensions' => [
                'cost' => [
                    'requestedQueryCost' => 4,
                    'actualQueryCost' => 4,
                    'throttleStatus' => [
                        'maximumAvailable' => 2000.0,
                        'currentlyAvailable' => 1996,
                        'restoreRate' => 100.0,
                    ],
                ],
            ],
        ];
    }
}
