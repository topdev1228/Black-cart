<?php
declare(strict_types=1);

namespace Tests\Fixtures\Domains\Shopify\Traits;

trait ShopifyBulkOperationResponsesTestData
{
    public static function getShopifyBulkOperationCreateSuccessResponse(): array
    {
        return [
            'data' => [
                'bulkOperationRunQuery' => [
                    'bulkOperation' => [
                        'id' => 'gid://shopify/BulkOperation/1',
                        'status' => 'CREATED',
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

    public static function getShopifyBulkOperationCreateErrorResponse(): array
    {
        return [
            'data' => [
                'bulkOperationRunQuery' => [
                    'bulkOperation' => null,
                    'userErrors' => [
                        [
                            'field' => [
                                'query',
                            ],
                            'message' => 'Query is invalid',
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

    public static function getShopifyBulkOperationFinishFileUrlSuccessResponse(): array
    {
        return [
            'data' => [
                'node' => [
                    'url' => 'https://shopify.com/data.jsonl',
                    'partialDataUrl' => '',
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
}
