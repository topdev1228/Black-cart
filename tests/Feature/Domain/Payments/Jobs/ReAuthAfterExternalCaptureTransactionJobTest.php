<?php
declare(strict_types=1);

namespace Feature\Domain\Payments\Jobs;

use App;
use App\Domain\Payments\Events\ReAuthSuccessEvent;
use App\Domain\Payments\Jobs\ReAuthAfterExternalCaptureTransactionJob;
use App\Domain\Payments\Jobs\ReAuthJob;
use App\Domain\Payments\Values\Order as OrderValue;
use App\Domain\Stores\Models\Store;
use Brick\Money\Money;
use Bus;
use Event;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Sleep;
use PHPUnit\Framework\Attributes\DataProvider;
use PrinsFrank\Standards\Currency\CurrencyAlpha3;
use Tests\TestCase;

class ReAuthAfterExternalCaptureTransactionJobTest extends TestCase
{
    protected Store $store;

    protected function setUp(): void
    {
        parent::setUp();

        $this->store = Store::withoutEvents(function () {
            return Store::factory()->create();
        });
        App::context(store: $this->store);

        Event::fake([ReAuthSuccessEvent::class]);
        Bus::fake([ReAuthAfterExternalCaptureTransactionJob::class, ReAuthJob::class]);
    }

    public function testItDoesNotReAuthOnGetOrderApiCallErrorAndRedispatches(): void
    {
        $orderValue = OrderValue::builder()->create([
            'storeId' => $this->store->id,
            'outstandingCustomerAmount' => Money::ofMinor(135, CurrencyAlpha3::US_Dollar->value),
            'outstandingShopAmount' => Money::ofMinor(100, CurrencyAlpha3::Canadian_Dollar->value),
        ]);

        Http::fake([
            'http://localhost:8080/api/stores/orders/' . $orderValue->id => Http::sequence()
                ->push([], 500),
        ]);

        $this->expectExceptionCode(500);
        $this->expectException(RequestException::class);

        $listener = new ReAuthAfterExternalCaptureTransactionJob($orderValue->id);
        $result = App::call([$listener, 'handle']);

        $this->assertNull($result);
        $this->assertDatabaseCount('orders_transactions', 0);

        Event::assertNotDispatched(ReAuthSuccessEvent::class);
        Bus::assertNotDispatched(ReAuthJob::class);
        Bus::assertDispatched(ReAuthAfterExternalCaptureTransactionJob::class);
    }

    #[DataProvider('orderOutstandingAmountProvider')]
    public function testItDoesNotReAuthOnOrderOutstandingAmountZero(int $amount): void
    {
        $orderValue = OrderValue::builder()->create([
            'storeId' => $this->store->id,
            'outstandingCustomerAmount' => Money::ofMinor($amount, CurrencyAlpha3::US_Dollar->value),
            'outstandingShopAmount' => Money::ofMinor($amount, CurrencyAlpha3::Canadian_Dollar->value),
        ]);

        Http::fake([
            'http://localhost:8080/api/stores/orders/' . $orderValue->id => Http::sequence()
                ->push(['order' => $orderValue->toArray()]),
        ]);

        $this->assertDatabaseCount('orders_transactions', 0);

        $listener = new ReAuthAfterExternalCaptureTransactionJob($orderValue->id);
        $transactionValue = App::call([$listener, 'handle']);

        $this->assertNull($transactionValue);
        $this->assertDatabaseCount('orders_transactions', 0);

        Event::assertNotDispatched(ReAuthSuccessEvent::class);
        Bus::assertNotDispatched(ReAuthJob::class);
    }

    public static function orderOutstandingAmountProvider(): array
    {
        return [
            'zero outstanding balance' => [0],
            'negative outstandinb balance' => [-100],
        ];
    }

    public function testItReAuths(): void
    {
        $orderValue = OrderValue::builder()->create([
            'storeId' => $this->store->id,
            'outstandingCustomerAmount' => Money::ofMinor(135, CurrencyAlpha3::US_Dollar->value),
            'outstandingShopAmount' => Money::ofMinor(100, CurrencyAlpha3::Canadian_Dollar->value),
        ]);

        $expectedResult = [
            'jobId' => 'test-job-id',
            'paymentReferenceId' => 'test-payment-reference-id',
        ];

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
            'http://localhost:8080/api/stores/orders/' . $orderValue->id => Http::sequence([
                Http::response([
                    'order' => $orderValue->toArray(),
                ]),
            ]),
            App::context()->store->domain . '/admin/api/*/graphql.json' => Http::sequence([
                Http::response([
                    'data' => [
                        'order' => [
                            'paymentCollectionDetails' => [
                                'vaultedPaymentMethods' => [
                                    [
                                        'id' => 'gid://shopify/PaymentMandate/19de18d646e13295b20c74c7d289a3bf',
                                    ],
                                ],
                            ],
                            'paymentTerms' => [
                                'id' => 'gid://shopify/PaymentTerms/8625782923',
                                'paymentSchedules' => [
                                    'nodes' => [
                                        [
                                            'id' => 'gid://shopify/PaymentSchedule/9960161419',
                                            'completedAt' => null,
                                            'presentmentMoney' => [
                                                'amount' => '631.97',
                                                'currencyCode' => 'USD',
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ]),
                Http::response([
                    'data' => [
                        'order' => [
                            'closed' => false,
                        ],
                    ],
                ]),
                Http::response([
                    'data' => [
                        'orderCreateMandatePayment' => [
                            'job' => ['id' => $expectedResult['jobId']],
                            'paymentReferenceId' => $expectedResult['paymentReferenceId'],
                        ],
                    ],
                ]),
                Http::response([
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
                Http::response([
                    'data' => [
                        'order' => [
                            'closed' => false,
                        ],
                    ],
                ]),
            ]),
        ]);

        $this->assertDatabaseCount('orders_transactions', 0);

        $listener = new ReAuthAfterExternalCaptureTransactionJob($orderValue->id);
        App::call([$listener, 'handle']);

        $this->assertDatabaseCount('payments_transactions', 1);
        Sleep::assertSleptTimes(1);
        Event::assertDispatched(ReAuthSuccessEvent::class);
        Bus::assertDispatched(ReAuthJob::class, function (ReAuthJob $job) use ($orderValue) {
            $this->assertEquals($orderValue->id, $job->order->id);

            return true;
        });
        Bus::assertNotDispatched(ReAuthAfterExternalCaptureTransactionJob::class);

        $this->markTestIncomplete('Verify the transaction in the DB');
    }
}
