<?php
declare(strict_types=1);

namespace Tests\Feature\Domain\Payments\Jobs;

use App;
use App\Domain\Payments\Enums\TransactionKind;
use App\Domain\Payments\Enums\TransactionStatus;
use App\Domain\Payments\Jobs\AuthExpiryNotificationJob;
use App\Domain\Payments\Jobs\CreateInitialAuthHoldJob;
use App\Domain\Payments\Jobs\ReAuthJob;
use App\Domain\Payments\Models\Transaction;
use App\Domain\Payments\Repositories\TransactionRepository;
use App\Domain\Payments\Services\BlackcartOrderService;
use App\Domain\Payments\Services\PaymentService;
use App\Domain\Payments\Values\Order as OrderValue;
use App\Domain\Payments\Values\OrderCreatedEvent;
use App\Domain\Stores\Models\Store;
use Bus;
use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Sleep;
use Mockery\MockInterface;
use Queue;
use Tests\TestCase;

class CreateAuthHoldJobTest extends TestCase
{
    protected Store $store;

    protected function setUp(): void
    {
        parent::setUp();

        $this->store = Store::withoutEvents(function () {
            return Store::factory()->create();
        });
        App::context(store: $this->store);
    }

    public function testItCreatesInitialAuthAndSavesTransaction(): void
    {
        Bus::fake([CreateInitialAuthHoldJob::class, ReAuthJob::class]);
        Queue::fake([
            AuthExpiryNotificationJob::class,
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
            '*' => Http::sequence([
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

        $orderValue = OrderValue::builder()->create(['storeId' => $this->store->id]);

        $this->mock(BlackcartOrderService::class, function (MockInterface $mock) use ($orderValue) {
            $mock->expects('getOrderById')->with($orderValue->id)->andReturn($orderValue);
        });

        $orderCreatedEvent = new OrderCreatedEvent($orderValue);
        $listener = new CreateInitialAuthHoldJob($orderCreatedEvent->order);
        App::call([$listener, 'handle']);

        $this->assertDatabaseCount('payments_transactions', 1);
        $transaction = Transaction::first();
        $this->assertEquals($this->store->id, $transaction->store_id);
        $this->assertEquals($orderValue->id, $transaction->order_id);
        $this->assertEquals($orderValue->sourceId, $transaction->source_order_id);
        $this->assertEquals(TransactionKind::AUTHORIZATION, $transaction->kind);
        $this->assertEquals(TransactionStatus::SUCCESS, $transaction->status);
        $this->assertEquals(TransactionRepository::SOURCE_TRANSACTION_NAME_BLACKCART, $transaction->transaction_source_name);
        $this->assertEquals('gid://shopify/OrderTransaction/6480340123787', $transaction->source_id);
        $this->assertEquals('2024-02-29', $transaction->authorization_expires_at->toDateString());
        $this->assertEquals('1.35', $transaction->shop_amount->getAmount()->toFloat());
        $this->assertEquals('CAD', $transaction->shop_currency->value);
        $this->assertEquals('1.00', $transaction->customer_amount->getAmount()->toFloat());
        $this->assertEquals('USD', $transaction->customer_currency->value);

        Sleep::assertSleptTimes(1);
        Bus::assertDispatched(ReAuthJob::class);
        Bus::assertNotDispatched(CreateInitialAuthHoldJob::class);
    }

    public function testItRedispatchesOnFailure(): void
    {
        Bus::fake([ReAuthJob::class]);

        $this->mock(PaymentService::class, function (MockInterface $mock) {
            $mock->shouldReceive('createInitialAuthHold')->times(5)->andThrow(new Exception('An error occurred'));
            $mock->shouldReceive('triggerInitialAuthHoldFailure')->once();
        });

        $orderValue = OrderValue::builder()->create(['storeId' => $this->store->id]);
        $orderCreatedEvent = new OrderCreatedEvent($orderValue);
        $listener = new CreateInitialAuthHoldJob($orderCreatedEvent->order);
        App::call([$listener, 'handle']);

        Bus::assertNotDispatched(ReAuthJob::class);
    }
}
