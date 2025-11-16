<?php
declare(strict_types=1);

namespace Tests\Unit\Domain\Payments\Services;

use App;
use App\Domain\Payments\Enums\OrderStatus;
use App\Domain\Payments\Enums\PaymentStatus;
use App\Domain\Payments\Enums\TransactionKind;
use App\Domain\Payments\Enums\TransactionStatus;
use App\Domain\Payments\Events\CheckoutAuthorizationSuccessEvent;
use App\Domain\Payments\Events\InitialAuthFailedEvent;
use App\Domain\Payments\Events\PaymentCompleteEvent;
use App\Domain\Payments\Events\ReAuthFailedEvent;
use App\Domain\Payments\Events\ReAuthSuccessEvent;
use App\Domain\Payments\Exceptions\PaymentFailedException;
use App\Domain\Payments\Exceptions\PaymentMandateNotFoundException;
use App\Domain\Payments\Exceptions\ShopifyMandatePaymentOutstandingAmountZeroException;
use App\Domain\Payments\Exceptions\ShopifyMandatePaymentRetryFailureLimitReachedException;
use App\Domain\Payments\Exceptions\ShopifyTransactionNotFoundException;
use App\Domain\Payments\Jobs\AuthExpiryNotificationJob;
use App\Domain\Payments\Jobs\ReAuthJob;
use App\Domain\Payments\Mail\ReAuthNotice;
use App\Domain\Payments\Repositories\TransactionRepository;
use App\Domain\Payments\Services\BlackcartOrderService;
use App\Domain\Payments\Services\PaymentService;
use App\Domain\Payments\Services\ShopifyOrderService;
use App\Domain\Payments\Services\ShopifyPaymentService;
use App\Domain\Payments\Services\TransactionService;
use App\Domain\Payments\Values\Order as OrderValue;
use App\Domain\Payments\Values\Transaction as TransactionValue;
use App\Domain\Stores\Models\Store;
use App\Domain\Stores\Values\Store as StoreValue;
use Brick\Money\Money;
use Bus;
use Event;
use Feature;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\ItemNotFoundException;
use Mockery\MockInterface;
use PHPUnit\Framework\Attributes\DataProvider;
use PrinsFrank\Standards\Currency\CurrencyAlpha3;
use Queue;
use Tests\TestCase;

class PaymentServiceTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Http::fake([
            'http://localhost:8080/api/stores/settings' => Http::response([
                'settings' => [
                    'customerSupportEmail' => [
                        'value' => 'customersupport@merchant.com',
                    ],
                ],
            ], 200),
        ]);
        App::context(store: Store::withoutEvents(function () {
            return Store::factory()->create();
        }));
    }

    public function testItDoesNotCaptureOrCreatePaymentOnZeroOutstandingBalance(): void
    {
        Event::fake([PaymentCompleteEvent::class]);

        $orderId = $sourceOrderId = 'test-order-id';

        $paymentService = resolve(PaymentService::class);
        $result = $paymentService->captureOrCreatePayment($orderId, $sourceOrderId, Money::zero('USD'));

        $this->assertNull($result);
        Event::assertNotDispatched(PaymentCompleteEvent::class);
    }

    public function testItCreatesPayment(): void
    {
        Event::fake([PaymentCompleteEvent::class]);

        $orderId = $sourceOrderId = 'test-order-id';

        $this->mock(ShopifyPaymentService::class, function (MockInterface $mock) use ($sourceOrderId) {
            $mock->shouldReceive('getOrderPaymentDetails')->once()->with($sourceOrderId)->andReturn(collect([
                'data' => ['order' => ['paymentCollectionDetails' => ['vaultedPaymentMethods' => [['id' => 'test-payment-mandate-id']]]]],
            ])->recursive());

            $mock->shouldReceive('createMandatePayment')->once()->withArgs(function (string $actualSourceOrderId, string $actualPaymentMandateId, bool $actualAutoCapture, Money $actualAmount) use ($sourceOrderId) {
                $this->assertEquals($sourceOrderId, $actualSourceOrderId);
                $this->assertEquals('test-payment-mandate-id', $actualPaymentMandateId);
                $this->assertTrue($actualAutoCapture);
                $this->assertTrue($actualAmount->isEqualTo(Money::of(100, 'USD')));

                return true;
            })->andReturn(collect([
                'jobId' => 'test-job-id',
                'paymentReferenceId' => 'test-payment-reference-id',
            ]));

            $mock->shouldReceive('capturePayment')->never();
        });

        $this->partialMock(TransactionService::class, function (MockInterface $mock) {
            $mock->shouldReceive('getLatestTransaction')->once()->andThrow(ModelNotFoundException::class);
            $mock->shouldReceive('getTransactionsFromSourceJob')->once()->andReturn(collect([
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
                        'presentmentMoney' => [
                            'amount' => '0.0',
                            'currencyCode' => 'USD',
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
                        'presentmentMoney' => [
                            'amount' => '0.0',
                            'currencyCode' => 'USD',
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
                        'presentmentMoney' => [
                            'amount' => '0.0',
                            'currencyCode' => 'USD',
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
                        'presentmentMoney' => [
                            'amount' => '1.0',
                            'currencyCode' => 'USD',
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
                        'presentmentMoney' => [
                            'amount' => '0.0',
                            'currencyCode' => 'USD',
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
            ])->recursive());

            $mock->shouldReceive('save')->once()->andReturnArg(0);
            $mock->shouldReceive('getTransactionBySourceIdAndSourceOrderId')->never();
        });

        $paymentService = resolve(PaymentService::class);
        $result = $paymentService->captureOrCreatePayment($orderId, $sourceOrderId, Money::of(100, 'USD'));

        $this->assertInstanceOf(TransactionValue::class, $result);
        Event::assertDispatched(PaymentCompleteEvent::class, function (PaymentCompleteEvent $event) use ($sourceOrderId) {
            $this->assertEquals($sourceOrderId, $event->sourceOrderId);
            $this->assertFalse($event->outstandingAmountZeroAlready);

            return true;
        });
    }

    public function testItDoesNotCreatePaymentOnOutstandingAmountAlreadyZeroError(): void
    {
        Event::fake([PaymentCompleteEvent::class]);

        $orderId = $sourceOrderId = 'test-order-id';

        $this->mock(ShopifyPaymentService::class, function (MockInterface $mock) use ($sourceOrderId) {
            $mock->shouldReceive('getOrderPaymentDetails')->once()->with($sourceOrderId)->andReturn(collect([
                'data' => ['order' => ['paymentCollectionDetails' => ['vaultedPaymentMethods' => [['id' => 'test-payment-mandate-id']]]]],
            ])->recursive());

            $mock->shouldReceive('createMandatePayment')->once()->withArgs(function (string $actualSourceOrderId, string $actualPaymentMandateId, bool $actualAutoCapture, Money $actualAmount) use ($sourceOrderId) {
                $this->assertEquals($sourceOrderId, $actualSourceOrderId);
                $this->assertEquals('test-payment-mandate-id', $actualPaymentMandateId);
                $this->assertTrue($actualAutoCapture);
                $this->assertTrue($actualAmount->isEqualTo(Money::of(100, 'USD')));

                return true;
            })->andThrows(new ShopifyMandatePaymentOutstandingAmountZeroException());

            $mock->shouldReceive('capturePayment')->never();
        });

        $this->partialMock(TransactionService::class, function (MockInterface $mock) {
            $mock->shouldReceive('getLatestTransaction')->once()->andThrow(ModelNotFoundException::class);
            $mock->shouldReceive('getTransactionBySourceIdAndSourceOrderId')->never();
            $mock->shouldReceive('getTransactionsFromSourceJob')->never();
            $mock->shouldReceive('save')->never();
        });

        $paymentService = resolve(PaymentService::class);
        $result = $paymentService->captureOrCreatePayment($orderId, $sourceOrderId, Money::of(100, 'USD'));
        $this->assertNull($result);

        Event::assertDispatched(PaymentCompleteEvent::class, function (PaymentCompleteEvent $event) use ($sourceOrderId) {
            $this->assertEquals($sourceOrderId, $event->sourceOrderId);
            $this->assertTrue($event->outstandingAmountZeroAlready);

            return true;
        });
    }

    public function testItDoesNotCreatePaymentOnRetryFailureLimitReachedError(): void
    {
        Event::fake([PaymentCompleteEvent::class]);

        $orderId = $sourceOrderId = 'test-order-id';

        $this->mock(ShopifyPaymentService::class, function (MockInterface $mock) use ($sourceOrderId) {
            $mock->shouldReceive('getOrderPaymentDetails')->once()->with($sourceOrderId)->andReturn(collect([
                'data' => ['order' => ['paymentCollectionDetails' => ['vaultedPaymentMethods' => [['id' => 'test-payment-mandate-id']]]]],
            ])->recursive());

            $mock->shouldReceive('createMandatePayment')->once()->withArgs(function (string $actualSourceOrderId, string $actualPaymentMandateId, bool $actualAutoCapture, Money $actualAmount) use ($sourceOrderId) {
                $this->assertEquals($sourceOrderId, $actualSourceOrderId);
                $this->assertEquals('test-payment-mandate-id', $actualPaymentMandateId);
                $this->assertTrue($actualAutoCapture);
                $this->assertTrue($actualAmount->isEqualTo(Money::of(100, 'USD')));

                return true;
            })->andThrows(new ShopifyMandatePaymentRetryFailureLimitReachedException());

            $mock->shouldReceive('capturePayment')->never();
        });

        $this->partialMock(TransactionService::class, function (MockInterface $mock) {
            $mock->shouldReceive('getLatestTransaction')->once()->andThrow(ModelNotFoundException::class);
            $mock->shouldReceive('getTransactionBySourceIdAndSourceOrderId')->never();
            $mock->shouldReceive('getTransactionsFromSourceJob')->never();
            $mock->shouldReceive('save')->never();
        });

        $paymentService = resolve(PaymentService::class);
        $result = $paymentService->captureOrCreatePayment($orderId, $sourceOrderId, Money::of(100, 'USD'));
        $this->assertNull($result);

        Event::assertDispatched(PaymentCompleteEvent::class, function (PaymentCompleteEvent $event) use ($sourceOrderId) {
            $this->assertEquals($sourceOrderId, $event->sourceOrderId);
            $this->assertTrue($event->outstandingAmountZeroAlready);

            return true;
        });
    }

    public function testItFailsToCreatePaymentOnError(): void
    {
        $orderId = $sourceOrderId = 'test-order-id';

        $this->mock(ShopifyPaymentService::class, function (MockInterface $mock) use ($sourceOrderId) {
            $mock->shouldReceive('getOrderPaymentDetails')->once()->with($sourceOrderId)->andReturn(collect([
                'data' => ['order' => ['paymentCollectionDetails' => ['vaultedPaymentMethods' => [['id' => 'test-payment-mandate-id']]]]],
            ])->recursive());

            $mock->shouldReceive('createMandatePayment')->once()->withArgs(function (string $actualSourceOrderId, string $actualPaymentMandateId, bool $actualAutoCapture, Money $actualAmount) use ($sourceOrderId) {
                $this->assertEquals($sourceOrderId, $actualSourceOrderId);
                $this->assertEquals('test-payment-mandate-id', $actualPaymentMandateId);
                $this->assertTrue($actualAutoCapture);
                $this->assertTrue($actualAmount->isEqualTo(Money::of(100, 'USD')));

                return true;
            })->andReturn(collect([
                'jobId' => 'test-job-id',
                'paymentReferenceId' => 'test-payment-reference-id',
            ]));
            $mock->shouldReceive('capturePayment')->never();
        });

        $this->partialMock(TransactionService::class, function (MockInterface $mock) {
            $mock->shouldReceive('getLatestTransaction')->once()->andThrow(ModelNotFoundException::class);

            $mock->shouldReceive('getTransactionsFromSourceJob')->once()->andReturn(collect([
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
                        'presentmentMoney' => [
                            'amount' => '0.0',
                            'currencyCode' => 'USD',
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
                        'presentmentMoney' => [
                            'amount' => '0.0',
                            'currencyCode' => 'USD',
                        ],
                    ],
                    'authorizationExpiresAt' => null,
                    'createdAt' => '2024-01-26T18:48:59Z',
                    'manuallyCapturable' => false,
                    'id' => 'gid://shopify/OrderTransaction/6429977477259',
                    'kind' => 'SALE',
                    'status' => TransactionStatus::ERROR->name,
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
                        'presentmentMoney' => [
                            'amount' => '1.0',
                            'currencyCode' => 'USD',
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
                        'presentmentMoney' => [
                            'amount' => '0.0',
                            'currencyCode' => 'USD',
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
            ])->recursive());

            $mock->shouldReceive('save')->never();
            $mock->shouldReceive('getTransactionBySourceIdAndSourceOrderId')->never();
        });

        $this->expectException(PaymentFailedException::class);

        $paymentService = resolve(PaymentService::class);
        $paymentService->captureOrCreatePayment($orderId, $sourceOrderId, Money::of(100, 'USD'));
    }

    public function testItCreatesPaymentWithAlreadyCapturedAuth(): void
    {
        $orderId = $sourceOrderId = 'test-order-id';

        $this->mock(ShopifyPaymentService::class, function (MockInterface $mock) use ($sourceOrderId) {
            $mock->shouldReceive('getOrderPaymentDetails')->once()->with($sourceOrderId)->andReturn(collect([
                'data' => ['order' => ['paymentCollectionDetails' => ['vaultedPaymentMethods' => [['id' => 'test-payment-mandate-id']]]]],
            ])->recursive());

            $mock->shouldReceive('createMandatePayment')->once()->withArgs(function (string $actualSourceOrderId, string $actualPaymentMandateId, bool $actualAutoCapture, Money $actualAmount) use ($sourceOrderId) {
                $this->assertEquals($sourceOrderId, $actualSourceOrderId);
                $this->assertEquals('test-payment-mandate-id', $actualPaymentMandateId);
                $this->assertTrue($actualAutoCapture);
                $this->assertTrue($actualAmount->isEqualTo(Money::of(100, 'USD')));

                return true;
            })->andReturn(collect([
                'jobId' => 'test-job-id',
                'paymentReferenceId' => 'test-payment-reference-id',
            ]));

            $mock->shouldReceive('capturePayment')->never();
        });

        $this->partialMock(TransactionService::class, function (MockInterface $mock) use ($orderId) {
            $mock->shouldReceive('getLatestTransaction')->once()->andReturn(TransactionValue::builder()->create([
                'source_id' => 'test-auth-transaction-id',
                'order_id' => $orderId,
                'store_id' => App::context()->store->id,
                'captured_transaction_id' => 'not-null',
            ]));
            $mock->shouldReceive('getTransactionsFromSourceJob')->once()->andReturn(collect([
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
                        'presentmentMoney' => [
                            'amount' => '0.0',
                            'currencyCode' => 'USD',
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
                        'presentmentMoney' => [
                            'amount' => '0.0',
                            'currencyCode' => 'USD',
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
                        'presentmentMoney' => [
                            'amount' => '0.0',
                            'currencyCode' => 'USD',
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
                            'amount' => '0.0',
                            'currencyCode' => 'CAD',
                        ],
                        'presentmentMoney' => [
                            'amount' => '0.0',
                            'currencyCode' => 'USD',
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
                        'presentmentMoney' => [
                            'amount' => '0.0',
                            'currencyCode' => 'USD',
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
            ])->recursive());

            $mock->shouldReceive('save')->once()->andReturnArg(0);
            $mock->shouldReceive('getTransactionBySourceIdAndSourceOrderId')->never();
        });

        $paymentService = resolve(PaymentService::class);
        $result = $paymentService->captureOrCreatePayment($orderId, $sourceOrderId, Money::of(100, 'USD'));

        $this->assertInstanceOf(TransactionValue::class, $result);
    }

    public function testItFailsToCreatePaymentWithAlreadyCapturedAuthOnError(): void
    {
        $orderId = $sourceOrderId = 'test-order-id';

        $this->mock(ShopifyPaymentService::class, function (MockInterface $mock) use ($sourceOrderId) {
            $mock->shouldReceive('getOrderPaymentDetails')->once()->with($sourceOrderId)->andReturn(collect([
                'data' => ['order' => ['paymentCollectionDetails' => ['vaultedPaymentMethods' => [['id' => 'test-payment-mandate-id']]]]],
            ])->recursive());

            $mock->shouldReceive('createMandatePayment')->once()->withArgs(function (string $actualSourceOrderId, string $actualPaymentMandateId, bool $actualAutoCapture, Money $actualAmount) use ($sourceOrderId) {
                $this->assertEquals($sourceOrderId, $actualSourceOrderId);
                $this->assertEquals('test-payment-mandate-id', $actualPaymentMandateId);
                $this->assertTrue($actualAutoCapture);
                $this->assertTrue($actualAmount->isEqualTo(Money::of(100, 'USD')));

                return true;
            })->andReturn(collect([
                'jobId' => 'test-job-id',
                'paymentReferenceId' => 'test-payment-reference-id',
            ]));

            $mock->shouldReceive('capturePayment')->never();
        });

        $this->partialMock(TransactionService::class, function (MockInterface $mock) use ($orderId) {
            $mock->shouldReceive('getLatestTransaction')->once()->andReturn(TransactionValue::builder(['source_id' => 'test-auth-transaction-id', 'order_id' => $orderId, 'store_id' => App::context()->store->id, 'captured_transaction_id' => 'not-null'])->create());

            $mock->shouldReceive('getTransactionsFromSourceJob')->once()->andReturn(collect([
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
                        'presentmentMoney' => [
                            'amount' => '0.0',
                            'currencyCode' => 'USD',
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
                        'presentmentMoney' => [
                            'amount' => '0.0',
                            'currencyCode' => 'USD',
                        ],
                    ],
                    'authorizationExpiresAt' => null,
                    'createdAt' => '2024-01-26T18:48:59Z',
                    'manuallyCapturable' => false,
                    'id' => 'gid://shopify/OrderTransaction/6429977477259',
                    'kind' => 'SALE',
                    'status' => TransactionStatus::ERROR->name,
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
                        'presentmentMoney' => [
                            'amount' => '0.0',
                            'currencyCode' => 'USD',
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
                            'amount' => '0.0',
                            'currencyCode' => 'CAD',
                        ],
                        'presentmentMoney' => [
                            'amount' => '0.0',
                            'currencyCode' => 'USD',
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
            ])->recursive());

            $mock->shouldReceive('save')->never();
            $mock->shouldReceive('getTransactionBySourceIdAndSourceOrderId')->never();
        });

        $this->expectException(PaymentFailedException::class);

        $paymentService = resolve(PaymentService::class);
        $paymentService->captureOrCreatePayment($orderId, $sourceOrderId, Money::of(100, 'USD'));
    }

    public function testItCreatesPaymentWithInvalidAuth(): void
    {
        $orderId = $sourceOrderId = 'test-order-id';

        $this->mock(ShopifyPaymentService::class, function (MockInterface $mock) use ($sourceOrderId) {
            $mock->shouldReceive('getOrderPaymentDetails')->once()->with($sourceOrderId)->andReturn(collect([
                'data' => ['order' => ['paymentCollectionDetails' => ['vaultedPaymentMethods' => [['id' => 'test-payment-mandate-id']]]]],
            ])->recursive());

            $mock->shouldReceive('createMandatePayment')->once()->withArgs(function (string $actualSourceOrderId, string $actualPaymentMandateId, bool $actualAutoCapture, Money $actualAmount) use ($sourceOrderId) {
                $this->assertEquals($sourceOrderId, $actualSourceOrderId);
                $this->assertEquals('test-payment-mandate-id', $actualPaymentMandateId);
                $this->assertTrue($actualAutoCapture);
                $this->assertTrue($actualAmount->isEqualTo(Money::of(100, 'USD')));

                return true;
            })->andReturn(collect([
                'jobId' => 'test-job-id',
                'paymentReferenceId' => 'test-payment-reference-id',
            ]));

            $mock->shouldReceive('capturePayment')->never();
        });

        $this->mock(TransactionService::class, function (MockInterface $mock) use ($orderId, $sourceOrderId) {
            $mock->shouldReceive('getLatestTransaction')->once()->andReturn(TransactionValue::builder()->create([
                'source_id' => 'test-auth-transaction-id',
                'order_id' => $orderId,
                'store_id' => App::context()->store->id,
            ]));
            $mock->shouldReceive('getTransactionsFromSourceJob')->once()->andReturn(collect([
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
                        'presentmentMoney' => [
                            'amount' => '0.0',
                            'currencyCode' => 'USD',
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
                        'presentmentMoney' => [
                            'amount' => '0.0',
                            'currencyCode' => 'USD',
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
                            'amount' => '0.0',
                            'currencyCode' => 'CAD',
                        ],
                        'presentmentMoney' => [
                            'amount' => '0.0',
                            'currencyCode' => 'USD',
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
                        'presentmentMoney' => [
                            'amount' => '0.0',
                            'currencyCode' => 'USD',
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
            ])->recursive());

            $mock->shouldReceive('save')->once()->andReturnArg(0);

            $mock->shouldReceive('getTransactionBySourceIdAndSourceOrderId')->once()->with('test-auth-transaction-id', $sourceOrderId)->andReturn(collect([
                'capturedTransactionId' => null,
                'manuallyCapturable' => false,
                'totalUnsettledSet' => [
                    'shopMoney' => [
                        'amount' => '0.0',
                        'currencyCode' => 'CAD',
                    ],
                    'presentmentMoney' => [
                        'amount' => '0.0',
                        'currencyCode' => 'USD',
                    ],
                ],
            ]));
        });

        $paymentService = resolve(PaymentService::class);
        $result = $paymentService->captureOrCreatePayment($orderId, $sourceOrderId, Money::of(100, 'USD'));

        $this->assertInstanceOf(TransactionValue::class, $result);
    }

    public function testItFailsToCreatePaymentWithInvalidAuthOnError(): void
    {
        $orderId = $sourceOrderId = 'test-order-id';

        $this->mock(ShopifyPaymentService::class, function (MockInterface $mock) use ($sourceOrderId) {
            $mock->shouldReceive('getOrderPaymentDetails')->once()->with($sourceOrderId)->andReturn(collect([
                'data' => ['order' => ['paymentCollectionDetails' => ['vaultedPaymentMethods' => [['id' => 'test-payment-mandate-id']]]]],
            ])->recursive());

            $mock->shouldReceive('createMandatePayment')->once()->withArgs(function (string $actualSourceOrderId, string $actualPaymentMandateId, bool $actualAutoCapture, Money $actualAmount) use ($sourceOrderId) {
                $this->assertEquals($sourceOrderId, $actualSourceOrderId);
                $this->assertEquals('test-payment-mandate-id', $actualPaymentMandateId);
                $this->assertTrue($actualAutoCapture);
                $this->assertTrue($actualAmount->isEqualTo(Money::of(100, 'USD')));

                return true;
            })->andReturn(collect([
                'jobId' => 'test-job-id',
                'paymentReferenceId' => 'test-payment-reference-id',
            ]));

            $mock->shouldReceive('capturePayment')->never();
        });

        $this->partialMock(TransactionService::class, function (MockInterface $mock) use ($orderId, $sourceOrderId) {
            $mock->shouldReceive('getLatestTransaction')->once()->andReturn(TransactionValue::builder(['source_id' => 'test-auth-transaction-id', 'order_id' => $orderId, 'store_id' => App::context()->store->id])->create());

            $mock->shouldReceive('getTransactionsFromSourceJob')->once()->andReturn(collect([
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
                        'presentmentMoney' => [
                            'amount' => '0.0',
                            'currencyCode' => 'USD',
                        ],
                    ],
                    'authorizationExpiresAt' => null,
                    'createdAt' => '2024-01-26T18:48:59Z',
                    'manuallyCapturable' => false,
                    'id' => 'gid://shopify/OrderTransaction/6429977477259',
                    'kind' => 'SALE',
                    'status' => TransactionStatus::ERROR->name,
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
                        'presentmentMoney' => [
                            'amount' => '0.0',
                            'currencyCode' => 'USD',
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
                        'presentmentMoney' => [
                            'amount' => '1.0',
                            'currencyCode' => 'USD',
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
                        'presentmentMoney' => [
                            'amount' => '0.0',
                            'currencyCode' => 'USD',
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
            ])->recursive());

            $mock->shouldReceive('getTransactionBySourceIdAndSourceOrderId')->once()->with('test-auth-transaction-id', $sourceOrderId)->andReturn(collect([
                'capturedTransactionId' => null,
                'manuallyCapturable' => false,
                'totalUnsettledSet' => [
                    'presentmentMoney' => [
                        'amount' => '0.0',
                        'currencyCode' => 'USD',
                    ],
                ],
            ]));

            $mock->shouldReceive('save')->never();
        });

        $this->expectException(PaymentFailedException::class);

        $paymentService = resolve(PaymentService::class);
        $paymentService->captureOrCreatePayment($orderId, $sourceOrderId, Money::of(100, 'USD'));
    }

    public function testItCapturesPayment(): void
    {
        Event::fake([PaymentCompleteEvent::class]);

        $orderId = $sourceOrderId = 'test-order-id';

        $this->mock(ShopifyPaymentService::class, function (MockInterface $mock) use ($sourceOrderId) {
            $mock->shouldReceive('createMandatePayment')->never();

            $mock->shouldReceive('capturePayment')->once()->withArgs(function (string $actualSourceOrderId, string $actualTransactionId, Money $money) {
                $this->assertEquals('test-order-id', $actualSourceOrderId);
                $this->assertEquals('test-parent-transaction-id', $actualTransactionId);
                $this->assertTrue($money->isEqualTo(Money::of(100, 'USD')));

                return true;
            })->andReturn(collect([
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
                    'presentmentMoney' => [
                        'amount' => '0.0',
                        'currencyCode' => 'USD',
                    ],
                ],
                'authorizationExpiresAt' => null,
                'createdAt' => '2024-01-26T18:48:16Z',
                'manuallyCapturable' => false,
                'id' => 'gid://shopify/OrderTransaction/6429975511179',
                'kind' => 'CAPTURE',
                'status' => 'SUCCESS',
                'gateway' => 'shopify_payments',
                'test' => true,
                'paymentId' => 'risOALVUIL4AqYY6zpDqVsZPp',
            ])->recursive());
        });

        $this->partialMock(TransactionService::class, function (MockInterface $mock) use ($orderId, $sourceOrderId) {
            $mock->shouldReceive('getLatestTransaction')->once()->andReturn(TransactionValue::builder()->create([
                'id' => 'test-parent-transaction-id',
                'source_id' => 'test-parent-transaction-id',
                'order_id' => $orderId,
                'store_id' => App::context()->store->id,
                'captured_transaction_id' => null,
            ]));

            $mock->shouldReceive('getTransactionBySourceIdAndSourceOrderId')->once()->with('test-parent-transaction-id', $sourceOrderId)->andReturn(collect([
                'manuallyCapturable' => false,
                'totalUnsettledSet' => [
                    'presentmentMoney' => [
                        'amount' => '100.00',
                        'currencyCode' => 'USD',
                    ],
                ],
            ]));

            $mock->shouldReceive('save')->twice()->andReturnUsing(function (TransactionValue $transaction) {
                if ($transaction->id === null) {
                    $transaction->id = 'test-transaction-id';
                } else {
                    $this->assertEquals('test-parent-transaction-id', $transaction->id);
                }

                return $transaction;
            });
        });

        $paymentService = resolve(PaymentService::class);
        $result = $paymentService->captureOrCreatePayment($orderId, $sourceOrderId, Money::of(100, 'USD'));

        $this->assertEquals([
            'id' => 'test-transaction-id',
            'store_id' => App::context()->store->id,
            'order_id' => $orderId,
            'source_order_id' => $sourceOrderId,
            'transaction_source_name' => 'blackcart',
            'kind' => TransactionKind::CAPTURE->value,
            'status' => TransactionStatus::SUCCESS->value,
            'shop_amount' => 444,
            'shop_currency' => 'CAD',
            'customer_amount' => 330,
            'customer_currency' => 'USD',
            'source_id' => 'gid://shopify/OrderTransaction/6429975511179',
            'parent_transaction_id' => 'test-parent-transaction-id',
            'parent_transaction_source_id' => null,
            'authorization_expires_at' => null,
            'captured_transaction_id' => null,
            'captured_transaction_source_id' => null,
        ], $result->toArray());

        Event::assertDispatched(PaymentCompleteEvent::class, function (PaymentCompleteEvent $event) use ($sourceOrderId) {
            $this->assertEquals($sourceOrderId, $event->sourceOrderId);
            $this->assertFalse($event->outstandingAmountZeroAlready);

            return true;
        });
    }

    public function testItFailsToCapturesPaymentOnError(): void
    {
        $orderId = $sourceOrderId = 'test-order-id';

        $this->mock(ShopifyPaymentService::class, function (MockInterface $mock) use ($sourceOrderId) {
            $mock->shouldReceive('createMandatePayment')->never();

            $mock->shouldReceive('capturePayment')->once()->withArgs(function (string $actualSourceOrderId, string $actualTransactionId, Money $money) {
                $this->assertEquals('test-order-id', $actualSourceOrderId);
                $this->assertEquals('test-parent-transaction-id', $actualTransactionId);
                $this->assertTrue($money->isEqualTo(Money::of(100, 'USD')));

                return true;
            })->andReturn(collect([
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
                    'presentmentMoney' => [
                        'amount' => '0.0',
                        'currencyCode' => 'USD',
                    ],
                ],
                'authorizationExpiresAt' => null,
                'createdAt' => '2024-01-26T18:48:16Z',
                'manuallyCapturable' => false,
                'id' => 'gid://shopify/OrderTransaction/6429975511179',
                'kind' => 'CAPTURE',
                'status' => TransactionStatus::ERROR->name,
                'gateway' => 'shopify_payments',
                'test' => true,
                'paymentId' => 'risOALVUIL4AqYY6zpDqVsZPp',
            ])->recursive());
        });

        $this->mock(TransactionService::class, function (MockInterface $mock) use ($orderId, $sourceOrderId) {
            $mock->shouldReceive('getLatestTransaction')->once()->andReturn(TransactionValue::builder(['source_id' => 'test-parent-transaction-id', 'order_id' => $orderId, 'store_id' => App::context()->store->id, 'captured_transaction_id' => null])->create());

            $mock->shouldReceive('getTransactionBySourceIdAndSourceOrderId')->once()->with('test-parent-transaction-id', $sourceOrderId)->andReturn(collect([
                'totalUnsettledSet' => [
                    'presentmentMoney' => [
                        'amount' => '100.00',
                        'currencyCode' => 'USD',
                    ],
                ],
            ]));

            $mock->shouldReceive('save')->never();
        });

        $this->expectException(PaymentFailedException::class);

        $paymentService = resolve(PaymentService::class);
        $paymentService->captureOrCreatePayment($orderId, $sourceOrderId, Money::of(100, 'USD'));
    }

    public function testCreatePaymentFailsWithoutPaymentMandate(): void
    {
        $orderId = $sourceOrderId = 'test-order-id';

        $this->mock(ShopifyPaymentService::class, function (MockInterface $mock) use ($sourceOrderId) {
            $mock->shouldReceive('getOrderPaymentDetails')->once()->with($sourceOrderId)->andReturn(collect([
                'data' => ['order' => ['paymentCollectionDetails' => null]],
            ])->recursive());

            $mock->shouldReceive('createMandatePayment')->never();
            $mock->shouldReceive('capturePayment')->never();
        });

        $this->mock(TransactionService::class, function (MockInterface $mock) {
            $mock->shouldReceive('getLatestTransaction')->once()->andThrow(ModelNotFoundException::class);
            $mock->shouldReceive('getTransactionBySourceIdAndSourceOrderId')->never();
        });

        $this->expectException(PaymentMandateNotFoundException::class);

        $paymentService = resolve(PaymentService::class);
        $paymentService->captureOrCreatePayment($orderId, $sourceOrderId, Money::of(100, 'USD'));
    }

    public function testItVerifiesPayment(): void
    {
        $jobId = 'test-job-id';
        $paymentReferenceId = 'test-payment-reference-id';
        $orderId = 'test-order-id';

        $this->mock(ShopifyPaymentService::class, function (MockInterface $mock) use ($jobId, $paymentReferenceId, $orderId) {
            $mock->shouldReceive('getPaymentAttemptJob')->once()->with($jobId, $paymentReferenceId, $orderId)->andReturn(collect([
                'done' => true,
                'status' => PaymentStatus::PURCHASED->value,
            ]));
        });

        $paymentService = resolve(PaymentService::class);
        $result = $paymentService->verifyPayment($jobId, $paymentReferenceId, $orderId);

        $this->assertTrue($result);
    }

    public function testItDoesNotVerifyIncompletePayments(): void
    {
        $jobId = 'test-job-id';
        $paymentReferenceId = 'test-payment-reference-id';
        $orderId = 'test-order-id';

        $this->mock(ShopifyPaymentService::class, function (MockInterface $mock) use ($jobId, $paymentReferenceId, $orderId) {
            $mock->shouldReceive('getPaymentAttemptJob')->once()->with($jobId, $paymentReferenceId, $orderId)->andReturn(collect([
                'done' => false,
                'status' => PaymentStatus::PROCESSING->value,
            ]));
        });

        $paymentService = resolve(PaymentService::class);
        $result = $paymentService->verifyPayment($jobId, $paymentReferenceId, $orderId);

        $this->assertFalse($result);
    }

    public function testVerifyPaymentThrowsExceptionOnError(): void
    {
        $jobId = 'test-job-id';
        $paymentReferenceId = 'test-payment-reference-id';
        $orderId = 'test-order-id';

        $this->mock(ShopifyPaymentService::class, function (MockInterface $mock) use ($jobId, $paymentReferenceId, $orderId) {
            $mock->shouldReceive('getPaymentAttemptJob')->once()->with($jobId, $paymentReferenceId, $orderId)->andReturn(collect([
                'done' => true,
                'status' => PaymentStatus::ERROR->value,
            ]));
        });

        $paymentService = resolve(PaymentService::class);
        $this->expectException(PaymentFailedException::class);

        $paymentService->verifyPayment($jobId, $paymentReferenceId, $orderId);
    }

    public function testVerifyPaymentThrowsExceptionOnUnknownStatus(): void
    {
        $jobId = 'test-job-id';
        $paymentReferenceId = 'test-payment-reference-id';
        $orderId = 'test-order-id';

        $this->mock(ShopifyPaymentService::class, function (MockInterface $mock) use ($jobId, $paymentReferenceId, $orderId) {
            $mock->shouldReceive('getPaymentAttemptJob')->once()->with($jobId, $paymentReferenceId, $orderId)->andReturn(collect([
                'done' => true,
                'status' => 'unknown',
            ]));
        });

        $paymentService = resolve(PaymentService::class);
        $this->expectException(PaymentFailedException::class);

        $paymentService->verifyPayment($jobId, $paymentReferenceId, $orderId);
    }

    public function testCreateInitialAuthHoldThrowsExceptionIfJobFails(): void
    {
        Bus::fake([ReAuthJob::class]);
        Event::fake([CheckoutAuthorizationSuccessEvent::class]);
        Queue::fake([AuthExpiryNotificationJob::class]);

        $expectedResult = [
            'jobId' => 'test-job-id',
            'paymentReferenceId' => 'test-payment-reference-id',
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
                // getOrderById

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
                            'query' => [
                            ],
                        ],
                    ],
                ]),
            ]),
        ]);

        $orderValue = OrderValue::builder()->create([
            'storeId' => (string) App::context()->store->id,
        ]);

        $this->mock(BlackcartOrderService::class, function (MockInterface $mock) use ($orderValue) {
            $mock->expects('getOrderById')->with($orderValue->id)->andReturn($orderValue);
        });

        $this->expectException(ItemNotFoundException::class);

        $paymentService = resolve(PaymentService::class);
        $paymentService->createInitialAuthHold($orderValue);
        $this->assertDatabaseCount('payments_transactions', 0);

        Event::assertDispatched(CheckoutAuthorizationSuccessEvent::class, function (CheckoutAuthorizationSuccessEvent $event) use ($orderValue) {
            $this->assertEquals($orderValue->id, $event->orderId);
            $this->assertEquals($orderValue->sourceId, $event->sourceOrderId);

            return true;
        });
        Bus::assertDispatched(ReAuthJob::class);
        Queue::assertPushed(AuthExpiryNotificationJob::class);
    }

    public function testCreateInitialAuthHoldPollsAndSavesTransaction(): void
    {
        Bus::fake([ReAuthJob::class]);
        Event::fake([CheckoutAuthorizationSuccessEvent::class]);
        Queue::fake([AuthExpiryNotificationJob::class]);

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
                'id' => 'gid://shopify/OrderTransaction/6480340123779',
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
                            'done' => 'not done',
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
                Http::response([
                    'data' => [
                        'order' => [
                            'closed' => false,
                        ],
                    ],
                ]),
            ]),
        ]);

        $orderValue = OrderValue::builder()->create([
            'storeId' => (string) App::context()->store->id,
        ]);

        $this->mock(BlackcartOrderService::class, function (MockInterface $mock) use ($orderValue) {
            $mock->expects('getOrderById')->with($orderValue->id)->andReturn($orderValue);
        });

        $paymentService = resolve(PaymentService::class);
        $resultTransaction = $paymentService->createInitialAuthHold($orderValue);

        $this->assertNotNull($resultTransaction);

        $this->assertDatabaseCount('payments_transactions', 1);
        $resultTransaction = App\Domain\Payments\Models\Transaction::first();
        $this->assertEquals((string) App::context()->store->id, $resultTransaction->store_id);
        $this->assertEquals($orderValue->id, $resultTransaction->order_id);
        $this->assertEquals($orderValue->sourceId, $resultTransaction->source_order_id);
        $this->assertEquals(TransactionKind::AUTHORIZATION, $resultTransaction->kind);
        $this->assertEquals(TransactionStatus::SUCCESS, $resultTransaction->status);
        $this->assertEquals(TransactionRepository::SOURCE_TRANSACTION_NAME_BLACKCART, $resultTransaction->transaction_source_name);
        $this->assertEquals('gid://shopify/OrderTransaction/6480340123779', $resultTransaction->source_id);
        $this->assertEquals('2024-02-29', $resultTransaction->authorization_expires_at->toDateString());
        $this->assertEquals('1.35', $resultTransaction->shop_amount->getAmount()->toFloat());
        $this->assertEquals('CAD', $resultTransaction->shop_currency->value);
        $this->assertEquals('1.00', $resultTransaction->customer_amount->getAmount()->toFloat());
        $this->assertEquals('USD', $resultTransaction->customer_currency->value);

        Event::assertDispatched(CheckoutAuthorizationSuccessEvent::class, function (CheckoutAuthorizationSuccessEvent $event) use ($orderValue) {
            $this->assertEquals($orderValue->id, $event->orderId);
            $this->assertEquals($orderValue->sourceId, $event->sourceOrderId);

            return true;
        });
        Bus::assertDispatched(ReAuthJob::class);
        Queue::assertPushed(AuthExpiryNotificationJob::class);
    }

    #[DataProvider('orderOutstandingAmountProvider')]
    public function testItDoesNotCreateInitialAuthHoldOnOrderOutstandingAmountZero(int $amount): void
    {
        Bus::fake([ReAuthJob::class]);
        Event::fake([CheckoutAuthorizationSuccessEvent::class]);
        Queue::fake([AuthExpiryNotificationJob::class]);

        $orderValue = OrderValue::builder()->create([
            'storeId' => App::context()->store->id,
            'outstandingCustomerAmount' => Money::ofMinor($amount, CurrencyAlpha3::US_Dollar->value),
            'outstandingShopAmount' => Money::ofMinor($amount, CurrencyAlpha3::Canadian_Dollar->value),
        ]);

        Http::fake([
            'http://localhost:8080/api/stores/orders/' . $orderValue->id => Http::sequence()
                ->push(['order' => $orderValue->toArray()]),
        ]);

        $this->assertDatabaseCount('orders_transactions', 0);

        $paymentService = resolve(PaymentService::class);
        $Transaction = $paymentService->createInitialAuthHold($orderValue);

        $this->assertNull($Transaction);
        $this->assertDatabaseCount('orders_transactions', 0);

        Event::assertDispatched(CheckoutAuthorizationSuccessEvent::class, function (CheckoutAuthorizationSuccessEvent $event) use ($orderValue) {
            $this->assertEquals($orderValue->id, $event->orderId);
            $this->assertEquals($orderValue->sourceId, $event->sourceOrderId);

            return true;
        });

        Bus::assertNotDispatched(ReAuthJob::class);
        Queue::assertNotPushed(AuthExpiryNotificationJob::class);
    }

    public function testItDoesNotCreateAuthWhenOrderIsCancelled(): void
    {
        App::context(store: StoreValue::builder()->create());

        $order = OrderValue::builder()->create([
            'storeId' => App::context()->store->id,
            'outstandingCustomerAmount' => '10000',
            'customerCurrency' => 'USD',
            'status' => OrderStatus::CANCELLED,
        ]);

        $this->mock(ShopifyPaymentService::class, function (MockInterface $mock) {
            $mock->shouldNotReceive('getOrderPaymentDetails');
            $mock->shouldNotReceive('createMandatePayment');
        });
        $this->mock(TransactionService::class, function (MockInterface $mock) {
            $mock->shouldNotReceive('saveTransactionFromJob');
        });

        $paymentService = resolve(PaymentService::class);
        $result = $paymentService->createAuthHold($order);

        $this->assertNull($result);
        $this->assertDatabaseCount('orders_transactions', 0);
    }

    public function testItDoesNotCreateAuthWhenOrderIsCompleted(): void
    {
        App::context(store: StoreValue::builder()->create());

        $order = OrderValue::builder()->create([
            'storeId' => App::context()->store->id,
            'outstandingCustomerAmount' => '10000',
            'customerCurrency' => 'USD',
            'status' => OrderStatus::COMPLETED,
        ]);

        $this->mock(ShopifyPaymentService::class, function (MockInterface $mock) {
            $mock->shouldNotReceive('getOrderPaymentDetails');
            $mock->shouldNotReceive('createMandatePayment');
        });
        $this->mock(TransactionService::class, function (MockInterface $mock) {
            $mock->shouldNotReceive('saveTransactionFromJob');
        });

        $paymentService = resolve(PaymentService::class);
        $result = $paymentService->createAuthHold($order);

        $this->assertNull($result);
        $this->assertDatabaseCount('orders_transactions', 0);
    }

    public function testItCreatesAuthWithAmountForShopifyPlus(): void
    {
        App::context(store: StoreValue::builder()->shopifyPlus()->create());

        $order = OrderValue::builder()->create([
            'outstandingCustomerAmount' => '10000',
            'customerCurrency' => 'USD',
        ]);

        $this->mock(ShopifyPaymentService::class, function (MockInterface $mock) use ($order) {
            $mock->shouldReceive('getOrderPaymentDetails')->once()->with($order->sourceId)->andReturn(collect([
                'data' => ['order' => ['paymentCollectionDetails' => ['vaultedPaymentMethods' => [['id' => 'test-payment-mandate-id']]]]],
            ])->recursive());

            $mock->shouldReceive('createMandatePayment')->once()->withArgs(function (string $actualSourceOrderId, string $actualPaymentMandateId, bool $actualAutoCapture, Money $actualAmount) use ($order) {
                $this->assertEquals($order->sourceId, $actualSourceOrderId);
                $this->assertEquals('test-payment-mandate-id', $actualPaymentMandateId);
                $this->assertFalse($actualAutoCapture);
                $this->assertTrue($actualAmount->isEqualTo(Money::of(100, 'USD')));

                return true;
            })->andReturn(collect([
                'jobId' => 'test-job-id',
                'paymentReferenceId' => 'test-payment-reference-id',
            ]));
        });

        $this->mock(TransactionService::class, function (MockInterface $mock) use ($order) {
            $mock->shouldReceive('saveTransactionFromJob')->once()->withArgs(['test-job-id', $order])->andReturn(
                TransactionValue::builder()->create([
                    'id' => 'test-transaction-id',
                    'store_id' => App::context()->store->id,
                    'order_id' => $order->id,
                    'source_order_id' => $order->sourceId,
                    'transaction_source_name' => TransactionRepository::SOURCE_TRANSACTION_NAME_BLACKCART,
                    'kind' => TransactionKind::AUTHORIZATION,
                    'status' => TransactionStatus::SUCCESS,
                    'shop_amount' => 10000,
                    'shop_currency' => 'USD',
                    'customer_amount' => 10000,
                    'customer_currency' => 'USD',
                    'source_id' => 'gid://shopify/OrderTransaction/6480340123779',
                    'authorization_expires_at' => Date::parse('2024-02-29T16:58:03Z'),
                ])
            );
        });

        $paymentService = resolve(PaymentService::class);
        $paymentService->createAuthHold($order);
    }

    public function testItCreatesAuthWithoutAmountForShopifyBasic(): void
    {
        App::context(store: StoreValue::builder()->shopifyBasic()->create());

        $order = OrderValue::builder(['outstanding_customer_amount' => '10000', 'customer_currency' => 'USD'])->create();

        $this->mock(ShopifyPaymentService::class, function (MockInterface $mock) use ($order) {
            $mock->shouldReceive('getOrderPaymentDetails')->once()->with($order->sourceId)->andReturn(collect([
                'data' => ['order' => ['paymentCollectionDetails' => ['vaultedPaymentMethods' => [['id' => 'test-payment-mandate-id']]]]],
            ])->recursive());

            $mock->shouldReceive('createMandatePayment')->once()->withArgs(function (string $actualSourceOrderId, string $actualPaymentMandateId, bool $actualAutoCapture, ?Money $actualAmount) use ($order) {
                $this->assertEquals($order->sourceId, $actualSourceOrderId);
                $this->assertEquals('test-payment-mandate-id', $actualPaymentMandateId);
                $this->assertFalse($actualAutoCapture);
                $this->assertNull($actualAmount);

                return true;
            })->andReturn(collect([
                'jobId' => 'test-job-id',
                'paymentReferenceId' => 'test-payment-reference-id',
            ]));
        });

        $this->mock(ShopifyOrderService::class, function (MockInterface $mock) use ($order) {
            $mock->expects('isOrderArchived')->twice()->with($order->sourceId)->andReturnFalse();
        });

        $this->mock(TransactionService::class, function (MockInterface $mock) use ($order) {
            $mock->shouldReceive('saveTransactionFromJob')->once()->withArgs(['test-job-id', $order])->andReturn(
                TransactionValue::builder()->create([
                    'id' => 'test-transaction-id',
                    'store_id' => App::context()->store->id,
                    'order_id' => $order->id,
                    'source_order_id' => $order->sourceId,
                    'transaction_source_name' => TransactionRepository::SOURCE_TRANSACTION_NAME_BLACKCART,
                    'kind' => TransactionKind::AUTHORIZATION,
                    'status' => TransactionStatus::SUCCESS,
                    'shop_amount' => 10000,
                    'shop_currency' => 'USD',
                    'customer_amount' => 10000,
                    'customer_currency' => 'USD',
                    'source_id' => 'gid://shopify/OrderTransaction/6480340123779',
                    'authorization_expires_at' => Date::parse('2024-02-29T16:58:03Z'),
                ])
            );
        });

        $paymentService = resolve(PaymentService::class);
        $paymentService->createAuthHold($order);
    }

    public function testItCreatesAuthWithoutAmountForShopifyBasicWithArchiveBug(): void
    {
        App::context(store: StoreValue::builder()->shopifyBasic()->create());

        $order = OrderValue::builder()->create(['outstanding_customer_amount' => '10000', 'customer_currency' => 'USD']);

        $this->mock(ShopifyPaymentService::class, function (MockInterface $mock) use ($order) {
            $mock->shouldReceive('getOrderPaymentDetails')->once()->with($order->sourceId)->andReturn(collect([
                'data' => ['order' => ['paymentCollectionDetails' => ['vaultedPaymentMethods' => [['id' => 'test-payment-mandate-id']]]]],
            ])->recursive());

            $mock->shouldReceive('createMandatePayment')->once()->withArgs(function (string $actualSourceOrderId, string $actualPaymentMandateId, bool $actualAutoCapture, ?Money $actualAmount) use ($order) {
                $this->assertEquals($order->sourceId, $actualSourceOrderId);
                $this->assertEquals('test-payment-mandate-id', $actualPaymentMandateId);
                $this->assertFalse($actualAutoCapture);
                $this->assertNull($actualAmount);

                return true;
            })->andReturn(collect([
                'jobId' => 'test-job-id',
                'paymentReferenceId' => 'test-payment-reference-id',
            ]));
        });

        $this->mock(ShopifyOrderService::class, function (MockInterface $mock) use ($order) {
            $mock->expects('isOrderArchived')->twice()->with($order->sourceId)->andReturn(false, true);
            $mock->expects('openOrder')->once()->with($order->sourceId)->andReturnTrue();
        });

        $this->mock(TransactionService::class, function (MockInterface $mock) use ($order) {
            $mock->shouldReceive('saveTransactionFromJob')->once()->withArgs(['test-job-id', $order])->andReturn(
                TransactionValue::builder()->create([
                    'id' => 'test-transaction-id',
                    'store_id' => App::context()->store->id,
                    'order_id' => $order->id,
                    'source_order_id' => $order->sourceId,
                    'transaction_source_name' => TransactionRepository::SOURCE_TRANSACTION_NAME_BLACKCART,
                    'kind' => TransactionKind::AUTHORIZATION,
                    'status' => TransactionStatus::SUCCESS,
                    'shop_amount' => 10000,
                    'shop_currency' => 'USD',
                    'customer_amount' => 10000,
                    'customer_currency' => 'USD',
                    'source_id' => 'gid://shopify/OrderTransaction/6480340123779',
                    'authorization_expires_at' => Date::parse('2024-02-29T16:58:03Z'),
                ])
            );
        });

        $paymentService = resolve(PaymentService::class);
        $paymentService->createAuthHold($order);
    }

    public function testItTriggersInitialAuthHoldFailure(): void
    {
        Event::fake([InitialAuthFailedEvent::class]);

        $paymentService = resolve(PaymentService::class);
        $paymentService->triggerInitialAuthHoldFailure('order_id_1');

        Event::assertDispatched(InitialAuthFailedEvent::class, function (InitialAuthFailedEvent $event) {
            $this->assertEquals('order_id_1', $event->orderId);

            return true;
        });
    }

    public function testItDoesNotReAuthHoldOnKillSwitchEnabled(): void
    {
        Feature::fake(['shopify-perm-b-kill-reauth']);

        Bus::fake([
            ReAuthJob::class,
        ]);

        Event::fake([
            ReAuthFailedEvent::class,
            ReAuthSuccessEvent::class,
            PaymentCompleteEvent::class,
        ]);

        Queue::fake([
            AuthExpiryNotificationJob::class,
        ]);

        $orderValue = OrderValue::builder()->create([
            'storeId' => App::context()->store->id,
        ]);

        $paymentService = resolve(PaymentService::class);
        $result = $paymentService->createReAuthHold($orderValue);

        $this->assertNull($result);

        Event::assertNotDispatched(ReAuthFailedEvent::class);
        Event::assertNotDispatched(ReAuthSuccessEvent::class);
        Event::assertNotDispatched(PaymentCompleteEvent::class);
        Bus::assertNotDispatched(ReAuthJob::class);
        Queue::assertNotPushed(AuthExpiryNotificationJob::class);
    }

    public function testItReCreatesAuthHoldAndSavesTransactionAndDispatchesNextReAuthJob(): void
    {
        Bus::fake([
            ReAuthJob::class,
        ]);

        Event::fake([
            ReAuthSuccessEvent::class,
            PaymentCompleteEvent::class,
        ]);

        Queue::fake([
            AuthExpiryNotificationJob::class,
        ]);

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

        $expectedResult = [
            'jobId' => 'test-job-id',
            'paymentReferenceId' => 'test-payment-reference-id',
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
        $orderValue = OrderValue::builder()->create([
            'storeId' => App::context()->store->id,
        ]);

        $this->mock(BlackcartOrderService::class, function (MockInterface $mock) use ($orderValue) {
            $mock->expects('getOrderById')->with($orderValue->id)->andReturn($orderValue);
        });

        $paymentService = resolve(PaymentService::class);
        $paymentService->createReAuthHold($orderValue);

        Bus::assertDispatched(ReAuthJob::class);
        Event::assertNotDispatched(PaymentCompleteEvent::class);
        Event::assertDispatched(ReAuthSuccessEvent::class, function (ReAuthSuccessEvent $event) {
            return $event->authAmount->isEqualTo(Money::ofMinor(100, 'USD'));
        });
    }

    public function testItCapturesPaymentWhenReAuthFails(): void
    {
        Bus::fake([
            ReAuthJob::class,
        ]);

        Event::fake([
            ReAuthSuccessEvent::class,
            ReAuthFailedEvent::class,
            PaymentCompleteEvent::class,
        ]);

        $expectedResult = [
            'jobId' => 'test-job-id',
            'paymentReferenceId' => 'test-payment-reference-id',
        ];

        Http::fake([
            '*' => Http::sequence([
                Http::response([
                    'data' => [
                        'order' => [
                            'closed' => false,
                        ],
                    ],
                ]),
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
                                    'transactions' => [],
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

        $orderId = $sourceOrderId = 'test-order-id';
        App::context(store: StoreValue::builder()->shopifyPlus()->create());
        $orderValue = OrderValue::builder()->create([
            'store_id' => App::context()->store->id,
            'id' => $orderId,
            'source_id' => $sourceOrderId,
            'outstanding_customer_amount' => 10000,
        ]);

        $this->mock(ShopifyPaymentService::class, function (MockInterface $mock) use ($sourceOrderId) {
            $mock->shouldReceive('getOrderPaymentDetails')->twice()->with($sourceOrderId)->andReturn(collect([
                'data' => ['order' => ['paymentCollectionDetails' => ['vaultedPaymentMethods' => [['id' => 'test-payment-mandate-id']]]]],
            ])->recursive());

            $mock->shouldReceive('createMandatePayment')->once()->withArgs(function (string $actualSourceOrderId, string $actualPaymentMandateId, bool $actualAutoCapture, Money $actualAmount) use ($sourceOrderId) {
                $this->assertEquals($sourceOrderId, $actualSourceOrderId);
                $this->assertEquals('test-payment-mandate-id', $actualPaymentMandateId);
                $this->assertFalse($actualAutoCapture);
                $this->assertTrue($actualAmount->isEqualTo(Money::of(100, 'USD')));

                return true;
            })->andReturn(collect([
                'jobId' => 'test-job-id',
                'paymentReferenceId' => 'test-payment-reference-id',
            ]));

            $mock->shouldReceive('createMandatePayment')->once()->withArgs(function (string $actualSourceOrderId, string $actualPaymentMandateId, bool $actualAutoCapture, Money $actualAmount) use ($sourceOrderId) {
                $this->assertEquals($sourceOrderId, $actualSourceOrderId);
                $this->assertEquals('test-payment-mandate-id', $actualPaymentMandateId);
                $this->assertTrue($actualAutoCapture);
                $this->assertTrue($actualAmount->isEqualTo(Money::of(100, 'USD')));

                return true;
            })->andReturn(collect([
                'jobId' => 'test-job-id',
                'paymentReferenceId' => 'test-payment-reference-id',
            ]));

            $mock->shouldReceive('capturePayment')->never();
        });

        $this->partialMock(TransactionService::class, function (MockInterface $mock) use ($orderId, $sourceOrderId) {
            $mock->shouldReceive('saveTransactionFromJob')->once()
                ->andThrow(new ShopifyTransactionNotFoundException('An error occurred'));
            $mock->shouldReceive('getLatestTransaction')->once()->andReturn(
                TransactionValue::builder()->create([
                    'source_id' => 'test-auth-transaction-id',
                    'order_id' => $orderId,
                    'store_id' => App::context()->store->id,
                ])
            );

            $mock->shouldReceive('getTransactionsFromSourceJob')->once()->andReturn(collect([
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
                        'presentmentMoney' => [
                            'amount' => '0.0',
                            'currencyCode' => 'USD',
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
                        'presentmentMoney' => [
                            'amount' => '0.0',
                            'currencyCode' => 'USD',
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
                            'amount' => '0.0',
                            'currencyCode' => 'CAD',
                        ],
                        'presentmentMoney' => [
                            'amount' => '0.0',
                            'currencyCode' => 'USD',
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
                        'presentmentMoney' => [
                            'amount' => '0.0',
                            'currencyCode' => 'USD',
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
            ])->recursive());

            $mock->shouldReceive('save')->once()->andReturnArg(0);

            $mock->shouldReceive('getTransactionBySourceIdAndSourceOrderId')->once()->with('test-auth-transaction-id', $sourceOrderId)->andReturn(collect([
                'capturedTransactionId' => null,
                'manuallyCapturable' => false,
                'totalUnsettledSet' => [
                    'shopMoney' => [
                        'amount' => '0.0',
                        'currencyCode' => 'CAD',
                    ],
                    'presentmentMoney' => [
                        'amount' => '0.0',
                        'currencyCode' => 'USD',
                    ],
                ],
            ]));
        });

        $this->mock(BlackcartOrderService::class, function (MockInterface $mock) use ($orderValue) {
            $mock->expects('getOrderById')->with($orderValue->id)->andReturn($orderValue);
        });

        $paymentService = resolve(PaymentService::class);
        $paymentService->createReAuthHold($orderValue);

        Event::assertDispatched(ReAuthFailedEvent::class, function (ReAuthFailedEvent $event) {
            return $event->authAmount->isEqualTo(Money::of(100, 'USD'));
        });
        Event::assertNotDispatched(ReAuthSuccessEvent::class);
        Event::assertDispatched(PaymentCompleteEvent::class, function (PaymentCompleteEvent $event) use ($sourceOrderId) {
            $this->assertEquals($sourceOrderId, $event->sourceOrderId);
            $this->assertFalse($event->outstandingAmountZeroAlready);

            return true;
        });
        Bus::assertNotDispatched(ReAuthJob::class);
    }

    public function testItDoesNotCapturesPaymentWhenReAuthFailsAndOutstandingAmountIsZero(): void
    {
        Bus::fake([
            ReAuthJob::class,
        ]);

        Event::fake([
            ReAuthSuccessEvent::class,
            ReAuthFailedEvent::class,
            PaymentCompleteEvent::class,
        ]);

        $expectedResult = [
            'jobId' => 'test-job-id',
            'paymentReferenceId' => 'test-payment-reference-id',
        ];

        Http::fake([
            '*' => Http::sequence([
                Http::response([
                    'data' => [
                        'order' => [
                            'closed' => false,
                        ],
                    ],
                ]),
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
                                    'transactions' => [],
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

        $orderId = $sourceOrderId = 'test-order-id';
        App::context(store: StoreValue::builder()->shopifyPlus()->create());
        $orderValue = OrderValue::builder()->create([
            'store_id' => App::context()->store->id,
            'id' => $orderId,
            'source_id' => $sourceOrderId,
            'outstanding_customer_amount' => 10000,
        ]);

        $this->mock(ShopifyPaymentService::class, function (MockInterface $mock) use ($sourceOrderId) {
            $mock->shouldReceive('getOrderPaymentDetails')->twice()->with($sourceOrderId)->andReturn(collect([
                'data' => ['order' => ['paymentCollectionDetails' => ['vaultedPaymentMethods' => [['id' => 'test-payment-mandate-id']]]]],
            ])->recursive());

            $mock->shouldReceive('createMandatePayment')->once()->withArgs(function (string $actualSourceOrderId, string $actualPaymentMandateId, bool $actualAutoCapture, Money $actualAmount) use ($sourceOrderId) {
                $this->assertEquals($sourceOrderId, $actualSourceOrderId);
                $this->assertEquals('test-payment-mandate-id', $actualPaymentMandateId);
                $this->assertFalse($actualAutoCapture);
                $this->assertTrue($actualAmount->isEqualTo(Money::of(100, 'USD')));

                return true;
            })->andReturn(collect([
                'jobId' => 'test-job-id',
                'paymentReferenceId' => 'test-payment-reference-id',
            ]));

            $mock->shouldReceive('createMandatePayment')->once()->withArgs(function (string $actualSourceOrderId, string $actualPaymentMandateId, bool $actualAutoCapture, Money $actualAmount) use ($sourceOrderId) {
                $this->assertEquals($sourceOrderId, $actualSourceOrderId);
                $this->assertEquals('test-payment-mandate-id', $actualPaymentMandateId);
                $this->assertTrue($actualAutoCapture);
                $this->assertTrue($actualAmount->isEqualTo(Money::of(100, 'USD')));

                return true;
            })->andThrow(new ShopifyMandatePaymentOutstandingAmountZeroException());

            $mock->shouldReceive('capturePayment')->never();
        });

        $this->partialMock(TransactionService::class, function (MockInterface $mock) use ($orderId, $sourceOrderId) {
            $mock->shouldReceive('saveTransactionFromJob')->once()
                ->andThrow(new ShopifyTransactionNotFoundException('An error occurred'));

            $mock->shouldReceive('getLatestTransaction')->once()->andReturn(
                TransactionValue::builder()->create([
                    'source_id' => 'test-auth-transaction-id',
                    'order_id' => $orderId,
                    'store_id' => App::context()->store->id,
                ])
            );

            $mock->shouldReceive('getTransactionBySourceIdAndSourceOrderId')->once()->with('test-auth-transaction-id', $sourceOrderId)->andReturn(collect([
                'capturedTransactionId' => null,
                'manuallyCapturable' => false,
                'totalUnsettledSet' => [
                    'shopMoney' => [
                        'amount' => '0.0',
                        'currencyCode' => 'CAD',
                    ],
                    'presentmentMoney' => [
                        'amount' => '0.0',
                        'currencyCode' => 'USD',
                    ],
                ],
            ]));

            $mock->shouldReceive('getTransactionsFromSourceJob')->never();
            $mock->shouldReceive('save')->never();
        });

        $this->mock(BlackcartOrderService::class, function (MockInterface $mock) use ($orderValue) {
            $mock->expects('getOrderById')->with($orderValue->id)->andReturn($orderValue);
        });

        $paymentService = resolve(PaymentService::class);
        $paymentService->createReAuthHold($orderValue);

        Event::assertNotDispatched(ReAuthFailedEvent::class);
        Event::assertNotDispatched(ReAuthSuccessEvent::class);
        Event::assertDispatched(PaymentCompleteEvent::class, function (PaymentCompleteEvent $event) use ($sourceOrderId) {
            $this->assertEquals($sourceOrderId, $event->sourceOrderId);
            $this->assertTrue($event->outstandingAmountZeroAlready);

            return true;
        });
        Bus::assertNotDispatched(ReAuthJob::class);
    }

    public function testItDoesNotCapturesPaymentWhenRetryFailureLimitReached(): void
    {
        Bus::fake([
            ReAuthJob::class,
        ]);

        Event::fake([
            ReAuthSuccessEvent::class,
            ReAuthFailedEvent::class,
            PaymentCompleteEvent::class,
        ]);

        $expectedResult = [
            'jobId' => 'test-job-id',
            'paymentReferenceId' => 'test-payment-reference-id',
        ];

        Http::fake([
            '*' => Http::sequence([
                Http::response([
                    'data' => [
                        'order' => [
                            'closed' => false,
                        ],
                    ],
                ]),
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
                                    'transactions' => [],
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

        $orderId = $sourceOrderId = 'test-order-id';
        App::context(store: StoreValue::builder()->shopifyPlus()->create());
        $orderValue = OrderValue::builder()->create([
            'store_id' => App::context()->store->id,
            'id' => $orderId,
            'source_id' => $sourceOrderId,
            'outstanding_customer_amount' => 10000,
        ]);

        $this->mock(ShopifyPaymentService::class, function (MockInterface $mock) use ($sourceOrderId) {
            $mock->shouldReceive('getOrderPaymentDetails')->twice()->with($sourceOrderId)->andReturn(collect([
                'data' => ['order' => ['paymentCollectionDetails' => ['vaultedPaymentMethods' => [['id' => 'test-payment-mandate-id']]]]],
            ])->recursive());

            $mock->shouldReceive('createMandatePayment')->once()->withArgs(function (string $actualSourceOrderId, string $actualPaymentMandateId, bool $actualAutoCapture, Money $actualAmount) use ($sourceOrderId) {
                $this->assertEquals($sourceOrderId, $actualSourceOrderId);
                $this->assertEquals('test-payment-mandate-id', $actualPaymentMandateId);
                $this->assertFalse($actualAutoCapture);
                $this->assertTrue($actualAmount->isEqualTo(Money::of(100, 'USD')));

                return true;
            })->andReturn(collect([
                'jobId' => 'test-job-id',
                'paymentReferenceId' => 'test-payment-reference-id',
            ]));

            $mock->shouldReceive('createMandatePayment')->once()->withArgs(function (string $actualSourceOrderId, string $actualPaymentMandateId, bool $actualAutoCapture, Money $actualAmount) use ($sourceOrderId) {
                $this->assertEquals($sourceOrderId, $actualSourceOrderId);
                $this->assertEquals('test-payment-mandate-id', $actualPaymentMandateId);
                $this->assertTrue($actualAutoCapture);
                $this->assertTrue($actualAmount->isEqualTo(Money::of(100, 'USD')));

                return true;
            })->andThrow(new ShopifyMandatePaymentRetryFailureLimitReachedException());

            $mock->shouldReceive('capturePayment')->never();
        });

        $this->partialMock(TransactionService::class, function (MockInterface $mock) use ($orderId, $sourceOrderId) {
            $mock->shouldReceive('saveTransactionFromJob')->once()
                ->andThrow(new ShopifyTransactionNotFoundException('An error occurred'));

            $mock->shouldReceive('getLatestTransaction')->once()->andReturn(
                TransactionValue::builder()->create([
                    'source_id' => 'test-auth-transaction-id',
                    'order_id' => $orderId,
                    'store_id' => App::context()->store->id,
                ])
            );

            $mock->shouldReceive('getTransactionBySourceIdAndSourceOrderId')->once()->with('test-auth-transaction-id', $sourceOrderId)->andReturn(collect([
                'capturedTransactionId' => null,
                'manuallyCapturable' => false,
                'totalUnsettledSet' => [
                    'shopMoney' => [
                        'amount' => '0.0',
                        'currencyCode' => 'CAD',
                    ],
                    'presentmentMoney' => [
                        'amount' => '0.0',
                        'currencyCode' => 'USD',
                    ],
                ],
            ]));

            $mock->shouldReceive('getTransactionsFromSourceJob')->never();
            $mock->shouldReceive('save')->never();
        });

        $this->mock(BlackcartOrderService::class, function (MockInterface $mock) use ($orderValue) {
            $mock->expects('getOrderById')->with($orderValue->id)->andReturn($orderValue);
        });

        $paymentService = resolve(PaymentService::class);
        $paymentService->createReAuthHold($orderValue);

        Event::assertNotDispatched(ReAuthFailedEvent::class);
        Event::assertNotDispatched(ReAuthSuccessEvent::class);
        Event::assertDispatched(PaymentCompleteEvent::class, function (PaymentCompleteEvent $event) use ($sourceOrderId) {
            $this->assertEquals($sourceOrderId, $event->sourceOrderId);
            $this->assertTrue($event->outstandingAmountZeroAlready);

            return true;
        });
        Bus::assertNotDispatched(ReAuthJob::class);
    }

    public function testItSendReauthNoticeEmail(): void
    {
        Mail::fake();

        $order = OrderValue::builder()->create([
            'orderData' => [
                'email' => 'matthew+test@blackcart.com',
                'customer' => [
                    'first_name' => 'Matthew',
                ],
                'name' => '#1001',
            ],
        ]);

        $transaction = TransactionValue::builder()->create([
            'store_id' => $order->storeId,
            'order_id' => $order->id,
            'customer_amount' => 500, // major?
        ]);

        $shopifyPaymentResponse = collect([
            'totalUnsettledSet' => [
                'presentmentMoney' => [
                    'amount' => '500.00',
                    'currencyCode' => 'USD',
                ],
            ],
        ]);

        $this->mock(TransactionService::class)->shouldReceive('getBySourceId')->once()->andReturn($transaction);
        $this->mock(ShopifyPaymentService::class)->shouldReceive('getTransaction')->once()->andReturn($shopifyPaymentResponse);

        AuthExpiryNotificationJob::dispatch($order, $transaction->sourceId);
        Mail::assertSent(ReAuthNotice::class);
    }

    public function testItDoesntSendReauthNoticeEmailOnCapturedPayment(): void
    {
        Mail::fake();

        $order = OrderValue::builder()->create();

        $transaction = TransactionValue::builder()->create([
            'store_id' => $order->storeId,
            'order_id' => $order->id,
            'customer_amount' => 500, // major?,
            'captured_transaction_id' => '1234567890', //already captured
        ]);

        $shopifyPaymentResponse = collect([
            'totalUnsettledSet' => [
                'presentmentMoney' => [
                    'amount' => '500.00',
                    'currencyCode' => 'USD',
                ],
            ],
        ]);

        $this->mock(TransactionService::class)->shouldReceive('getBySourceId')->once()->andReturn($transaction);
        $this->mock(ShopifyPaymentService::class)->shouldReceive('getTransaction')->never();

        AuthExpiryNotificationJob::dispatch($order, $transaction->sourceId);
        Mail::assertNotSent(ReAuthNotice::class);
    }

    public function testItDoesntSendReauthNoticeEmailOnValueMismatch(): void
    {
        Mail::fake();

        $order = OrderValue::builder()->create();

        $transaction = TransactionValue::builder()->create([
            'store_id' => $order->storeId,
            'order_id' => $order->id,
            'customer_amount' => 600, // major?,
        ]);

        $shopifyPaymentResponse = collect([
            'totalUnsettledSet' => [
                'presentmentMoney' => [
                    'amount' => '500.00',
                    'currencyCode' => 'USD',
                ],
            ],
        ]);

        $this->mock(TransactionService::class)->shouldReceive('getBySourceId')->once()->andReturn($transaction);
        $this->mock(ShopifyPaymentService::class)->shouldReceive('getTransaction')->once()->andReturn($shopifyPaymentResponse);

        AuthExpiryNotificationJob::dispatch($order, $transaction->sourceId);
        Mail::assertNotSent(ReAuthNotice::class);
    }

    public function testItDoesntSendReauthNoticeEmailOnAmountZero(): void
    {
        Mail::fake();

        $order = OrderValue::builder()->create();

        $transaction = TransactionValue::builder()->create([
            'store_id' => $order->storeId,
            'order_id' => $order->id,
            'customer_amount' => 0, // major?,
        ]);

        $shopifyPaymentResponse = collect([
            'totalUnsettledSet' => [
                'presentmentMoney' => [
                    'amount' => '500.00',
                    'currencyCode' => 'USD',
                ],
            ],
        ]);

        $this->mock(TransactionService::class)->shouldReceive('getBySourceId')->once()->andReturn($transaction);
        $this->mock(ShopifyPaymentService::class)->shouldReceive('getTransaction')->once()->andReturn($shopifyPaymentResponse);

        AuthExpiryNotificationJob::dispatch($order, $transaction->sourceId);
        Mail::assertNotSent(ReAuthNotice::class);
    }

    #[DataProvider('orderOutstandingAmountProvider')]
    public function testItDoesNotReAuthOnOrderOutstandingAmountZero(int $amount): void
    {
        Bus::fake([
            ReAuthJob::class,
        ]);

        Event::fake([
            ReAuthSuccessEvent::class,
            ReAuthFailedEvent::class,
        ]);

        $orderValue = OrderValue::builder()->create([
            'storeId' => App::context()->store->id,
            'outstandingCustomerAmount' => Money::ofMinor($amount, CurrencyAlpha3::US_Dollar->value),
            'outstandingShopAmount' => Money::ofMinor($amount, CurrencyAlpha3::Canadian_Dollar->value),
        ]);

        Http::fake([
            'http://localhost:8080/api/stores/orders/' . $orderValue->id => Http::sequence()
                ->push(['order' => $orderValue->toArray()]),
        ]);

        $this->assertDatabaseCount('orders_transactions', 0);

        $paymentService = resolve(PaymentService::class);
        $Transaction = $paymentService->createReAuthHold($orderValue);

        $this->assertNull($Transaction);
        $this->assertDatabaseCount('orders_transactions', 0);

        Event::assertNotDispatched(ReAuthFailedEvent::class);
        Event::assertNotDispatched(ReAuthSuccessEvent::class);
        Bus::assertNotDispatched(ReAuthJob::class);
    }

    public function testItDoesNotReAuthHoldNoCaptureOnFailureOnKillSwitchEnabled(): void
    {
        Feature::fake(['shopify-perm-b-kill-reauth']);

        Bus::fake([
            ReAuthJob::class,
        ]);

        Event::fake([
            ReAuthSuccessEvent::class,
            ReAuthFailedEvent::class,
        ]);

        $orderValue = OrderValue::builder()->create([
            'storeId' => App::context()->store->id,
        ]);

        $paymentService = resolve(PaymentService::class);
        $result = $paymentService->createReAuthHoldNoCaptureOnFailure($orderValue->id);

        $this->assertNull($result);

        Event::assertNotDispatched(ReAuthFailedEvent::class);
        Event::assertNotDispatched(ReAuthSuccessEvent::class);
        Bus::assertNotDispatched(ReAuthJob::class);
    }

    public function testItCreatesReAuthHoldNoCaptureOnFailure(): void
    {
        Bus::fake([
            ReAuthJob::class,
        ]);

        Event::fake([
            ReAuthSuccessEvent::class,
        ]);

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

        $expectedResult = [
            'jobId' => 'test-job-id',
            'paymentReferenceId' => 'test-payment-reference-id',
        ];

        $orderValue = OrderValue::builder()->create([
            'storeId' => App::context()->store->id,
        ]);

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

        $paymentService = resolve(PaymentService::class);
        $Transaction = $paymentService->createReAuthHoldNoCaptureOnFailure($orderValue->id);

        Event::assertDispatched(ReAuthSuccessEvent::class, function (ReAuthSuccessEvent $event) use ($Transaction) {
            $this->assertEquals($Transaction->customerAmount, $event->authAmount);
            $this->assertEquals($Transaction->sourceOrderId, $event->sourceOrderId);

            return true;
        });
        Bus::assertDispatched(ReAuthJob::class, function (ReAuthJob $job) use ($orderValue, $Transaction) {
            $this->assertEquals($orderValue->id, $job->order->id);
            $this->assertEquals($Transaction->id, $job->transaction->id);

            return true;
        });
    }

    #[DataProvider('orderOutstandingAmountProvider')]
    public function testItDoesNotCreateReAuthHoldNoCaptureOnFailureOnOrderOutstandingAmountZero(int $amount): void
    {
        Bus::fake([
            ReAuthJob::class,
        ]);

        Event::fake([
            ReAuthSuccessEvent::class,
        ]);

        $orderValue = OrderValue::builder()->create([
            'storeId' => App::context()->store->id,
            'outstandingCustomerAmount' => Money::ofMinor($amount, CurrencyAlpha3::US_Dollar->value),
            'outstandingShopAmount' => Money::ofMinor($amount, CurrencyAlpha3::Canadian_Dollar->value),
        ]);

        Http::fake([
            'http://localhost:8080/api/stores/orders/' . $orderValue->id => Http::sequence()
                ->push(['order' => $orderValue->toArray()]),
        ]);

        $this->assertDatabaseCount('orders_transactions', 0);

        $paymentService = resolve(PaymentService::class);
        $Transaction = $paymentService->createReAuthHoldNoCaptureOnFailure($orderValue->id);

        $this->assertNull($Transaction);
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

    public function testItDoesNotSendAuthExpiryNotificationOnReAuthFeatureFlagOff(): void
    {
        Feature::fake(['shopify-perm-b-kill-reauth']);

        Mail::fake();

        $orderValue = OrderValue::builder()->create([
            'storeId' => App::context()->store->id,
        ]);

        $transaction = TransactionValue::builder()->create([
            'store_id' => $orderValue->storeId,
            'order_id' => $orderValue->id,
        ]);

        $this->mock(TransactionService::class)->shouldReceive('getBySourceId')->never();
        $this->mock(ShopifyPaymentService::class)->shouldReceive('getTransaction')->never();

        $paymentService = resolve(PaymentService::class);
        $paymentService->sendAuthExpiryNotification($orderValue, $transaction->sourceId);

        Mail::assertNotSent(ReAuthNotice::class);
    }

    public function testItDoesNotSendAuthExpiryNotificationOnEmailFeatureFlagOff(): void
    {
        Feature::fake(['shopify-perm-b-merchant-reauth-notice-email']);

        Mail::fake();

        $orderValue = OrderValue::builder()->create([
            'storeId' => App::context()->store->id,
        ]);

        $transaction = TransactionValue::builder()->create([
            'store_id' => $orderValue->storeId,
            'order_id' => $orderValue->id,
        ]);

        $this->mock(TransactionService::class)->shouldReceive('getBySourceId')->never();
        $this->mock(ShopifyPaymentService::class)->shouldReceive('getTransaction')->never();

        $paymentService = resolve(PaymentService::class);
        $paymentService->sendAuthExpiryNotification($orderValue, $transaction->sourceId);

        Mail::assertNotSent(ReAuthNotice::class);
    }
}
