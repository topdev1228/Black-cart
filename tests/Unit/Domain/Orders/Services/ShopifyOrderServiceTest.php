<?php
declare(strict_types=1);

namespace Tests\Unit\Domain\Orders\Services;

use App\Domain\Orders\Enums\FulfillmentOrderStatus;
use App\Domain\Orders\Enums\ShopifyRefundLineItemRestockType;
use App\Domain\Orders\Exceptions\ShopifyOrderCannotBeEditedException;
use App\Domain\Orders\Exceptions\ShopifyUpdatePaymentScheduleDueDateOnPaidOrderException;
use App\Domain\Orders\GraphQL\Shopify\Stable\Operations\RefundCreate;
use App\Domain\Orders\GraphQL\Shopify\Stable\Operations\RefundCreate\RefundCreate\Refund\Refund;
use App\Domain\Orders\GraphQL\Shopify\Stable\Operations\RefundCreate\RefundCreate\RefundCreatePayload;
use App\Domain\Orders\GraphQL\Shopify\Stable\Operations\RefundCreate\RefundCreateResult;
use App\Domain\Orders\GraphQL\Shopify\Stable\Types\RefundLineItemInput;
use App\Domain\Orders\Models\Order;
use App\Domain\Orders\Services\ShopifyOrderService;
use App\Domain\Orders\Values\Order as OrderValue;
use App\Domain\Orders\Values\ShopifyRefundLineItemInput;
use App\Domain\Shared\Exceptions\Shopify\ShopifyMutationClientException;
use App\Domain\Shared\Exceptions\Shopify\ShopifyServerException;
use App\Domain\Shared\Services\ShopifyGraphqlService;
use App\Domain\Stores\Models\Store;
use Brick\Money\Money;
use Carbon\CarbonImmutable;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use PHPUnit\Framework\Attributes\DataProvider;
use Spawnia\Sailor\Testing\UsesSailorMocks;
use Tests\Fixtures\Domains\Orders\Traits\ShopifyAddTagsResponsesTestData;
use Tests\Fixtures\Domains\Orders\Traits\ShopifyUpdatePaymentTermsResponsesTestData;
use Tests\Fixtures\Domains\Shared\Shopify\Traits\ShopifyErrorsTestData;
use Tests\TestCase;

class ShopifyOrderServiceTest extends TestCase
{
    use ShopifyAddTagsResponsesTestData;
    use ShopifyUpdatePaymentTermsResponsesTestData;
    use ShopifyErrorsTestData;
    use UsesSailorMocks;

    protected Store $currentStore;
    protected ShopifyOrderService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->currentStore = Store::withoutEvents(function () {
            return Store::factory()->create();
        });
        App::context(store: $this->currentStore);

        $this->service = resolve(ShopifyOrderService::class);
    }

    #[DataProvider('shopifyErrorExceptionsForMutationProvider')]
    public function testItDoesNotAddTagsOnShopifyError(
        array $responseJson,
        int $httpStatusCode,
        string $expectedException,
    ): void {
        $order = Order::withoutEvents(function () {
            return Order::factory()->tbybOnly(10000, 10000)
                ->netSales(10000, 10000, 0, 0)->create(['store_id' => $this->currentStore->id]);
        });

        Http::fake([
            App::context()->store->domain . '/admin/api/*/graphql.json' => Http::sequence()
                ->push($responseJson, $httpStatusCode),
        ]);
        // We catch all Shopify exceptions and throw InternalShopifyRequestException instead
        $this->expectException($expectedException);

        $this->service->addTags($order->source_id, ['blackcart']);

        Http::assertSent(function (Request $request) {
            // TODO: validate graphql query
            return $request->method() === 'POST' &&
                $request->hasHeader('X-Shopify-Access-Token', App::context()->store->accessToken);
        });
    }

    public function testItAddsTagsMockShopifyGraphqlService(): void
    {
        $order = Order::withoutEvents(function () {
            return Order::factory()->tbybOnly(10000, 10000)
                ->netSales(10000, 10000, 0, 0)->create(['store_id' => $this->currentStore->id]);
        });

        $this->mock(ShopifyGraphqlService::class)
            ->shouldReceive('postMutation')
            ->with(
                <<<'QUERY'
                    mutation tagsAdd ($id: ID!, $tags: [String!]!) {
                      tagsAdd(
                        id: $id,
                        tags: $tags
                      ) {
                        userErrors {
                          field,
                          message
                        }
                      }
                    }
                    QUERY,
                [
                    'id' => $order->source_id,
                    'tags' => ['blackcart'],
                ]
            )
            ->once()
            ->andReturn($this->getShopifyAddTagsSuccessResponse());

        $service = resolve(ShopifyOrderService::class);
        $service->addTags($order->source_id, ['blackcart']);
    }

    public function testItAddsTags(): void
    {
        $order = Order::withoutEvents(function () {
            return Order::factory()->tbybOnly(10000, 10000)
                ->netSales(10000, 10000, 0, 0)->create(['store_id' => $this->currentStore->id]);
        });

        Http::fake([
            App::context()->store->domain . '/admin/api/*/graphql.json' => Http::sequence()
                ->push($this->getShopifyAddTagsSuccessResponse()),
        ]);

        $this->service->addTags($order->source_id, ['blackcart']);

        Http::assertSent(function (Request $request) {
            // TODO: validate graphql query
            return $request->method() === 'POST' &&
                $request->hasHeader('X-Shopify-Access-Token', App::context()->store->accessToken);
        });
    }

    public function testItReleasesFulfillment(): void
    {
        Http::fake([
            '*graphql.json' => Http::sequence()
                ->push([
                    'data' => [
                        'order' => [
                            'fulfillmentOrders' => [
                                'nodes' => [
                                    [
                                        'id' => 'testFulfillmentOrderId',
                                        'status' => FulfillmentOrderStatus::CANCELLED->name,
                                        'createdAt' => Date::now()->toIso8601String(),
                                    ],
                                    [
                                        'id' => 'testFulfillmentOrderId2',
                                        'status' => FulfillmentOrderStatus::ON_HOLD->name,
                                        'createdAt' => Date::now()->toIso8601String(),
                                    ],
                                    [
                                        'id' => 'testFulfillmentOrderId3',
                                        'status' => FulfillmentOrderStatus::IN_PROGRESS->name,
                                        'createdAt' => Date::now()->toIso8601String(),
                                    ],
                                ],
                            ],
                        ],
                    ],
                ], 200)
                ->push([
                    'data' => [
                        'fulfillmentOrderReleaseHold' => [
                            'fulfillmentOrder' => [
                                'id' => 'testFulfillmentOrderId', 'status' => FulfillmentOrderStatus::IN_PROGRESS,
                            ],
                        ],
                    ],
                ], 200),
        ]);

        $this->service->releaseFulfillment('test-source-id');

        Http::assertSentCount(2);
        Http::assertSent(function (Request $request) {
            return $request->method() === 'POST' &&
                $request->hasHeader('X-Shopify-Access-Token', App::context()->store->accessToken);
        });
    }

    public function testItAddsCustomLineItems(): void
    {
        Http::fake([
            '*' => Http::sequence()
                ->push([
                    'data' => [
                        'orderEditBegin' => [
                            'calculatedOrder' => [
                                'id' => Str::shopifyGid(Str::uuid(), 'CalculatedOrder'),
                            ],
                        ],
                    ],
                ])
                ->push([
                    'data' => [
                        'orderEditAddCustomItem' => [
                            'calculatedLineItem' => [
                                'id' => 'gid://shopify/CalculatedLineItem/2ddbfc8e-bdc7-405d-b1a2-f0870d3d4b8f',
                            ],
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
                ])
                ->push([
                    'data' => [
                        'orderEditCommit' => [
                            'order' => [
                                'id' => 'gid://shopify/Order/4188101345409',
                                'lineItems' => [
                                    'nodes' => [
                                        [
                                            'id' => 'gid://shopify/LineItem/10972956557441',
                                            'product' => [
                                                'id' => 'gid://shopify/Product/6672585523329',
                                            ],
                                            'variant' => [
                                                'id' => 'gid://shopify/ProductVariant/39732921204865',
                                            ],
                                            'title' => 'The Collection Snowboard: Liquid',
                                            'variantTitle' => null,
                                            'image' => [
                                                'url' => 'https://cdn.shopify.com/s/files/1/0568/0739/1361/products/Main_b13ad453-477c-4ed1-9b43-81f3345adfd6.jpg?v=1707514531',
                                            ],
                                            'quantity' => 1,
                                            'originalUnitPriceSet' => [
                                                'shopMoney' => [
                                                    'amount' => '688.64',
                                                    'currencyCode' => 'CAD',
                                                ],
                                                'presentmentMoney' => [
                                                    'amount' => '507.0',
                                                    'currencyCode' => 'USD',
                                                ],
                                            ],
                                            'originalTotalSet' => [
                                                'shopMoney' => [
                                                    'amount' => '688.64',
                                                    'currencyCode' => 'CAD',
                                                ],
                                                'presentmentMoney' => [
                                                    'amount' => '507.0',
                                                    'currencyCode' => 'USD',
                                                ],
                                            ],
                                            'discountAllocations' => [],
                                            'taxLines' => [
                                                [
                                                    'rate' => 0.065,
                                                    'ratePercentage' => 6.5,
                                                    'priceSet' => [
                                                        'shopMoney' => [
                                                            'amount' => '44.77',
                                                            'currencyCode' => 'CAD',
                                                        ],
                                                        'presentmentMoney' => [
                                                            'amount' => '32.96',
                                                            'currencyCode' => 'USD',
                                                        ],
                                                    ],
                                                ],
                                                [
                                                    'rate' => 0.036,
                                                    'ratePercentage' => 3.6,
                                                    'priceSet' => [
                                                        'shopMoney' => [
                                                            'amount' => '24.79',
                                                            'currencyCode' => 'CAD',
                                                        ],
                                                        'presentmentMoney' => [
                                                            'amount' => '18.25',
                                                            'currencyCode' => 'USD',
                                                        ],
                                                    ],
                                                ],
                                                [
                                                    'rate' => 0.0,
                                                    'ratePercentage' => 0.0,
                                                    'priceSet' => [
                                                        'shopMoney' => [
                                                            'amount' => '0.0',
                                                            'currencyCode' => 'CAD',
                                                        ],
                                                        'presentmentMoney' => [
                                                            'amount' => '0.0',
                                                            'currencyCode' => 'USD',
                                                        ],
                                                    ],
                                                ],
                                            ],
                                        ],
                                        [
                                            'id' => 'gid://shopify/LineItem/10974668128385',
                                            'product' => null,
                                            'variant' => null,
                                            'title' => 'Blackcart TBYB Adjustment',
                                            'variantTitle' => null,
                                            'image' => null,
                                            'quantity' => 1,
                                            'originalUnitPriceSet' => [
                                                'shopMoney' => [
                                                    'amount' => '271.86',
                                                    'currencyCode' => 'CAD',
                                                ],
                                                'presentmentMoney' => [
                                                    'amount' => '200.0',
                                                    'currencyCode' => 'USD',
                                                ],
                                            ],
                                            'originalTotalSet' => [
                                                'shopMoney' => [
                                                    'amount' => '271.86',
                                                    'currencyCode' => 'CAD',
                                                ],
                                                'presentmentMoney' => [
                                                    'amount' => '200.0',
                                                    'currencyCode' => 'USD',
                                                ],
                                            ],
                                            'discountAllocations' => [],
                                            'taxLines' => [
                                                [
                                                    'rate' => 0.036,
                                                    'ratePercentage' => 3.6,
                                                    'priceSet' => [
                                                        'shopMoney' => [
                                                            'amount' => '0.0',
                                                            'currencyCode' => 'CAD',
                                                        ],
                                                        'presentmentMoney' => [
                                                            'amount' => '0.0',
                                                            'currencyCode' => 'USD',
                                                        ],
                                                    ],
                                                ],
                                                [
                                                    'rate' => 0.0,
                                                    'ratePercentage' => 0.0,
                                                    'priceSet' => [
                                                        'shopMoney' => [
                                                            'amount' => '0.0',
                                                            'currencyCode' => 'CAD',
                                                        ],
                                                        'presentmentMoney' => [
                                                            'amount' => '0.0',
                                                            'currencyCode' => 'USD',
                                                        ],
                                                    ],
                                                ],
                                                [
                                                    'rate' => 0.065,
                                                    'ratePercentage' => 6.5,
                                                    'priceSet' => [
                                                        'shopMoney' => [
                                                            'amount' => '0.0',
                                                            'currencyCode' => 'CAD',
                                                        ],
                                                        'presentmentMoney' => [
                                                            'amount' => '0.0',
                                                            'currencyCode' => 'USD',
                                                        ],
                                                    ],
                                                ],
                                            ],
                                        ],
                                        [
                                            'id' => 'gid://shopify/LineItem/10974668456065',
                                            'product' => null,
                                            'variant' => null,
                                            'title' => 'Blackcart TBYB Adjustment',
                                            'variantTitle' => null,
                                            'image' => null,
                                            'quantity' => 1,
                                            'originalUnitPriceSet' => [
                                                'shopMoney' => [
                                                    'amount' => '135.93',
                                                    'currencyCode' => 'CAD',
                                                ],
                                                'presentmentMoney' => [
                                                    'amount' => '100.0',
                                                    'currencyCode' => 'USD',
                                                ],
                                            ],
                                            'originalTotalSet' => [
                                                'shopMoney' => [
                                                    'amount' => '135.93',
                                                    'currencyCode' => 'CAD',
                                                ],
                                                'presentmentMoney' => [
                                                    'amount' => '100.0',
                                                    'currencyCode' => 'USD',
                                                ],
                                            ],
                                            'discountAllocations' => [],
                                            'taxLines' => [
                                                [
                                                    'rate' => 0.036,
                                                    'ratePercentage' => 3.6,
                                                    'priceSet' => [
                                                        'shopMoney' => [
                                                            'amount' => '0.0',
                                                            'currencyCode' => 'CAD',
                                                        ],
                                                        'presentmentMoney' => [
                                                            'amount' => '0.0',
                                                            'currencyCode' => 'USD',
                                                        ],
                                                    ],
                                                ],
                                                [
                                                    'rate' => 0.0,
                                                    'ratePercentage' => 0.0,
                                                    'priceSet' => [
                                                        'shopMoney' => [
                                                            'amount' => '0.0',
                                                            'currencyCode' => 'CAD',
                                                        ],
                                                        'presentmentMoney' => [
                                                            'amount' => '0.0',
                                                            'currencyCode' => 'USD',
                                                        ],
                                                    ],
                                                ],
                                                [
                                                    'rate' => 0.065,
                                                    'ratePercentage' => 6.5,
                                                    'priceSet' => [
                                                        'shopMoney' => [
                                                            'amount' => '0.0',
                                                            'currencyCode' => 'CAD',
                                                        ],
                                                        'presentmentMoney' => [
                                                            'amount' => '0.0',
                                                            'currencyCode' => 'USD',
                                                        ],
                                                    ],
                                                ],
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                            'userErrors' => [],
                        ],
                    ],
                ]),
        ]);

        $this->service->addCustomLineItem('test-id', Money::of(100, 'USD'), 'Test');

        Http::assertSequencesAreEmpty();
        Http::assertSent(function (Request $request) {
            return $request->method() === 'POST' &&
                $request->hasHeader('X-Shopify-Access-Token', App::context()->store->accessToken);
        });
    }

    public function testItErrorsWhenUnableToOpenOrderEdit(): void
    {
        Http::fake([
            '*' => Http::sequence()
                ->push([
                    'data' => [
                        'orderEditBegin' => [],
                    ],
                ]),
        ]);

        $this->expectException(ShopifyServerException::class);
        $this->service->addCustomLineItem('test-id', Money::of(100, 'USD'), 'Test');
    }

    public function testItThrowsOnOrderCannotBeEditedError(): void
    {
        Http::fake([
            '*' => Http::response(
                body: [
                    'data' => [
                        '__typename' => 'Mutation',
                        'orderEditBegin' => [
                            'userErrors' => [
                                [
                                    '__typename' => 'UserError',
                                    'field' => [],
                                    'message' => 'The order cannot be edited.',
                                ],
                            ],
                        ],
                    ],
                ],
            ),
        ]);

        $this->expectException(ShopifyOrderCannotBeEditedException::class);
        $this->service->addCustomLineItem('test-id', Money::of(100, 'USD'), 'Test');
    }

    public function testItCreatesRefunds(): void
    {
        RefundCreate::mock()->shouldReceive('execute')->once()->withArgs(function (string $sourceOrderId, string $note, float $amount, string $gateway, array $refundLineItems, string $currency, string $parentTransactionId) {
            $this->assertEquals('gid://shopify/Order/test-source-order-id', $sourceOrderId);
            $this->assertEquals('test-note', $note);
            $this->assertEquals(100.0, $amount);
            $this->assertEquals('test-gateway', $gateway);
            $this->assertEquals('USD', $currency);
            $this->assertEquals('test-parent-transaction-id', $parentTransactionId);

            /** @var RefundLineItemInput[] $refundLineItems */
            $this->assertCount(2, $refundLineItems);
            $this->assertInstanceOf(RefundLineItemInput::class, $refundLineItems[0]);
            $this->assertInstanceOf(RefundLineItemInput::class, $refundLineItems[1]);

            $this->assertEquals('3f40f259-cac1-36b7-811b-c8a2ea2e5e95', $refundLineItems[0]->lineItemId);
            $this->assertEquals(ShopifyRefundLineItemRestockType::NO_RESTOCK->name, $refundLineItems[0]->restockType);
            $this->assertEquals(1, $refundLineItems[0]->quantity);
            $this->assertEquals('57107a89-dc2e-3f01-8dd8-ba16a3974dc2', $refundLineItems[0]->locationId);

            $this->assertEquals('3f40f259-cac1-36b7-811b-c8a2ea2e5e96', $refundLineItems[1]->lineItemId);
            $this->assertEquals(ShopifyRefundLineItemRestockType::NO_RESTOCK->name, $refundLineItems[1]->restockType);
            $this->assertEquals(2, $refundLineItems[1]->quantity);
            $this->assertEquals('57107a89-dc2e-3f01-8dd8-ba16a3974dc2', $refundLineItems[1]->locationId);

            return true;
        })->andReturn(
            RefundCreateResult::fromData(
                data: RefundCreate\RefundCreate::make(
                    RefundCreatePayload::make(
                        userErrors: [],
                        refund: Refund::make('test-refund-id'),
                    )
                )
            )
        );

        $shopifyOrderService = resolve(ShopifyOrderService::class);

        /** @noinspection PhpParamsInspection */
        $shopifyOrderService->createRefund('test-source-order-id', Money::ofMinor(10000, 'USD'), 'test-note', ShopifyRefundLineItemInput::collection([
            ShopifyRefundLineItemInput::from([
                'line_item_id' => '3f40f259-cac1-36b7-811b-c8a2ea2e5e95',
                'restock_type' => 'no-restock',
                'quantity' => 1,
                'location_id' => '57107a89-dc2e-3f01-8dd8-ba16a3974dc2',
            ]),
            ShopifyRefundLineItemInput::from([
                'line_item_id' => '3f40f259-cac1-36b7-811b-c8a2ea2e5e96',
                'restock_type' => 'no-restock',
                'quantity' => 2,
                'location_id' => '57107a89-dc2e-3f01-8dd8-ba16a3974dc2',
            ]),
        ]), 'test-gateway', 'test-parent-transaction-id');
    }

    public function testItThrowsOnRefundCreateError(): void
    {
        Http::fake([
            '*' => Http::response(
                body: [
                    'data' => [
                        '__typename' => 'Mutation',
                        'refundCreate' => [
                            '__typename' => 'RefundCreatePayload',
                            'userErrors' => [
                                [
                                    '__typename' => 'UserError',
                                    'field' => [
                                        'transactions',
                                    ],
                                    'message' => "Transactions not on 'store-credit', 'exchange-credit', or 'cash' gateways require a parent_id",
                                ],
                            ],
                            'refund' => null,
                        ],
                    ],
                    'extensions' => [
                        'cost' => [
                            'requestedQueryCost' => 20,
                            'actualQueryCost' => 20,
                            'throttleStatus' => [
                                'maximumAvailable' => 2000.0,
                                'currentlyAvailable' => 1980,
                                'restoreRate' => 100.0,
                            ],
                        ],
                    ],
                ],
                headers: [
                    'content-type' => 'application/json; charset=utf-8',
                ]
            ),
        ]);

        $this->expectException(ShopifyMutationClientException::class);

        $shopifyOrderService = resolve(ShopifyOrderService::class);
        $shopifyOrderService->createRefund(
            sourceOrderId: 'test-source-order-id',
            amount: Money::ofMinor(10000, 'USD'),
            note: 'test-note',
            gateway: 'test-gateway',
        );
    }

    public function testItUpdatesShopifyPaymentScheduleDueDate(): void
    {
        $trialExpiryDatetime = CarbonImmutable::now()->addDays(7);
        $order = Order::withoutEvents(function () {
            return Order::factory()->tbybOnly(10000, 10000)
                ->netSales(10000, 10000, 0, 0)->create(['store_id' => $this->currentStore->id, 'payment_terms_id' => '123456789']);
        });
        $orderValue = OrderValue::from($order);

        Http::fake([
            App::context()->store->domain . '/admin/api/*/graphql.json' => Http::sequence()
                ->push($this->getShopifyUpdatePaymentTermsSuccessResponse()),
        ]);

        $this->service->updateShopifyPaymentScheduleDueDate($orderValue->paymentTermsId, $trialExpiryDatetime);

        Http::assertSent(function (Request $request) {
            return $request->method() === 'POST' &&
                $request->hasHeader('X-Shopify-Access-Token', App::context()->store->accessToken);
        });
    }

    public function testItDoesNotUpdateShopifyPaymentScheduleDueDateOnPaidOrderError(): void
    {
        $trialExpiryDatetime = CarbonImmutable::now()->addDays(7);
        $order = Order::withoutEvents(function () {
            return Order::factory()->tbybOnly(10000, 10000)
                ->netSales(10000, 10000, 0, 0)->create(['store_id' => $this->currentStore->id, 'payment_terms_id' => '123456789']);
        });
        $orderValue = OrderValue::from($order);

        Http::fake([
            App::context()->store->domain . '/admin/api/*/graphql.json' => Http::sequence()
                ->push($this->getShopifyUpdatePaymentTermsOnPaidOrderErrorResponse()),
        ]);
        $this->expectException(ShopifyUpdatePaymentScheduleDueDateOnPaidOrderException::class);

        $this->service->updateShopifyPaymentScheduleDueDate($orderValue->paymentTermsId, $trialExpiryDatetime);

        Http::assertSent(function (Request $request) {
            return $request->method() === 'POST' &&
                $request->hasHeader('X-Shopify-Access-Token', App::context()->store->accessToken);
        });
    }

    public function testItDoesNotUpdateShopifyPaymentScheduleDueDateClientError(): void
    {
        $trialExpiryDatetime = CarbonImmutable::now()->addDays(7);
        $order = Order::withoutEvents(function () {
            return Order::factory()->tbybOnly(10000, 10000)
                ->netSales(10000, 10000, 0, 0)->create(['store_id' => $this->currentStore->id, 'payment_terms_id' => '123456789']);
        });
        $orderValue = OrderValue::from($order);

        Http::fake([
            App::context()->store->domain . '/admin/api/*/graphql.json' => Http::sequence()
                ->push($this->getShopifyUpdatePaymentTermsClientErrorResponse()),
        ]);

        $this->expectException(ShopifyMutationClientException::class);

        $this->service->updateShopifyPaymentScheduleDueDate($orderValue->paymentTermsId, $trialExpiryDatetime);

        Http::assertSent(function (Request $request) {
            return $request->method() === 'POST' &&
                $request->hasHeader('X-Shopify-Access-Token', App::context()->store->accessToken);
        });
    }

    #[DataProvider('shopifyErrorExceptionsForMutationProvider')]
    public function testItDoesNotUpdateShopifyPaymentScheduleDueDateOnShopifyError(
        array $responseJson,
        int $httpStatusCode,
        string $expectedException,
    ): void {
        $trialExpiryDatetime = CarbonImmutable::now()->addDays(7);
        $order = Order::withoutEvents(function () {
            return Order::factory()->tbybOnly(10000, 10000)
                ->netSales(10000, 10000, 0, 0)->create(['store_id' => $this->currentStore->id, 'payment_terms_id' => '123456789']);
        });
        $orderValue = OrderValue::from($order);

        Http::fake([
            App::context()->store->domain . '/admin/api/*/graphql.json' => Http::sequence()
                ->push($responseJson, $httpStatusCode),
        ]);

        $this->expectException($expectedException);
        $this->service->updateShopifyPaymentScheduleDueDate($orderValue->paymentTermsId, $trialExpiryDatetime);

        Http::assertSent(function (Request $request) {
            return $request->method() === 'POST' &&
                $request->hasHeader('X-Shopify-Access-Token', App::context()->store->accessToken);
        });
    }

    public function testItUpdatesShopifyPaymentScheduleDueDateMockShopifyGraphqlService(): void
    {
        CarbonImmutable::setTestNow(CarbonImmutable::create(2024, 4, 24, 12));

        $trialExpiryDatetime = CarbonImmutable::now()->addDays(7);

        $order = Order::withoutEvents(function () {
            return Order::factory()->tbybOnly(10000, 10000)
                ->netSales(10000, 10000, 0, 0)->create(['store_id' => $this->currentStore->id, 'payment_terms_id' => '123456789']);
        });
        $orderValue = OrderValue::from($order);

        $this->mock(ShopifyGraphqlService::class)
            ->shouldReceive('postMutation')
            ->withArgs(function ($query, $input) use ($orderValue) {
                $this->assertEquals(
                    Str::trim(
                        preg_replace(
                            '/\s+/S',
                            ' ',
                            <<<'QUERY'
                            mutation PaymentTermsUpdate($input: PaymentTermsUpdateInput!) {
                                paymentTermsUpdate(input: $input) {
                                    paymentTerms {
                                        id
                                    }
                                    userErrors {
                                        code
                                        field
                                        message
                                    }
                                }
                            }
                            QUERY
                        )
                    ),
                    Str::trim(preg_replace('/\s+/S', ' ', $query))
                );

                $this->assertEquals(
                    [
                        'input' => [
                            'paymentTermsId' => 'gid://shopify/PaymentTerms/' . $orderValue->paymentTermsId,
                            'paymentTermsAttributes' => [
                                'paymentSchedules' => [['dueAt' => '2024-05-01T12:00:00.000000Z']],
                            ],
                        ],
                    ],
                    $input
                );

                return true;
            })
            ->once()
            ->andReturn($this->getShopifyUpdatePaymentTermsSuccessResponse());

        $service = resolve(ShopifyOrderService::class);
        $service->updateShopifyPaymentScheduleDueDate($orderValue->paymentTermsId, $trialExpiryDatetime);
    }
}
