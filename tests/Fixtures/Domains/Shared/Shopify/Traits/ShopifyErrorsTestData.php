<?php
declare(strict_types=1);

namespace Tests\Fixtures\Domains\Shared\Shopify\Traits;

use App\Domain\Shared\Exceptions\Shopify\ShopifyAuthenticationException;
use App\Domain\Shared\Exceptions\Shopify\ShopifyClientException;
use App\Domain\Shared\Exceptions\Shopify\ShopifyMutationServerException;
use App\Domain\Shared\Exceptions\Shopify\ShopifyServerException;

trait ShopifyErrorsTestData
{
    public static function shopifyErrorExceptionsForMutationProvider(): array
    {
        return array_merge(
            static::shopifyErrorExceptionsProvider(),
            [
                'On Shopify mutation server error' => [
                    static::getShopifyAdminApiErrorResponse(),
                    200,
                    ShopifyMutationServerException::class,
                ],
            ],
        );
    }

    public static function shopifyErrorExceptionsProvider(): array
    {
        return [
            'On HTTP request error' => [
                ['errors' => '400 error'],
                400,
                ShopifyClientException::class,
            ],
            'On HTTP server error' => [
                ['errors' => '500 error'],
                500,
                ShopifyServerException::class,
            ],
            'On Shopify authentication error' => [
                static::getShopifyAdminApiAuthenticationErrorResponse(),
                401,
                ShopifyAuthenticationException::class,
            ],
        ];
    }

    public static function getShopifyAdminApiAuthenticationErrorResponse(): array
    {
        return [
            'errors' => '[API] Invalid API key or access token (unrecognized login or wrong password)',
        ];
    }

    public static function getShopifyAdminApiErrorResponse(array $path = ['examplePath']): array
    {
        return [
            'errors' => [
                [
                    'message' => 'syntax error, unexpected invalid token ("-"), expecting RCURLY at [12, 18]',
                    'locations' => [
                        [
                            'line' => 12,
                            'column' => 18,
                        ],
                    ],
                    'path' => $path,
                ],
            ],
        ];
    }
}
