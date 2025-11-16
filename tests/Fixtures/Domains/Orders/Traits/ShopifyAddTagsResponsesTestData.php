<?php
declare(strict_types=1);

namespace Tests\Fixtures\Domains\Orders\Traits;

trait ShopifyAddTagsResponsesTestData
{
    public static function getShopifyAddTagsSuccessResponse(): array
    {
        return [
            'data' => [
                'tagsAdd' => [
                    'userErrors' => [],
                ],
            ],
            'extensions' => [
                'cost' => [
                    'requestedQueryCost' => 10,
                    'actualQueryCost' => 10,
                    'throttleStatus' => [
                        'maximumAvailable' => 2000.0,
                        'currentlyAvailable' => 1990,
                        'restoreRate' => 100.0,
                    ],
                ],
            ],
        ];
    }

    public static function getShopifyAddTagsErrorResponse(): array
    {
        return [
            'data' => [
                'tagsAdd' => [
                    'userErrors' => [
                        [
                            'field' => [
                                'id',
                            ],
                            'message' => 'Order does not exist',
                        ],
                    ],
                ],
            ],
            'extensions' => [
                'cost' => [
                    'requestedQueryCost' => 10,
                    'actualQueryCost' => 10,
                    'throttleStatus' => [
                        'maximumAvailable' => 2000.0,
                        'currentlyAvailable' => 1990,
                        'restoreRate' => 100.0,
                    ],
                ],
            ],
        ];
    }
}
