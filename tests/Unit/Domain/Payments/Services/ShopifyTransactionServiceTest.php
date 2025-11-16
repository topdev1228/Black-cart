<?php
declare(strict_types=1);

namespace Tests\Unit\Domain\Payments\Services;

use App;
use App\Domain\Payments\Services\ShopifyTransactionService;
use App\Domain\Stores\Values\Store as StoreValue;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\ItemNotFoundException;
use Illuminate\Support\Sleep;
use Tests\TestCase;

class ShopifyTransactionServiceTest extends TestCase
{
    public function testItGetOrderTransactionsFromJobOnFirstTry(): void
    {
        App::context(store: StoreValue::builder()->create());
        $expectedResult = [
            'jobId' => 'test-job-id',
            'paymentReferenceId' => 'test-payment-reference-id',
        ];

        $sourceOrderId = 'test-source-order-id';
        $transactions = [
            [
                'amountSet' => [
                    'shopMoney' => [
                        'amount' => '4.44',
                        'currencyCode' => 'CAD',
                    ],
                    'presentmentMoney' => [
                        'amount' => '3.3',
                        'currencyCode' => 'USD',
                    ],
                ],
                'totalUnsettledSet' => [
                    'shopMoney' => [
                        'amount' => '0.0',
                        'currencyCode' => 'CAD',
                    ],
                ],
                'authorizationExpiresAt' => null,
                'createdAt' => '2024-01-26T18:48:16Z',
                'manuallyCapturable' => false,
                'id' => 'gid://shopify/OrderTransaction/6429975511179',
                'kind' => 'SALE',
                'status' => 'SUCCESS',
                'gateway' => 'shopify_payments',
                'test' => true,
                'paymentId' => 'risOALVUIL4AqYY6zpDqVsZPp',
            ],
            [
                'amountSet' => [
                    'shopMoney' => [
                        'amount' => '84.39',
                        'currencyCode' => 'CAD',
                    ],
                    'presentmentMoney' => [
                        'amount' => '62.77',
                        'currencyCode' => 'USD',
                    ],
                ],
                'totalUnsettledSet' => [
                    'shopMoney' => [
                        'amount' => '0.0',
                        'currencyCode' => 'CAD',
                    ],
                ],
                'authorizationExpiresAt' => null,
                'createdAt' => '2024-01-26T18:48:59Z',
                'manuallyCapturable' => false,
                'id' => 'gid://shopify/OrderTransaction/6429977477259',
                'kind' => 'SALE',
                'status' => 'SUCCESS',
                'gateway' => 'shopify_payments',
                'test' => true,
                'paymentId' => 'rE0TUavrl9hkFRBJr8KiIunGP',
            ],
            [
                'amountSet' => [
                    'shopMoney' => [
                        'amount' => '4.05',
                        'currencyCode' => 'CAD',
                    ],
                    'presentmentMoney' => [
                        'amount' => '3.0',
                        'currencyCode' => 'USD',
                    ],
                ],
                'totalUnsettledSet' => [
                    'shopMoney' => [
                        'amount' => '0.0',
                        'currencyCode' => 'CAD',
                    ],
                ],
                'authorizationExpiresAt' => '2024-02-29T16:58:03Z',
                'createdAt' => '2024-02-22T16:58:02Z',
                'manuallyCapturable' => false,
                'id' => 'gid://shopify/OrderTransaction/6480339927179',
                'kind' => 'AUTHORIZATION',
                'status' => 'SUCCESS',
                'gateway' => 'shopify_payments',
                'test' => true,
                'paymentId' => 'rIHKE0JRCGIW6hHL2E6spYEXf',
            ],
            [
                'amountSet' => [
                    'shopMoney' => [
                        'amount' => '1.35',
                        'currencyCode' => 'CAD',
                    ],
                    'presentmentMoney' => [
                        'amount' => '1.0',
                        'currencyCode' => 'USD',
                    ],
                ],
                'totalUnsettledSet' => [
                    'shopMoney' => [
                        'amount' => '1.35',
                        'currencyCode' => 'CAD',
                    ],
                ],
                'authorizationExpiresAt' => '2024-02-29T16:58:08Z',
                'createdAt' => '2024-02-22T16:58:08Z',
                'manuallyCapturable' => false,
                'id' => 'gid://shopify/OrderTransaction/6480340123787',
                'kind' => 'AUTHORIZATION',
                'status' => 'SUCCESS',
                'gateway' => 'shopify_payments',
                'test' => true,
                'paymentId' => 'rCumFBzLKj7ETrT24fKG6tRBm',
            ],
            [
                'amountSet' => [
                    'shopMoney' => [
                        'amount' => '4.05',
                        'currencyCode' => 'CAD',
                    ],
                    'presentmentMoney' => [
                        'amount' => '3.0',
                        'currencyCode' => 'USD',
                    ],
                ],
                'totalUnsettledSet' => [
                    'shopMoney' => [
                        'amount' => '0.0',
                        'currencyCode' => 'CAD',
                    ],
                ],
                'authorizationExpiresAt' => null,
                'createdAt' => '2024-02-22T16:58:09Z',
                'manuallyCapturable' => false,
                'id' => 'gid://shopify/OrderTransaction/6480340222091',
                'kind' => 'VOID',
                'status' => 'SUCCESS',
                'gateway' => 'shopify_payments',
                'test' => true,
                'paymentId' => 'p1365576679563.2',
            ],
        ];

        Http::fake([
            '*' => Http::response([
                'data' => [
                    'job' => [
                        'done' => true,
                        'id' => $expectedResult['jobId'],
                        'query' => [
                            'order' => [
                                'transactions' => $transactions,
                            ],
                        ],
                    ],
                ],
            ]),
        ]);

        $shopifyTransactionService = resolve(ShopifyTransactionService::class);
        $result = $shopifyTransactionService->getTransactionsFromJob($expectedResult['jobId'], $sourceOrderId);

        $this->assertEquals(collect($transactions)->recursive(), $result);
        Sleep::assertSleptTimes(1);
    }

    public function testItReturnsEmptyCollectionAfterPollingTransactionsJob(): void
    {
        App::context(store: StoreValue::builder()->create());
        $expectedResult = [
            'jobId' => 'test-job-id',
            'paymentReferenceId' => 'test-payment-reference-id',
        ];

        $sourceOrderId = 'test-source-order-id';

        Http::fake([
            '*' => Http::response([
                'data' => [
                    'job' => [
                        'done' => false,
                        'id' => $expectedResult['jobId'],
                        'query' => [
                        ],
                    ],
                ],
            ]),
        ]);

        $shopifyTransactionService = resolve(ShopifyTransactionService::class);
        $result = $shopifyTransactionService->getTransactionsFromJob($expectedResult['jobId'], $sourceOrderId);

        $this->assertEquals(collect([]), $result);
        Http::assertSentCount(ShopifyTransactionService::POLLING_MAX_ATTEMPTS);
        Sleep::assertSleptTimes(ShopifyTransactionService::POLLING_MAX_ATTEMPTS);
    }

    public function testItGetsSingleTransaction(): void
    {
        App::context(store: StoreValue::builder()->create());

        Http::fake([
            '*' => Http::response(
                [
                    'data' => [
                        'order' => [
                            'transactions' => [
                                [
                                    'id' => 'gid://shopify/OrderTransaction/7211086839895',
                                    'status' => 'SUCCESS',
                                    'kind' => 'SALE',
                                    'amountSet' => [
                                        'shopMoney' => [
                                            'amount' => '11.01',
                                            'currencyCode' => 'USD',
                                        ],
                                    ],
                                    'manuallyCapturable' => false,
                                    'multiCapturable' => false,
                                    'authorizationExpiresAt' => null,
                                    'totalUnsettledSet' => [
                                        'shopMoney' => [
                                            'amount' => '0.0',
                                            'currencyCode' => 'USD',
                                        ],
                                    ],
                                    'parentTransaction' => null,
                                ],
                                [
                                    'id' => 'gid://shopify/OrderTransaction/7211088674903',
                                    'status' => 'SUCCESS',
                                    'kind' => 'AUTHORIZATION',
                                    'amountSet' => [
                                        'shopMoney' => [
                                            'amount' => '110.1',
                                            'currencyCode' => 'USD',
                                        ],
                                    ],
                                    'manuallyCapturable' => false,
                                    'multiCapturable' => false,
                                    'authorizationExpiresAt' => '2024-02-21T01:08:09Z',
                                    'totalUnsettledSet' => [
                                        'shopMoney' => [
                                            'amount' => '0.0',
                                            'currencyCode' => 'USD',
                                        ],
                                    ],
                                    'parentTransaction' => null,
                                ],
                            ],
                        ],
                    ],
                    'extensions' => [
                        'cost' => [
                            'requestedQueryCost' => 79,
                            'actualQueryCost' => 20,
                            'throttleStatus' => [
                                'maximumAvailable' => 2000.0,
                                'currentlyAvailable' => 1980,
                                'restoreRate' => 100.0,
                            ],
                        ],
                    ],
                ]
            ),
        ]);

        $shopifyTransactionService = resolve(ShopifyTransactionService::class);
        $result = $shopifyTransactionService->getTransactionByIdAndOrderId('7211088674903', 'test-order-id');

        $this->assertInstanceOf(Collection::class, $result);

        $result = $result->toArray();
        $this->assertArrayHasKey('id', $result);
        $this->assertArrayHasKey('status', $result);
        $this->assertArrayHasKey('kind', $result);
        $this->assertArrayHasKey('amountSet', $result);
        $this->assertArrayHasKey('manuallyCapturable', $result);
        $this->assertArrayHasKey('multiCapturable', $result);
        $this->assertArrayHasKey('authorizationExpiresAt', $result);
        $this->assertArrayHasKey('totalUnsettledSet', $result);
        $this->assertArrayHasKey('parentTransaction', $result);

        $this->assertEquals('gid://shopify/OrderTransaction/7211088674903', $result['id']);
    }

    public function testItFailsToGetsSingleTransaction(): void
    {
        App::context(store: StoreValue::builder()->create());

        Http::fake([
            '*' => Http::response(
                [
                    'data' => [
                        'order' => [
                            'transactions' => [
                                [
                                    'id' => 'gid://shopify/OrderTransaction/7211086839895',
                                    'status' => 'SUCCESS',
                                    'kind' => 'SALE',
                                    'amountSet' => [
                                        'shopMoney' => [
                                            'amount' => '11.01',
                                            'currencyCode' => 'USD',
                                        ],
                                    ],
                                    'manuallyCapturable' => false,
                                    'multiCapturable' => false,
                                    'authorizationExpiresAt' => null,
                                    'totalUnsettledSet' => [
                                        'shopMoney' => [
                                            'amount' => '0.0',
                                            'currencyCode' => 'USD',
                                        ],
                                    ],
                                    'parentTransaction' => null,
                                ],
                                [
                                    'id' => 'gid://shopify/OrderTransaction/7211088674903',
                                    'status' => 'SUCCESS',
                                    'kind' => 'AUTHORIZATION',
                                    'amountSet' => [
                                        'shopMoney' => [
                                            'amount' => '110.1',
                                            'currencyCode' => 'USD',
                                        ],
                                    ],
                                    'manuallyCapturable' => false,
                                    'multiCapturable' => false,
                                    'authorizationExpiresAt' => '2024-02-21T01:08:09Z',
                                    'totalUnsettledSet' => [
                                        'shopMoney' => [
                                            'amount' => '0.0',
                                            'currencyCode' => 'USD',
                                        ],
                                    ],
                                    'parentTransaction' => null,
                                ],
                            ],
                        ],
                    ],
                    'extensions' => [
                        'cost' => [
                            'requestedQueryCost' => 79,
                            'actualQueryCost' => 20,
                            'throttleStatus' => [
                                'maximumAvailable' => 2000.0,
                                'currentlyAvailable' => 1980,
                                'restoreRate' => 100.0,
                            ],
                        ],
                    ],
                ]
            ),
        ]);

        $this->expectException(ItemNotFoundException::class);

        $shopifyTransactionService = resolve(ShopifyTransactionService::class);
        $shopifyTransactionService->getTransactionByIdAndOrderId('incorrect-id', 'test-order-id');
    }
}
