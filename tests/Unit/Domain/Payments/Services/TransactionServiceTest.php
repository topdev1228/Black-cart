<?php
declare(strict_types=1);

namespace Tests\Unit\Domain\Payments\Services;

use App;
use App\Domain\Payments\Enums\TransactionKind;
use App\Domain\Payments\Enums\TransactionStatus;
use App\Domain\Payments\Exceptions\PaymentFailedException;
use App\Domain\Payments\Jobs\ReAuthAfterExternalCaptureTransactionJob;
use App\Domain\Payments\Models\Transaction;
use App\Domain\Payments\Repositories\TransactionRepository;
use App\Domain\Payments\Services\TransactionService;
use App\Domain\Payments\Values\Order as OrderValue;
use App\Domain\Payments\Values\Transaction as TransactionValue;
use App\Domain\Stores\Models\Store;
use App\Domain\Stores\Values\Store as StoreValue;
use Bus;
use Carbon\CarbonImmutable;
use Http;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Support\ItemNotFoundException;
use Tests\TestCase;

class TransactionServiceTest extends TestCase
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

    public function testItReturnsNullWhenJobFailsToComplete(): void
    {
        Http::fake([
            '*' => Http::response([
                'data' => [
                    'job' => [
                        'done' => false,
                        'id' => 'test-job-id',
                        'query' => [
                        ],
                    ],
                ],
            ]),
        ]);

        $this->expectException(ItemNotFoundException::class);

        $transactionService = resolve(TransactionService::class);
        $orderValue = OrderValue::builder()->create(['storeId' => (string) App::context()->store->id]);

        $transactionService->saveTransactionFromJob('test-job-id', $orderValue);

        $this->assertDatabaseCount('payments_transactions', 0);
    }

    public function testItSavesTransactionOnSuccess(): void
    {
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
        App::context(store: StoreValue::builder()->create());

        Http::fake([
            '*' => Http::sequence([
                Http::response([
                    'data' => [
                        'job' => [
                            'done' => false,
                            'id' => $expectedResult['jobId'],
                            'query' => [
                            ],
                        ],
                    ],
                ]),
                Http::response([
                    'data' => [
                        'job' => [
                            'done' => false,
                            'id' => $expectedResult['jobId'],
                        ],
                    ],
                ]),
                Http::response([
                    'data' => [
                        'job' => [
                            'done' => false,
                            'id' => $expectedResult['jobId'],
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
            ]),
        ]);

        $transactionService = resolve(TransactionService::class);
        $orderValue = OrderValue::builder()->create(['storeId' => (string) App::context()->store->id]);
        $transactionService->saveTransactionFromJob($expectedResult['jobId'], $orderValue);

        $this->assertDatabaseCount('payments_transactions', 1);
        $transaction = Transaction::first();
        $this->assertEquals((string) App::context()->store->id, $transaction->store_id);
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
    }

    public function testItSavesTransactionOnSuccessWithNullAuthorizationExpiresAt(): void
    {
        CarbonImmutable::setTestNow(CarbonImmutable::create(2024, 5, 9, 12, 1, 1));

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
                'gateway' => 'paypal',
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
                'gateway' => 'paypal',
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
                'authorizationExpiresAt' => null,
                'createdAt' => '2024-02-22T16:58:02Z',
                'manuallyCapturable' => false,
                'id' => 'gid://shopify/OrderTransaction/6480339927179',
                'kind' => 'AUTHORIZATION',
                'status' => 'SUCCESS',
                'gateway' => 'paypal',
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
                'authorizationExpiresAt' => null,
                'createdAt' => '2024-02-22T16:58:08Z',
                'manuallyCapturable' => false,
                'id' => 'gid://shopify/OrderTransaction/6480340123787',
                'kind' => 'AUTHORIZATION',
                'status' => 'SUCCESS',
                'gateway' => 'paypal',
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
                'gateway' => 'paypal',
                'test' => true,
                'paymentId' => 'p1365576679563.2',
            ],
        ];
        App::context(store: StoreValue::builder()->create());

        Http::fake([
            '*' => Http::sequence([
                Http::response([
                    'data' => [
                        'job' => [
                            'done' => false,
                            'id' => $expectedResult['jobId'],
                            'query' => [
                            ],
                        ],
                    ],
                ]),
                Http::response([
                    'data' => [
                        'job' => [
                            'done' => false,
                            'id' => $expectedResult['jobId'],
                        ],
                    ],
                ]),
                Http::response([
                    'data' => [
                        'job' => [
                            'done' => false,
                            'id' => $expectedResult['jobId'],
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
            ]),
        ]);

        $transactionService = resolve(TransactionService::class);
        $orderValue = OrderValue::builder()->create(['storeId' => (string) App::context()->store->id]);
        $transactionService->saveTransactionFromJob($expectedResult['jobId'], $orderValue);

        $this->assertDatabaseCount('payments_transactions', 1);
        $transaction = Transaction::first();
        $this->assertEquals((string) App::context()->store->id, $transaction->store_id);
        $this->assertEquals($orderValue->id, $transaction->order_id);
        $this->assertEquals($orderValue->sourceId, $transaction->source_order_id);
        $this->assertEquals(TransactionKind::AUTHORIZATION, $transaction->kind);
        $this->assertEquals(TransactionStatus::SUCCESS, $transaction->status);
        $this->assertEquals(TransactionRepository::SOURCE_TRANSACTION_NAME_BLACKCART, $transaction->transaction_source_name);
        $this->assertEquals('gid://shopify/OrderTransaction/6480340123787', $transaction->source_id);
        $this->assertEquals('2024-05-16', $transaction->authorization_expires_at->toDateString());
        $this->assertEquals('1.35', $transaction->shop_amount->getAmount()->toFloat());
        $this->assertEquals('CAD', $transaction->shop_currency->value);
        $this->assertEquals('1.00', $transaction->customer_amount->getAmount()->toFloat());
        $this->assertEquals('USD', $transaction->customer_currency->value);
    }

    public function testItReturnsNullOnFailedAuth(): void
    {
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
                'status' => 'FAILURE',
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
                        'job' => [
                            'done' => false,
                            'id' => $expectedResult['jobId'],
                            'query' => [
                            ],
                        ],
                    ],
                ]),
                Http::response([
                    'data' => [
                        'job' => [
                            'done' => false,
                            'id' => $expectedResult['jobId'],
                        ],
                    ],
                ]),
                Http::response([
                    'data' => [
                        'job' => [
                            'done' => false,
                            'id' => $expectedResult['jobId'],
                            'query' => [
                            ],
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
            ]),
        ]);

        $this->expectException(PaymentFailedException::class);

        $transactionService = resolve(TransactionService::class);
        $orderValue = OrderValue::builder()->create(['storeId' => (string) App::context()->store->id]);
        $transactionService->saveTransactionFromJob($expectedResult['jobId'], $orderValue);

        $this->assertDatabaseCount('payments_transactions', 0);
    }

    public function testItSavesExistingTransaction(): void
    {
        $transaction = Transaction::withoutEvents(function () {
            return Transaction::factory()->create([
                'store_id' => $this->store->id,
            ]);
        });
        $this->assertDatabaseCount('payments_transactions', 1);

        $val = TransactionValue::from($transaction);
        $val->status = TransactionStatus::UNKNOWN;

        $transactionService = resolve(TransactionService::class);
        $record = $transactionService->save($val);
        $this->assertDatabaseCount('payments_transactions', 1);
        $this->assertEquals($transaction->id, $record->id);

        $transaction->refresh();
        $this->assertEquals($transaction->status, $record->status);
    }

    public function testItGetsLatestTransactionByOrderIdAndKind(): void
    {
        $saleTransaction = Transaction::withoutEvents(function () {
            Transaction::factory()->count(7)->state(new Sequence(
                ['kind' => TransactionKind::AUTHORIZATION],
                ['kind' => TransactionKind::CAPTURE],
                ['kind' => TransactionKind::SUGGESTED_REFUND],
                ['kind' => TransactionKind::CHANGE],
                ['kind' => TransactionKind::EMV_AUTHORIZATION],
                ['kind' => TransactionKind::VOID],
                ['kind' => TransactionKind::SUGGESTED_REFUND],
            ))->create([
                'store_id' => $this->store->id,
                'order_id' => 'test_order_id',
            ]);

            return Transaction::factory()->create([
                'store_id' => $this->store->id,
                'order_id' => 'test_order_id',
                'kind' => TransactionKind::SALE,
            ]);
        });

        $transactionService = resolve(TransactionService::class);
        $record = $transactionService->getLatestTransaction('test_order_id', TransactionKind::SALE);
        $this->assertEquals($saleTransaction->id, $record->id);
    }

    public function testItGetsLatestTransactionByOrderId(): void
    {
        $latestTransaction = Transaction::withoutEvents(function () {
            Transaction::factory()->count(7)->state(new Sequence(
                ['kind' => TransactionKind::AUTHORIZATION],
                ['kind' => TransactionKind::CAPTURE],
                ['kind' => TransactionKind::SUGGESTED_REFUND],
                ['kind' => TransactionKind::CHANGE],
                ['kind' => TransactionKind::EMV_AUTHORIZATION],
                ['kind' => TransactionKind::VOID],
                ['kind' => TransactionKind::SUGGESTED_REFUND],
            ))->create([
                'store_id' => $this->store->id,
                'order_id' => 'test_order_id',
                'created_at' => '2024-03-17 00:00:00',
            ]);

            return Transaction::factory()->create([
                'store_id' => $this->store->id,
                'order_id' => 'test_order_id',
                'kind' => TransactionKind::SALE,
                'created_at' => '2024-03-18 00:00:00',
            ]);
        });

        $transactionService = resolve(TransactionService::class);
        $record = $transactionService->getLatestTransaction('test_order_id');
        $this->assertEquals($latestTransaction->id, $record->id);
    }

    public function testItGetsBySourceId(): void
    {
        $transaction = Transaction::withoutEvents(function () {
            return Transaction::factory()->create([
                'store_id' => $this->store->id,
            ]);
        });

        $transactionService = resolve(TransactionService::class);
        $actualTransaction = $transactionService->getBySourceId($transaction->source_id);

        $this->assertEquals($transaction->id, $actualTransaction->id);
    }

    public function testItDoesNotCreateTransactionOnNonCaptureTransaction(): void
    {
        Bus::fake([
            ReAuthAfterExternalCaptureTransactionJob::class,
        ]);

        $transactionValue = TransactionValue::builder()->create([
            // Authorization transaction by default
            'store_id' => $this->store->id,
        ]);

        $transactionService = resolve(TransactionService::class);
        $newTransaction = $transactionService->createCaptureTransaction($transactionValue);
        $this->assertNull($newTransaction);

        Bus::assertNotDispatched(ReAuthAfterExternalCaptureTransactionJob::class);
    }

    public function testItCreatesCaptureTransactionWithoutParentTransaction(): void
    {
        Bus::fake([
            ReAuthAfterExternalCaptureTransactionJob::class,
        ]);

        $transactionValue = TransactionValue::builder()->capture()->create([
            'store_id' => $this->store->id,
        ]);

        $this->assertDatabaseCount('payments_transactions', 0);

        $transactionService = resolve(TransactionService::class);
        $newTransaction = $transactionService->createCaptureTransaction($transactionValue);

        $this->assertNotEmpty($newTransaction->id);
        $this->assertDatabaseCount('payments_transactions', 1);
        $this->assertDatabaseHas('payments_transactions', [
            'source_id' => $transactionValue->sourceId,
            'parent_transaction_id' => null,
            'parent_transaction_source_id' => $transactionValue->parentTransactionSourceId,
        ]);

        Bus::assertNotDispatched(ReAuthAfterExternalCaptureTransactionJob::class);
    }

    public function testItSavesExistingCaptureTransaction(): void
    {
        Bus::fake([
            ReAuthAfterExternalCaptureTransactionJob::class,
        ]);

        $parentTransaction = Transaction::withoutEvents(function () {
            return Transaction::factory()->create([
                'store_id' => $this->store->id,
                'transaction_source_name' => TransactionRepository::SOURCE_TRANSACTION_NAME_BLACKCART,
            ]);
        });

        $existingTransaction = Transaction::withoutEvents(function () use ($parentTransaction) {
            return Transaction::factory()->capture()->create([
                'store_id' => $this->store->id,
                'parent_transaction_source_id' => $parentTransaction->source_id,
                'transaction_source_name' => 'special source name',
            ]);
        });

        $transactionValue = TransactionValue::from($existingTransaction);
        $transactionValue->id = null; // unset the internal Blackcart ID as Shopify's value won't have it

        $this->assertDatabaseCount('payments_transactions', 2);

        $transactionService = resolve(TransactionService::class);
        $newTransaction = $transactionService->createCaptureTransaction($transactionValue);

        $this->assertNotEmpty($newTransaction->id);
        $this->assertDatabaseCount('payments_transactions', 2);
        $this->assertDatabaseHas('payments_transactions', [
            'source_id' => $transactionValue->sourceId,
            'parent_transaction_id' => $parentTransaction->id,
            'parent_transaction_source_id' => $parentTransaction->source_id,
            'transaction_source_name' => 'special source name',
        ]);
        $this->assertDatabaseHas('payments_transactions', [
            'id' => $parentTransaction->id,
            'captured_transaction_id' => $newTransaction->id,
            'captured_transaction_source_id' => $newTransaction->sourceId,
        ]);

        Bus::assertDispatched(ReAuthAfterExternalCaptureTransactionJob::class, function ($job) use ($newTransaction) {
            $this->assertEquals($job->orderId, $newTransaction->orderId);

            return true;
        });
    }

    public function testItCreatesBlackcartCaptureTransaction(): void
    {
        Bus::fake([
            ReAuthAfterExternalCaptureTransactionJob::class,
        ]);

        $parentTransaction = Transaction::withoutEvents(function () {
            return Transaction::factory()->create([
                'store_id' => $this->store->id,
                'transaction_source_name' => TransactionRepository::SOURCE_TRANSACTION_NAME_BLACKCART,
            ]);
        });

        $transactionValue = TransactionValue::builder()->capture()->create([
            'store_id' => $this->store->id,
            'parent_transaction_source_id' => $parentTransaction->source_id,
            'transaction_source_name' => TransactionRepository::SOURCE_TRANSACTION_NAME_BLACKCART,
        ]);

        $this->assertDatabaseCount('payments_transactions', 1);

        $transactionService = resolve(TransactionService::class);
        $newTransaction = $transactionService->createCaptureTransaction($transactionValue);

        $this->assertNotEmpty($newTransaction->id);
        $this->assertDatabaseCount('payments_transactions', 2);
        $this->assertDatabaseHas('payments_transactions', [
            'source_id' => $transactionValue->sourceId,
            'parent_transaction_id' => $parentTransaction->id,
            'parent_transaction_source_id' => $parentTransaction->source_id,
        ]);
        $this->assertDatabaseHas('payments_transactions', [
            'id' => $parentTransaction->id,
            'captured_transaction_id' => $newTransaction->id,
            'captured_transaction_source_id' => $newTransaction->sourceId,
        ]);

        Bus::assertNotDispatched(ReAuthAfterExternalCaptureTransactionJob::class);
    }

    public function testItCreatesExternalCaptureTransaction(): void
    {
        Bus::fake([
            ReAuthAfterExternalCaptureTransactionJob::class,
        ]);

        $parentTransaction = Transaction::withoutEvents(function () {
            return Transaction::factory()->create([
                'store_id' => $this->store->id,
                'transaction_source_name' => TransactionRepository::SOURCE_TRANSACTION_NAME_BLACKCART,
            ]);
        });

        $transactionValue = TransactionValue::builder()->capture()->create([
            'store_id' => $this->store->id,
            'parent_transaction_source_id' => $parentTransaction->source_id,
            'transaction_source_name' => 'web',
        ]);

        $this->assertDatabaseCount('payments_transactions', 1);

        $transactionService = resolve(TransactionService::class);
        $newTransaction = $transactionService->createCaptureTransaction($transactionValue);

        $this->assertNotEmpty($newTransaction->id);
        $this->assertDatabaseCount('payments_transactions', 2);
        $this->assertDatabaseHas('payments_transactions', [
            'source_id' => $transactionValue->sourceId,
            'parent_transaction_id' => $parentTransaction->id,
            'parent_transaction_source_id' => $parentTransaction->source_id,
        ]);
        $this->assertDatabaseHas('payments_transactions', [
            'id' => $parentTransaction->id,
            'captured_transaction_id' => $newTransaction->id,
            'captured_transaction_source_id' => $newTransaction->sourceId,
        ]);

        Bus::assertDispatched(ReAuthAfterExternalCaptureTransactionJob::class, function ($job) use ($newTransaction) {
            $this->assertEquals($job->orderId, $newTransaction->orderId);

            return true;
        });
    }
}
