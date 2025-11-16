<?php
declare(strict_types=1);

namespace Tests\Fixtures\Domains\Billings\Traits;

trait ShopifyCurrentAppInstallationResponsesTestData
{
    public static function getShopifyCurrentAppInstallationQuerySuccessResponse(): array
    {
        return [
            'data' => [
                'currentAppInstallation' => [
                    'id' => 'gid://shopify/AppInstallation/432744464523',
                    'activeSubscriptions' => [
                        [
                            'id' => 'gid://shopify/AppSubscription/1234567890',
                            'status' => 'ACTIVE',
                        ],
                    ],
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

    public static function getShopifyCurrentAppInstallationQueryErrorResponse(): array
    {
        return [
            'errors' => [
                [
                    'message' => "Field 'ids' doesn't exist on type 'AppInstallation'",
                    'locations' => [
                        [
                            'line' => 3,
                            'column' => 17,
                        ],
                    ],
                    'path' => [
                        'query',
                        'currentAppInstallation',
                        'ids',
                    ],
                    'extensions' => [
                        'code' => 'undefinedField',
                        'typeName' => 'AppInstallation',
                        'fieldName' => 'ids',
                    ],
                ],
            ],
        ];
    }
}
