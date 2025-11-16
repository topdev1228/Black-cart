<?php
declare(strict_types=1);

namespace Tests\Fixtures\Domains\Orders\Traits;

trait ShopifyGetTransactionResponsesTestData
{
    public static function getTransactionClientErrorResponse(): array
    {
        return [
            'errors' => [
                [
                    'message' => "Field 'presentMoney' doesn't exist on type 'MoneyBag'",
                    'locations' => [
                        [
                            'line' => 12,
                            'column' => 21,
                        ],
                    ],
                    'path' => [
                        'query',
                        'node',
                        '... on OrderTransaction',
                        'amountSet',
                        'presentMoney',
                    ],
                    'extensions' => [
                        'code' => 'undefinedField',
                        'typeName' => 'MoneyBag',
                        'fieldName' => 'presentMoney',
                    ],
                ],
            ],
        ];
    }

    public static function getTransactionNotFoundSuccessResponse(): array
    {
        return [
            'data' => [
                'node' => null,
            ],
            'extensions' => [
                'cost' => [
                    'requestedQueryCost' => 2,
                    'actualQueryCost' => 1,
                    'throttleStatus' => [
                        'maximumAvailable' => 2000.0,
                        'currentlyAvailable' => 1999,
                        'restoreRate' => 100.0,
                    ],
                ],
            ],
        ];
    }

    public static function getAuthorizationTransactionSuccessResponse(): array
    {
        return [
            'data' => [
                'node' => [
                    'id' => 'gid://shopify/OrderTransaction/5488058826881',
                    'kind' => 'AUTHORIZATION',
                    'authorizationExpiresAt' => '2024-03-13T21:52:55Z',
                    'amountSet' => [
                        'shopMoney' => [
                            'amount' => '698.38',
                            'currencyCode' => 'CAD',
                        ],
                        'presentmentMoney' => [
                            'amount' => '516.8',
                            'currencyCode' => 'USD',
                        ],
                    ],
                    'parentTransaction' => null,
                ],
            ],
            'extensions' => [
                'cost' => [
                'requestedQueryCost' => 2,
                    'actualQueryCost' => 2,
                    'throttleStatus' => [
                        'maximumAvailable' => 2000.0,
                        'currentlyAvailable' => 1998,
                        'restoreRate' => 100.0,
                    ],
                ],
            ],
        ];
    }

    public static function getAuthorizationTransactionNullAuthorizationExpiresAtSuccessResponse(): array
    {
        return [
            'data' => [
                'node' => [
                    'id' => 'gid://shopify/OrderTransaction/5488058826881',
                    'kind' => 'AUTHORIZATION',
                    'authorizationExpiresAt' => null,
                    'amountSet' => [
                        'shopMoney' => [
                            'amount' => '698.38',
                            'currencyCode' => 'CAD',
                        ],
                        'presentmentMoney' => [
                            'amount' => '516.8',
                            'currencyCode' => 'USD',
                        ],
                    ],
                    'parentTransaction' => null,
                ],
            ],
            'extensions' => [
                'cost' => [
                'requestedQueryCost' => 2,
                    'actualQueryCost' => 2,
                    'throttleStatus' => [
                        'maximumAvailable' => 2000.0,
                        'currentlyAvailable' => 1998,
                        'restoreRate' => 100.0,
                    ],
                ],
            ],
        ];
    }

    public static function getSaleTransactionSuccessResponse(): array
    {
        return [
            'data' =>  [
                'node' =>  [
                    'id' =>  'gid://shopify/OrderTransaction/5488058630273',
                    'kind' =>  'SALE',
                    'authorizationExpiresAt' =>  null,
                    'amountSet' =>  [
                        'shopMoney' => [
                            'amount' => '698.38',
                            'currencyCode' => 'CAD',
                        ],
                        'presentmentMoney' => [
                            'amount' => '516.8',
                            'currencyCode' => 'USD',
                        ],
                    ],
                    'parentTransaction' =>  null,
                ],
            ],
            'extensions' =>  [
                'cost' =>  [
                    'requestedQueryCost' =>  2,
                    'actualQueryCost' =>  2,
                    'throttleStatus' =>  [
                        'maximumAvailable' =>  2000.0,
                        'currentlyAvailable' =>  1998,
                        'restoreRate' =>  100.0,
                    ],
                ],
            ],
        ];
    }

    public static function getCaptureTransactionSuccessResponse(): array
    {
        return [
            'data' =>  [
                'node' =>  [
                    'id' =>  'gid://shopify/OrderTransaction/5488059121793',
                    'kind' =>  'CAPTURE',
                    'authorizationExpiresAt' =>  null,
                    'amountSet' =>  [
                        'shopMoney' => [
                            'amount' => '698.38',
                            'currencyCode' => 'CAD',
                        ],
                        'presentmentMoney' => [
                            'amount' => '516.8',
                            'currencyCode' => 'USD',
                        ],
                    ],
                    'parentTransaction' =>  [
                        'id' =>  'gid://shopify/OrderTransaction/5488058826881',
                    ],
                ],
            ],
            'extensions' =>  [
                'cost' =>  [
                    'requestedQueryCost' =>  2,
                    'actualQueryCost' =>  2,
                    'throttleStatus' =>  [
                        'maximumAvailable' =>  2000.0,
                        'currentlyAvailable' =>  1998,
                        'restoreRate' =>  100.0,
                    ],
                ],
            ],
        ];
    }
}
