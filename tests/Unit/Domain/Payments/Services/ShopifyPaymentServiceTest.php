<?php
declare(strict_types=1);

namespace Tests\Unit\Domain\Payments\Services;

use App;
use App\Domain\Payments\Enums\TransactionKind;
use App\Domain\Payments\Enums\TransactionStatus;
use App\Domain\Payments\Exceptions\ShopifyMandatePaymentOutstandingAmountZeroException;
use App\Domain\Payments\Exceptions\ShopifyMandatePaymentRetryFailureLimitReachedException;
use App\Domain\Payments\Services\ShopifyPaymentService;
use App\Domain\Shared\Services\ShopifyGraphqlService;
use App\Domain\Stores\Values\Store as StoreValue;
use Brick\Money\Money;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class ShopifyPaymentServiceTest extends TestCase
{
    public function testItCreatesMandatePaymentWithAmountForNonShopifyPlusStore(): void
    {
        App::context(store: StoreValue::builder()->create([
            'ecommerce_platform_plan' => 'shopify_basic', // non-Plus
        ]));

        $sourceOrderId = 'gid://shopify/Order/123456789';
        $paymentMandateId = 'gid://shopify/PaymentMandate/123456789';
        $amount = Money::of(13.56, 'USD');

        $shopifyGraphqlService = $this->partialMock(ShopifyGraphqlService::class);
        $shopifyGraphqlService->shouldReceive('postMutation')->once()->withArgs(
            function ($mutation, $variables) {
                $this->assertEquals('gid://shopify/Order/123456789', $variables['id']);
                $this->assertEquals(32, strlen($variables['idempotencyKey']));
                $this->assertEquals('gid://shopify/PaymentMandate/123456789', $variables['mandateId']);
                $this->assertFalse($variables['autoCapture']);
                $this->assertNull($variables['amount']); // Verify that amount got changed to null

                return true;
            }
        )->andReturn([
                'data' => [
                    'orderCreateMandatePayment' => [
                        'job' => ['id' => 'test-job-id'],
                        'paymentReferenceId' => 'test-payment-reference-id',
                    ],
                ],
            ]);

        $shopifyPaymentService = resolve(ShopifyPaymentService::class);

        $result = $shopifyPaymentService->createMandatePayment($sourceOrderId, $paymentMandateId, false, $amount);

        $this->assertEquals([
            'jobId' => 'test-job-id',
            'paymentReferenceId' => 'test-payment-reference-id',
        ], $result->toArray());
    }

    public function testItCreatesMandatePaymentWithNullAmountForNonShopifyPlusStore(): void
    {
        App::context(store: StoreValue::builder()->create([
            'ecommerce_platform_plan' => 'shopify_basic', // non-Plus
        ]));

        $sourceOrderId = 'gid://shopify/Order/123456789';
        $paymentMandateId = 'gid://shopify/PaymentMandate/123456789';

        $shopifyGraphqlService = $this->partialMock(ShopifyGraphqlService::class);
        $shopifyGraphqlService->shouldReceive('postMutation')->once()->withArgs(
            function ($mutation, $variables) {
                $this->assertEquals('gid://shopify/Order/123456789', $variables['id']);
                $this->assertEquals(32, strlen($variables['idempotencyKey']));
                $this->assertEquals('gid://shopify/PaymentMandate/123456789', $variables['mandateId']);
                $this->assertTrue($variables['autoCapture']);
                $this->assertNull($variables['amount']); // Verify that amount got changed to null

                return true;
            }
        )->andReturn([
                'data' => [
                    'orderCreateMandatePayment' => [
                        'job' => ['id' => 'test-job-id'],
                        'paymentReferenceId' => 'test-payment-reference-id',
                    ],
                ],
            ]);

        $shopifyPaymentService = resolve(ShopifyPaymentService::class);

        $result = $shopifyPaymentService->createMandatePayment($sourceOrderId, $paymentMandateId, true, null);

        $this->assertEquals([
            'jobId' => 'test-job-id',
            'paymentReferenceId' => 'test-payment-reference-id',
        ], $result->toArray());
    }

    public function testItCreatesMandatePaymentWithAmountForShopifyPlusStore(): void
    {
        App::context(store: StoreValue::builder()->create([
            'ecommerce_platform_plan' => 'shopify_plus',
        ]));

        $sourceOrderId = 'gid://shopify/Order/123456789';
        $paymentMandateId = 'gid://shopify/PaymentMandate/123456789';
        $amount = Money::of(13.56, 'USD');

        $shopifyGraphqlService = $this->partialMock(ShopifyGraphqlService::class);
        $shopifyGraphqlService->shouldReceive('postMutation')->once()->withArgs(
            function ($mutation, $variables) {
                $this->assertEquals('gid://shopify/Order/123456789', $variables['id']);
                $this->assertEquals(32, strlen($variables['idempotencyKey']));
                $this->assertEquals('gid://shopify/PaymentMandate/123456789', $variables['mandateId']);
                $this->assertTrue($variables['autoCapture']);
                $this->assertEquals(13.56, $variables['amount']['amount']);
                $this->assertEquals('USD', $variables['amount']['currencyCode']);

                return true;
            }
        )->andReturn([
                'data' => [
                    'orderCreateMandatePayment' => [
                        'job' => ['id' => 'test-job-id'],
                        'paymentReferenceId' => 'test-payment-reference-id',
                    ],
                ],
            ]);

        $shopifyPaymentService = resolve(ShopifyPaymentService::class);

        $result = $shopifyPaymentService->createMandatePayment($sourceOrderId, $paymentMandateId, true, $amount);

        $this->assertEquals([
            'jobId' => 'test-job-id',
            'paymentReferenceId' => 'test-payment-reference-id',
        ], $result->toArray());
    }

    public function testItCreatesMandatePaymentWithNullAmountForShopifyPlusStore(): void
    {
        App::context(store: StoreValue::builder()->create([
            'ecommerce_platform_plan' => 'shopify_plus',
        ]));

        $sourceOrderId = 'gid://shopify/Order/123456789';
        $paymentMandateId = 'gid://shopify/PaymentMandate/123456789';

        $shopifyGraphqlService = $this->partialMock(ShopifyGraphqlService::class);
        $shopifyGraphqlService->shouldReceive('postMutation')->once()->withArgs(
            function ($mutation, $variables) {
                $this->assertEquals('gid://shopify/Order/123456789', $variables['id']);
                $this->assertEquals(32, strlen($variables['idempotencyKey']));
                $this->assertEquals('gid://shopify/PaymentMandate/123456789', $variables['mandateId']);
                $this->assertFalse($variables['autoCapture']);
                $this->assertNull($variables['amount']); // Verify that amount got changed to null

                return true;
            }
        )->andReturn([
                'data' => [
                    'orderCreateMandatePayment' => [
                        'job' => ['id' => 'test-job-id'],
                        'paymentReferenceId' => 'test-payment-reference-id',
                    ],
                ],
            ]);

        $shopifyPaymentService = resolve(ShopifyPaymentService::class);

        $result = $shopifyPaymentService->createMandatePayment($sourceOrderId, $paymentMandateId, false, null);

        $this->assertEquals([
            'jobId' => 'test-job-id',
            'paymentReferenceId' => 'test-payment-reference-id',
        ], $result->toArray());
    }

    public function testItDoesNotCreateMandatePaymentOnTotalOutstandingAmountZeroError(): void
    {
        App::context(store: StoreValue::builder()->create());

        $sourceOrderId = 'gid://shopify/Order/123456789';
        $paymentMandateId = 'gid://shopify/PaymentMandate/123456789';

        Http::fake([
            App::context()->store->domain . '/admin/api/unstable/graphql.json' => Http::response([
                'data' => [
                    'orderCreateMandatePayment' => [
                        'job' => null,
                        'paymentReferenceId' => null,
                        'userErrors' => [
                            [
                                'field' => null,
                                'message' => 'total_outstanding amount must be greater than zero.',
                            ],
                        ],
                    ],
                ],
            ]),
        ]);

        $this->expectException(ShopifyMandatePaymentOutstandingAmountZeroException::class);

        $shopifyPaymentService = resolve(ShopifyPaymentService::class);

        $shopifyPaymentService->createMandatePayment($sourceOrderId, $paymentMandateId, false, null);
    }

    public function testItDoesNotCreateMandatePaymentOnRetryLimitReachedError(): void
    {
        App::context(store: StoreValue::builder()->create());

        $sourceOrderId = 'gid://shopify/Order/123456789';
        $paymentMandateId = 'gid://shopify/PaymentMandate/123456789';

        Http::fake([
            App::context()->store->domain . '/admin/api/unstable/graphql.json' => Http::response([
                'data' => [
                    'orderCreateMandatePayment' => [
                        'job' => null,
                        'paymentReferenceId' => null,
                        'userErrors' => [
                            [
                                'field' => null,
                                'message' => "you're unable to retry payment collection using this mandate because you have reached the threshold limit for failed payments.",
                            ],
                        ],
                    ],
                ],
            ]),
        ]);

        $this->expectException(ShopifyMandatePaymentRetryFailureLimitReachedException::class);

        $shopifyPaymentService = resolve(ShopifyPaymentService::class);

        $shopifyPaymentService->createMandatePayment($sourceOrderId, $paymentMandateId, false, null);
    }

    public function testItGetsOrderPaymentDetails(): void
    {
        App::context(store: StoreValue::builder()->create());

        $sourceOrderId = 'test-source-order-id';

        Http::fake([
            '*' => Http::response([
                'data' => [
                    'order' => [
                        'lineItems' => [
                            'nodes' => [],
                        ],
                        'paymentCollectionDetails' => [
                            'vaultedPaymentMethods' => [
                                ['id' => 'test-payment-mandate-id'],
                            ],
                        ],
                        'paymentTerms' => [
                            'id' => 'test-payment-terms-id',
                            'paymentSchedules' => [
                                'nodes' => [],
                            ],
                        ],
                    ],
                ],
            ]),
        ]);

        $shopifyPaymentService = resolve(ShopifyPaymentService::class);

        $result = $shopifyPaymentService->getOrderPaymentDetails($sourceOrderId);

        $this->assertEquals('test-payment-mandate-id', $result['data']['order']['paymentCollectionDetails']['vaultedPaymentMethods'][0]['id']);
    }

    public function testItGetsPaymentAttemptJob(): void
    {
        App::context(store: StoreValue::builder()->create());

        $jobId = 'test-job-id';
        $paymentReferenceId = 'test-payment-reference-id';
        $sourceOrderId = 'test-source-order-id';

        Http::fake([
            '*' => Http::response([
                'data' => [
                    'job' => [
                        'id' => $jobId,
                        'done' => true,
                        'query' => [
                            'orderPaymentStatus' => [
                                'status' => 'PURCHASED',
                                'translatedErrorMessage' => null,
                            ],
                        ],
                    ],
                ],
            ]),
        ]);

        $shopifyPaymentService = resolve(ShopifyPaymentService::class);

        $result = $shopifyPaymentService->getPaymentAttemptJob($jobId, $paymentReferenceId, $sourceOrderId);

        $this->assertEquals([
            'done' => true,
            'status' => 'PURCHASED',
            'errorMessage' => null,
        ], $result->toArray());
    }

    public function testItCapturesPayment(): void
    {
        App::context(store: StoreValue::builder()->create());

        Http::fake([
            '*' => Http::response(
                [
                    'data' => [
                        'orderCapture' => [
                            'transaction' => [
                                'id' => 'gid://shopify/OrderTransaction/1234',
                                'createdAt' => Date::now()->toDateTimeString(),
                                'status' => TransactionStatus::SUCCESS->value,
                                'kind' => TransactionKind::CAPTURE->value,
                                'accountNumber' => '****1234',
                                'amountSet' => [
                                    'shopMoney' => [
                                        'amount' => '100.00',
                                        'currencyCode' => 'CAD',
                                    ],
                                    'presentmentMoney' => [
                                        'amount' => '73.64',
                                        'currencyCode' => 'USD',
                                    ],
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

        $shopifyPaymentService = resolve(ShopifyPaymentService::class);
        $payment = $shopifyPaymentService->capturePayment('test-order-id', 'incorrect-id', Money::of(100, 'USD'));

        $this->assertArrayHasKey('id', $payment);
        $this->assertArrayHasKey('createdAt', $payment);
        $this->assertArrayHasKey('status', $payment);
        $this->assertArrayHasKey('kind', $payment);
        $this->assertArrayHasKey('accountNumber', $payment);
        $this->assertArrayHasKey('amountSet', $payment);
    }
}
