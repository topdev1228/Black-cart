<?php
declare(strict_types=1);

namespace Tests\Unit\Domain\Orders\Services;

use App\Domain\Orders\Enums\LineItemStatus;
use App\Domain\Orders\Enums\LineItemStatusUpdatedBy;
use App\Domain\Orders\Enums\OrderStatus;
use App\Domain\Orders\Enums\ShopifyRefundLineItemRestockType;
use App\Domain\Orders\Enums\TransactionKind;
use App\Domain\Orders\Events\LineItemSavedEvent;
use App\Domain\Orders\Events\PaymentRequiredEvent;
use App\Domain\Orders\Events\TrialableDeliveredEvent;
use App\Domain\Orders\Exceptions\ShopifyOrderCannotBeEditedException;
use App\Domain\Orders\Mail\AssumedDeliveryMerchantReminder;
use App\Domain\Orders\Mail\OrderConfirmation;
use App\Domain\Orders\Models\LineItem;
use App\Domain\Orders\Models\Order;
use App\Domain\Orders\Repositories\OrderRepository;
use App\Domain\Orders\Services\OrderService;
use App\Domain\Orders\Services\ShopifyOrderService;
use App\Domain\Orders\Services\TransactionService;
use App\Domain\Orders\Services\TrialService;
use App\Domain\Orders\Values\Collections\OrderCollection;
use App\Domain\Orders\Values\Collections\ShopifyRefundLineItemInputCollection;
use App\Domain\Orders\Values\LineItem as LineItemValue;
use App\Domain\Orders\Values\Order as OrderValue;
use App\Domain\Orders\Values\Transaction;
use App\Domain\Shared\Services\ShopifyGraphqlService;
use App\Domain\Shopify\Exceptions\InternalShopifyRequestException;
use App\Domain\Stores\Models\Store;
use App\Domain\Stores\Values\Store as StoreValue;
use Brick\Money\Money;
use Event;
use Feature;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Mockery\MockInterface;
use PHPUnit\Framework\Attributes\DataProvider;
use Str;
use Tests\Fixtures\Domains\Orders\Traits\ShopifyAddTagsResponsesTestData;
use Tests\Fixtures\Domains\Orders\Traits\ShopifyUpdatePaymentTermsResponsesTestData;
use Tests\Fixtures\Domains\Shared\Shopify\Traits\ShopifyErrorsTestData;
use Tests\TestCase;

class OrderServiceTest extends TestCase
{
    use ShopifyAddTagsResponsesTestData;
    use ShopifyUpdatePaymentTermsResponsesTestData;
    use ShopifyErrorsTestData;

    protected $repositoryMock;
    protected Store $store;
    protected OrderService $service;
    protected $trialService;
    protected $simpleOrderJson;
    protected $complexOrderJson;

    protected function setUp(): void
    {
        parent::setUp();

        $this->repositoryMock = $this->mock(OrderRepository::class);
        $this->trialService = $this->mock(TrialService::class);
        $this->service = resolve(OrderService::class);
        $this->simpleOrderJson = collect($this->loadFixtureData('order.json', 'Orders'));
        $this->complexOrderJson = collect($this->loadFixtureData('order-complex.json', 'Orders'));
        $this->store = Store::withoutEvents(function () {
            return Store::factory()->create();
        });

        App::context(store: StoreValue::from($this->store));
    }

    public function testGet(): void
    {
        $value = OrderValue::builder()->create([
            'id' => (string) Str::uuid(),
        ]);

        $this->repositoryMock->shouldReceive('getById')->with($value->id)->andReturn($value);

        $return = $this->service->getById($value->id);

        $this->assertEquals($value, $return);
    }

    public function testItGetsOrderBySourceId(): void
    {
        $value = OrderValue::builder()->create([
            'source_id' => (string) Str::uuid(),
        ]);

        $this->repositoryMock->shouldReceive('getBySourceId')->with($value->sourceId)->andReturn($value);

        $return = $this->service->getBySourceId($value->sourceId);

        $this->assertEquals($value, $return);
    }

    public function testGetNotFound(): void
    {
        $this->expectException(ModelNotFoundException::class);
        $this->repositoryMock->shouldReceive('getById')->with('non_existent_order_id')
            ->andThrow(new ModelNotFoundException('No query results for model [App\Domain\Programs\Models\Order] non_existent_order_id'));

        $this->service->getById('non_existent_order_id');
    }

    public function testCreate(): void
    {
        $value = OrderValue::builder()->create();
        $this->repositoryMock->shouldReceive('create')->andReturn($value);

        $return = $this->service->create($value);

        $this->assertEquals($value->id, $return->id);
    }

    public function testCreateGeneratesTrials(): void
    {
        $testData = $this->loadFixtureData('order.json', 'Orders');

        $value = OrderValue::builder()->create([
            'order_data' => $testData,
        ]);

        $this->repositoryMock->shouldReceive('create')->andReturn($value);

        $return = $this->service->create($value);

        $this->assertEquals($value->id, $return->id);
    }

    public function testUpdate(): void
    {
        $value = OrderValue::builder()->create();
        $this->repositoryMock->shouldReceive('update')->andReturn($value);

        $return = $this->service->update($value);

        $this->assertEquals($value->id, $return->id);
    }

    public function testGetAll(): void
    {
        $valueCollection = new OrderCollection(Order::class, [OrderValue::builder()->create()]);
        $this->repositoryMock->shouldReceive('all')->andReturn($valueCollection);

        $return = $this->service->all();

        $this->assertEquals($valueCollection, $return);
    }

    public function testThatItDoesNotEndTrialBeforeExpiryOnOrderNotFound(): void
    {
        Event::fake();
        $this->expectException(ModelNotFoundException::class);

        $this->repositoryMock->shouldReceive('getById')
            ->with('non-existent-order-id')
            ->andThrow(ModelNotFoundException::class);

        $this->service->endTrialBeforeExpiry('non-existent-order-id');

        Event::assertNotDispatched(PaymentRequiredEvent::class);
    }

    public function testThatItEndsTrialBeforeExpiry(): void
    {
        Event::fake([
            PaymentRequiredEvent::class,
        ]);

        $value = OrderValue::builder()->create([
            'id' => (string) Str::uuid(),
        ]);

        $this->repositoryMock->shouldReceive('getById')->with($value->id)->andReturn($value);

        $this->service->endTrialBeforeExpiry($value->id);

        Event::assertDispatched(PaymentRequiredEvent::class, function (PaymentRequiredEvent $event) use ($value) {
            $this->assertEquals($value->id, $event->orderId);
            $this->assertEquals($value->sourceId, $event->sourceOrderId);
            $this->assertEquals($value->id, $event->trialGroupId);

            return true;
        });
    }

    public function testGetStoreIdsByDate(): void
    {
        Date::setTestNow();
        $cutoff = Date::now()->subWeek();
        $storeIds = ['123', '456', '789'];

        $this->repositoryMock->shouldReceive('getStoreIdsByDate')->with($cutoff)->andReturn($storeIds);

        $return = $this->service->getStoreIdsByDate($cutoff);

        $this->assertEquals($storeIds, $return);
    }

    public function testItGetsGrossSales(): void
    {
        $date = Date::now();
        $grossSales = 15000;

        $this->repositoryMock->shouldReceive('getGrossSales')->withArgs([$date, null])->andReturn($grossSales);

        $return = $this->service->getGrossSales($date);

        $this->assertEquals($grossSales, $return);
    }

    public function testItGetsTotalDiscounts(): void
    {
        $date = Date::now();
        $totalDiscounts = 15000;

        $this->repositoryMock->shouldReceive('getTotalDiscounts')->withArgs([$date, null])->andReturn($totalDiscounts);

        $return = $this->service->getTotalDiscounts($date);

        $this->assertEquals($totalDiscounts, $return);
    }

    public function testItDoesNotAddBlackcartTagsOnOrderNotFound(): void
    {
        $currentStore = Store::withoutEvents(function () {
            return Store::factory()->create();
        });
        App::context(store: $currentStore);

        Http::fake();

        $order = Order::withoutEvents(function () use ($currentStore) {
            return Order::factory()->tbybOnly(10000, 10000)
                ->netSales(10000, 10000, 0, 0)->create(['store_id' => $currentStore->id]);
        });
        $orderValue = OrderValue::from($order);
        $orderValue->id = 'non-existent-order-id';

        $this->repositoryMock->shouldReceive('getById')->with($orderValue->id)
            ->andThrow(ModelNotFoundException::class);

        $this->expectException(ModelNotFoundException::class);

        $this->service->addBlackcartTagsToOrder($orderValue);

        Http::assertNothingSent();
    }

    #[DataProvider('shopifyErrorExceptionsForMutationProvider')]
    public function testItDoesNotAddBlackcartTagsOnShopifyError(
        array $responseJson,
        int $httpStatusCode,
        string $expectedException,
    ): void {
        $currentStore = Store::withoutEvents(function () {
            return Store::factory()->create();
        });
        App::context(store: $currentStore);

        $order = Order::withoutEvents(function () use ($currentStore) {
            return Order::factory()->tbybOnly(10000, 10000)
                ->netSales(10000, 10000, 0, 0)->create(['store_id' => $currentStore->id]);
        });
        $orderValue = OrderValue::from($order);

        $this->repositoryMock->shouldReceive('getById')->with($orderValue->id)->andReturn($orderValue);

        Http::fake([
            App::context()->store->domain . '/admin/api/*/graphql.json' => Http::sequence()
                ->push($responseJson, $httpStatusCode),
        ]);
        // We catch all Shopify exceptions and throw InternalShopifyRequestException instead
        $this->expectException(InternalShopifyRequestException::class);

        $this->service->addBlackcartTagsToOrder($orderValue);

        Http::assertSent(function (Request $request) {
            // TODO: validate graphql query
            return $request->method() === 'POST' &&
                $request->hasHeader('X-Shopify-Access-Token', App::context()->store->accessToken);
        });
    }

    public function testItAddsBlackcartTagsMockShopifyOrderService(): void
    {
        $currentStore = Store::withoutEvents(function () {
            return Store::factory()->create();
        });
        App::context(store: $currentStore);

        $order = Order::withoutEvents(function () use ($currentStore) {
            return Order::factory()->tbybOnly(10000, 10000)
                ->netSales(10000, 10000, 0, 0)->create(['store_id' => $currentStore->id]);
        });
        $orderValue = OrderValue::from($order);

        $this->repositoryMock->shouldReceive('getById')->with($orderValue->id)->andReturn($orderValue);

        $shopifyOrderServiceMock = $this->mock(ShopifyOrderService::class);
        $shopifyOrderServiceMock->shouldReceive('addTags')->with($orderValue->sourceId, ['blackcart'])->once();

        $service = resolve(OrderService::class);
        $service->addBlackcartTagsToOrder($orderValue);
    }

    public function testItAddsBlackcartTagsMockShopifyResponse(): void
    {
        $currentStore = Store::withoutEvents(function () {
            return Store::factory()->create();
        });
        App::context(store: $currentStore);

        $order = Order::withoutEvents(function () use ($currentStore) {
            return Order::factory()->tbybOnly(10000, 10000)
                ->netSales(10000, 10000, 0, 0)->create(['store_id' => $currentStore->id]);
        });
        $orderValue = OrderValue::from($order);

        $this->repositoryMock->shouldReceive('getById')->with($orderValue->id)->andReturn($orderValue);

        Http::fake([
            App::context()->store->domain . '/admin/api/*/graphql.json' => Http::sequence()
                ->push($this->getShopifyAddTagsSuccessResponse()),
        ]);

        $this->service->addBlackcartTagsToOrder($orderValue);

        Http::assertSent(function (Request $request) {
            // TODO: validate graphql query
            return $request->method() === 'POST' &&
                $request->hasHeader('X-Shopify-Access-Token', App::context()->store->accessToken);
        });
    }

    public function testItDoesNotAddTagsOnOrderNotFound(): void
    {
        $currentStore = Store::withoutEvents(function () {
            return Store::factory()->create();
        });
        App::context(store: $currentStore);

        Http::fake();

        $order = Order::withoutEvents(function () use ($currentStore) {
            return Order::factory()->tbybOnly(10000, 10000)
                ->netSales(10000, 10000, 0, 0)->create(['store_id' => $currentStore->id]);
        });
        $orderValue = OrderValue::from($order);
        $orderValue->id = 'non-existent-order-id';

        $this->repositoryMock->shouldReceive('getById')->with($orderValue->id)
            ->andThrow(ModelNotFoundException::class);

        $this->expectException(ModelNotFoundException::class);

        $this->service->addTags($orderValue, ['tag1', 'tag2']);

        Http::assertNothingSent();
    }

    #[DataProvider('shopifyErrorExceptionsForMutationProvider')]
    public function testItDoesNotAddTagsOnShopifyError(
        array $responseJson,
        int $httpStatusCode,
        string $expectedException,
    ): void {
        $currentStore = Store::withoutEvents(function () {
            return Store::factory()->create();
        });
        App::context(store: $currentStore);

        $order = Order::withoutEvents(function () use ($currentStore) {
            return Order::factory()->tbybOnly(10000, 10000)
                ->netSales(10000, 10000, 0, 0)->create(['store_id' => $currentStore->id]);
        });
        $orderValue = OrderValue::from($order);

        $this->repositoryMock->shouldReceive('getById')->with($orderValue->id)->andReturn($orderValue);

        Http::fake([
            App::context()->store->domain . '/admin/api/*/graphql.json' => Http::sequence()
                ->push($responseJson, $httpStatusCode),
        ]);
        // We catch all Shopify exceptions and throw InternalShopifyRequestException instead
        $this->expectException(InternalShopifyRequestException::class);

        $this->service->addTags($orderValue, ['tag1', 'tag2']);

        Http::assertSequencesAreEmpty();
        Http::assertSent(function (Request $request) {
            // TODO: validate graphql query
            return $request->method() === 'POST' &&
                $request->hasHeader('X-Shopify-Access-Token', App::context()->store->accessToken);
        });
    }

    public function testItAddsTagsMockShopifyOrderService(): void
    {
        $currentStore = Store::withoutEvents(function () {
            return Store::factory()->create();
        });
        App::context(store: $currentStore);

        $order = Order::withoutEvents(function () use ($currentStore) {
            return Order::factory()->tbybOnly(10000, 10000)
                ->netSales(10000, 10000, 0, 0)->create(['store_id' => $currentStore->id]);
        });
        $orderValue = OrderValue::from($order);

        $this->repositoryMock->shouldReceive('getById')->with($orderValue->id)->andReturn($orderValue);

        $shopifyOrderServiceMock = $this->mock(ShopifyOrderService::class);
        $shopifyOrderServiceMock->shouldReceive('addTags')->with($orderValue->sourceId, ['tag1', 'tag2'])->once();

        $service = resolve(OrderService::class);
        $service->addTags($orderValue, ['tag1', 'tag2']);
    }

    public function testItAddsTagsMockShopifyResponse(): void
    {
        $currentStore = Store::withoutEvents(function () {
            return Store::factory()->create();
        });
        App::context(store: $currentStore);

        $order = Order::withoutEvents(function () use ($currentStore) {
            return Order::factory()->tbybOnly(10000, 10000)
                ->netSales(10000, 10000, 0, 0)->create(['store_id' => $currentStore->id]);
        });
        $orderValue = OrderValue::from($order);

        $this->repositoryMock->shouldReceive('getById')->with($orderValue->id)->andReturn($orderValue);

        Http::fake([
            App::context()->store->domain . '/admin/api/*/graphql.json' => Http::sequence()
                ->push($this->getShopifyAddTagsSuccessResponse()),
        ]);

        $this->service->addTags($orderValue, ['tag1', 'tag2']);

        Http::assertSent(function (Request $request) {
            // TODO: validate graphql query
            return $request->method() === 'POST' &&
                $request->hasHeader('X-Shopify-Access-Token', App::context()->store->accessToken);
        });
    }

    public function testItReleasesFulfillment(): void
    {
        $order = OrderValue::builder()->create(['id' => 'test-order-id']);

        $this->mock(OrderRepository::class, function (MockInterface $mock) use ($order) {
            $mock->shouldReceive('getById')->once()->with($order->id)->andReturn($order);
        });

        $this->mock(ShopifyOrderService::class, function (MockInterface $mock) use ($order) {
            $mock->shouldReceive('releaseFulfillment')->once()->with($order->sourceId);
        });

        $orderService = app(OrderService::class);
        $orderService->releaseFulfillment($order->id);
    }

    public function testItAddsOrderAdjustment(): void
    {
        $store = Store::factory()->create();
        App::context(store: $store);

        $order = Order::factory()->create(['store_id' => $store->id, 'shop_currency' => 'CAD', 'source_id' => 'gid://shopify/Order/467284042']);

        $this->mock(OrderRepository::class, function (MockInterface $mock) use ($order) {
            $mock->shouldReceive('getById')->once()->with($order->id)->andReturn(OrderValue::from($order->toArray()));
        });

        $this->mock(ShopifyOrderService::class, function (MockInterface $mock) {
            $mock->shouldReceive('addCustomLineItem')->once()->withArgs(function (string $sourceOrderId, Money $amount, string $title) {
                $this->assertEquals('gid://shopify/Order/467284042', $sourceOrderId);
                $this->assertTrue($amount->isEqualTo(Money::ofMinor(10000, 'USD')));
                $this->assertEquals(config('shopify.order_refund_adjustment.line_item_title'), $title);

                return true;
            })->andReturn([
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
            ]);
        });

        $orderService = app(OrderService::class);
        $result = $orderService->addOrderRefundAdjustment($order->id, Money::ofMinor(10000, 'USD'));
        $this->assertTrue($result);

        $this->assertDatabaseCount('orders_line_items', 1);
        $this->assertDatabaseHas('orders_line_items', [
            'order_id' => $order->id,
            'source_order_id' => 'gid://shopify/Order/467284042',
            'source_id' => 'gid://shopify/LineItem/10974668456065',
            'source_product_id' => null,
            'source_variant_id' => null,
            'product_title' => 'Blackcart TBYB Adjustment',
            'variant_title' => null,
            'thumbnail' => null,
            'quantity' => 1,
            'status' => 'internal',
            'decision_status' => 'internal',
            'trialable_id' => null,
            'trial_group_id' => null,
            'is_tbyb' => 0,
            'selling_plan_id' => null,
            'deposit_type' => null,
            'deposit_value' => null,
            'shop_currency' => 'CAD',
            'customer_currency' => 'USD',
            'price_shop_amount' => 13593,
            'price_customer_amount' => 10000,
            'total_price_shop_amount' => 13593,
            'total_price_customer_amount' => 10000,
            'discount_shop_amount' => 0,
            'discount_customer_amount' => 0,
            'tax_shop_amount' => 0,
            'tax_customer_amount' => 0,
            'deposit_shop_amount' => 0,
            'deposit_customer_amount' => 0,
        ]);
    }

    public function testItFailsToAddOrderAdjustment(): void
    {
        $store = Store::withoutEvents(function () {
            return Store::factory()->create();
        });
        App::context(store: $store);

        $order = Order::factory()->create(['store_id' => $store->id, 'shop_currency' => 'CAD', 'source_id' => 'gid://shopify/Order/467284042']);

        $this->mock(OrderRepository::class, function (MockInterface $mock) use ($order) {
            $mock->shouldReceive('getById')->once()->with($order->id)->andReturn(OrderValue::from($order->toArray()));
        });

        $this->mock(ShopifyOrderService::class, function (MockInterface $mock) use ($order) {
            $mock->shouldReceive('addCustomLineItem')->once()->withArgs(function (string $sourceOrderId, Money $amount, string $title) {
                $this->assertEquals('gid://shopify/Order/467284042', $sourceOrderId);
                $this->assertTrue($amount->isEqualTo(Money::ofMinor(10000, 'USD')));
                $this->assertEquals(config('shopify.order_refund_adjustment.line_item_title'), $title);

                return true;
            })->andReturn([
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
                                ],
                            ],
                        ],
                        'userErrors' => [],
                    ],
                ],
            ]);
        });

        $orderService = app(OrderService::class);
        $result = $orderService->addOrderRefundAdjustment($order->id, Money::ofMinor(10000, 'USD'));

        $this->assertFalse($result);

        $this->assertDatabaseCount('orders_line_items', 0);
    }

    public function testItFailsToAddOrderAdjustmentOnOrderCannotBeEditedShopifyError(): void
    {
        $store = Store::withoutEvents(function () {
            return Store::factory()->create();
        });
        App::context(store: $store);

        $order = Order::factory()->create(['store_id' => $store->id, 'shop_currency' => 'CAD', 'source_id' => 'gid://shopify/Order/467284042']);

        $this->mock(OrderRepository::class, function (MockInterface $mock) use ($order) {
            $mock->shouldReceive('getById')->once()->with($order->id)->andReturn(OrderValue::from($order->toArray()));
        });

        $this->mock(ShopifyOrderService::class, function (MockInterface $mock) use ($order) {
            $mock->shouldReceive('addCustomLineItem')->once()->withArgs(function (string $sourceOrderId, Money $amount, string $title) {
                $this->assertEquals('gid://shopify/Order/467284042', $sourceOrderId);
                $this->assertTrue($amount->isEqualTo(Money::ofMinor(10000, 'USD')));
                $this->assertEquals(config('shopify.order_refund_adjustment.line_item_title'), $title);

                return true;
            })->andThrow(ShopifyOrderCannotBeEditedException::class);
        });

        $orderService = app(OrderService::class);
        $result = $orderService->addOrderRefundAdjustment($order->id, Money::ofMinor(10000, 'USD'));

        $this->assertFalse($result);

        $this->assertDatabaseCount('orders_line_items', 0);
    }

    public function testItDoesNotAddOrderAdjustmentOnZeroRefundedValue(): void
    {
        $store = Store::withoutEvents(function () {
            return Store::factory()->create();
        });
        App::context(store: $store);

        $order = Order::factory()->create(['store_id' => $store->id, 'shop_currency' => 'CAD', 'source_id' => 'gid://shopify/Order/467284042']);

        $this->mock(OrderRepository::class, function (MockInterface $mock) use ($order) {
            $mock->shouldNotReceive('getById');
        });

        $orderService = app(OrderService::class);
        $result = $orderService->addOrderRefundAdjustment($order->id, Money::ofMinor(0, 'USD'));

        $this->assertTrue($result);

        $this->assertDatabaseCount('orders_line_items', 0);
    }

    public function testItDoesNotAddOrderAdjustmentOnNegativeRefundedValue(): void
    {
        $store = Store::withoutEvents(function () {
            return Store::factory()->create();
        });
        App::context(store: $store);

        $order = Order::factory()->create(['store_id' => $store->id, 'shop_currency' => 'CAD', 'source_id' => 'gid://shopify/Order/467284042']);

        $this->mock(OrderRepository::class, function (MockInterface $mock) use ($order) {
            $mock->shouldNotReceive('getById');
        });

        $orderService = app(OrderService::class);
        $result = $orderService->addOrderRefundAdjustment($order->id, Money::ofMinor(-1000, 'USD'));

        $this->assertTrue($result);

        $this->assertDatabaseCount('orders_line_items', 0);
    }

    public function testItRefundsOrderAdjustment(): void
    {
        $store = Store::withoutEvents(function () {
            return Store::factory()->create();
        });
        App::context(store: $store);

        $order = Order::factory()->create(['store_id' => $store->id, 'shop_currency' => 'CAD', 'source_id' => 'gid://shopify/Order/467284042']);

        $this->mock(OrderRepository::class, function (MockInterface $mock) use ($order) {
            $mock->shouldReceive('getById')->once()->with($order->id)->andReturn(OrderValue::from($order->toArray()));
        });

        $this->mock(TransactionService::class, function (MockInterface $mock) use ($order) {
            $mock->shouldReceive('getLatestTransaction')->once()->with($order->id, [TransactionKind::SALE, TransactionKind::CAPTURE])->andReturn(Transaction::builder(['gateway' => 'shopify_payments'])->create());
        });

        LineItem::factory()->count(5)->sequence(
            ['status' => LineItemStatus::OPEN],
            ['status' => LineItemStatus::DELIVERED],
            ['status' => LineItemStatus::FULFILLED],
            ['status' => LineItemStatus::IN_TRIAL],
            ['status' => LineItemStatus::ARCHIVED],
        )->create(['order_id' => $order->id]);

        $lineItem = LineItem::factory(['status' => LineItemStatus::INTERNAL, 'order_id' => $order->id])->create();

        $this->mock(ShopifyOrderService::class, function (MockInterface $mock) use ($lineItem) {
            $mock->shouldReceive('createRefund')->once()->withArgs(function (string $sourceOrderId, Money $amount, string $note, ShopifyRefundLineItemInputCollection $refundLineItemInputCollection, string $gateway) use ($lineItem) {
                $this->assertEquals('gid://shopify/Order/467284042', $sourceOrderId);
                $this->assertTrue($amount->isZero());
                $this->assertEquals(config('shopify.order_refund_adjustment.staff_note'), $note);
                $this->assertEquals([
                    [
                        'line_item_id' => $lineItem->source_id,
                        'restock_type' => ShopifyRefundLineItemRestockType::NO_RESTOCK->value,
                        'quantity' => $lineItem->quantity,
                        'location_id' => null,
                    ],
                ], $refundLineItemInputCollection->toArray());
                $this->assertEquals('shopify_payments', $gateway);

                return true;
            })->andReturnNull();
        });

        $orderService = app(OrderService::class);
        $this->trialService->shouldReceive('cancelTrial')->once();
        $orderService->refundOrderRefundAdjustments($order->id);

        $this->assertDatabaseHas('orders_line_items', [
            'id' => $lineItem->id,
            'source_id' => $lineItem->source_id,
            'quantity' => 0,
            'status' => LineItemStatus::INTERNAL_CANCELLED,
        ]);
    }

    public function testItDoesNotSendAssumedDeliveryMerchantNotificationOrderNotFound(): void
    {
        $store = Store::withoutEvents(function () {
            return Store::factory()->create();
        });
        App::context(store: $store);

        Mail::fake();

        $this->repositoryMock->shouldReceive('getById')->with('non-existent-order-id')
            ->andThrow(new ModelNotFoundException());
        $this->service->sendAssumedDeliveryMerchantNotification('non-existent-order-id');

        Mail::assertNothingSent();
    }

    public function testItSendsAssumedDeliveryMerchantNotification(): void
    {
        Date::setTestNow('2024-04-09 00:00:00');

        $store = Store::withoutEvents(function () {
            return Store::factory()->create();
        });
        App::context(store: $store);

        Http::fake([
            'http://localhost:8080/api/stores/settings' => Http::response([
                'settings' => [
                    'customerSupportEmail' => [
                        'value' => 'customersupport@merchant.com',
                    ],
                ],
            ], 200),
        ]);

        $order = Order::factory()->create([
            'store_id' => $store->id,
            'status' => OrderStatus::OPEN,
            'order_data' => [
                'email' => 'matthew+test@blackcart.com',
                'customer' => [
                    'first_name' => 'Matthew',
                ],
                'name' => '#1001',
            ],
        ]);
        $orderValue = OrderValue::from($order);
        /** @noinspection PhpFieldAssignmentTypeMismatchInspection */
        $orderValue->lineItems = LineItemValue::collection([
            LineItemValue::builder()->create([
                'order_id' => $order->id,
                'status' => LineItemStatus::OPEN,
            ]),
            LineItemValue::builder()->create([
                'order_id' => $order->id,
                'status' => LineItemStatus::OPEN,
            ]),
        ]);

        Mail::fake();

        $orderValueWithAssumedDeliveryMerchantEmailSetAt = OrderValue::from($orderValue->toArray());
        $orderValueWithAssumedDeliveryMerchantEmailSetAt->assumedDeliveryMerchantEmailSentAt = Date::now();

        $this->repositoryMock->shouldReceive('getById')->with($orderValue->id)->andReturn($orderValue);
        $this->repositoryMock->shouldReceive('update')
            ->withArgs(function (OrderValue $order) use ($orderValueWithAssumedDeliveryMerchantEmailSetAt) {
                $this->assertEquals(
                    $order->assumedDeliveryMerchantEmailSentAt,
                    $orderValueWithAssumedDeliveryMerchantEmailSetAt->assumedDeliveryMerchantEmailSentAt
                );

                return true;
            })
            ->andReturn($orderValueWithAssumedDeliveryMerchantEmailSetAt);

        $this->service->sendAssumedDeliveryMerchantNotification($order->id);

        Mail::assertSent(AssumedDeliveryMerchantReminder::class);
    }

    public function testItDoesNotSendAssumedDeliveryMerchantNotificationEmailAlreadySent(): void
    {
        Date::setTestNow('2024-04-09 00:00:00');

        $store = Store::withoutEvents(function () {
            return Store::factory()->create();
        });
        App::context(store: $store);

        $order = Order::factory()->create([
            'store_id' => $store->id,
            'status' => OrderStatus::OPEN,
            'assumed_delivery_merchant_email_sent_at' => Date::now(),
        ]);
        $orderValue = OrderValue::from($order);
        /** @noinspection PhpFieldAssignmentTypeMismatchInspection */
        $orderValue->lineItems = LineItemValue::collection([
            LineItemValue::builder()->create([
                'order_id' => $order->id,
                'status' => LineItemStatus::OPEN,
            ]),
            LineItemValue::builder()->create([
                'order_id' => $order->id,
                'status' => LineItemStatus::OPEN,
            ]),
        ]);

        Mail::fake();

        $this->repositoryMock->shouldReceive('getById')->with($orderValue->id)->andReturn($orderValue);
        $this->repositoryMock->shouldNotReceive('update');

        $this->service->sendAssumedDeliveryMerchantNotification($order->id);

        Mail::assertNothingSent();
    }

    public function testItDoesNotSendAssumedDeliveryMerchantNotificationMerchantEmailIsNull(): void
    {
        Http::fake([
            'http://localhost:8080/api/stores/settings' => Http::response([
                'settings' => [
                    'customerSupportEmail' => [
                        'value' => null,
                    ],
                ],
            ], 200),
        ]);

        $store = Store::withoutEvents(function () {
            return Store::factory()->create();
        });
        App::context(store: $store);

        $order = Order::factory()->create([
            'store_id' => $store->id,
            'status' => OrderStatus::OPEN,
        ]);
        $orderValue = OrderValue::from($order);
        /** @noinspection PhpFieldAssignmentTypeMismatchInspection */
        $orderValue->lineItems = LineItemValue::collection([
            LineItemValue::builder()->create([
                'order_id' => $order->id,
                'status' => LineItemStatus::OPEN,
            ]),
            LineItemValue::builder()->create([
                'order_id' => $order->id,
                'status' => LineItemStatus::OPEN,
            ]),
        ]);

        Mail::fake();

        $this->repositoryMock->shouldReceive('getById')->with($order->id)->andReturn($orderValue);
        $this->repositoryMock->shouldNotReceive('update');

        $this->service->sendAssumedDeliveryMerchantNotification($order->id);

        Mail::assertNothingSent();
    }

    public function testItDoesNotSendAssumedDeliveryMerchantNotificationOrderCompleted(): void
    {
        $store = Store::withoutEvents(function () {
            return Store::factory()->create();
        });
        App::context(store: $store);

        $order = Order::factory()->create([
            'store_id' => $store->id,
            'status' => OrderStatus::COMPLETED,
        ]);
        $orderValue = OrderValue::from($order);
        $orderValue->lineItems = LineItemValue::collection([
            LineItemValue::builder()->create([
                'order_id' => $order->id,
                'status' => LineItemStatus::OPEN,
            ]),
            LineItemValue::builder()->create([
                'order_id' => $order->id,
                'status' => LineItemStatus::OPEN,
            ]),
        ]);

        Mail::fake();

        $this->repositoryMock->shouldReceive('getById')->with($order->id)->andReturn($orderValue);
        $this->repositoryMock->shouldNotReceive('update');

        $this->service->sendAssumedDeliveryMerchantNotification($order->id);

        Mail::assertNothingSent();
    }

    public function testItDoesNotSendAssumedDeliveryMerchantNotificationOrderCancelled(): void
    {
        $store = Store::withoutEvents(function () {
            return Store::factory()->create();
        });
        App::context(store: $store);

        $order = Order::factory()->create([
            'store_id' => $store->id,
            'status' => OrderStatus::CANCELLED,
        ]);
        $orderValue = OrderValue::from($order);
        /** @noinspection PhpFieldAssignmentTypeMismatchInspection */
        $orderValue->lineItems = LineItemValue::collection([
            LineItemValue::builder()->create([
                'order_id' => $order->id,
                'status' => LineItemStatus::OPEN,
            ]),
            LineItemValue::builder()->create([
                'order_id' => $order->id,
                'status' => LineItemStatus::OPEN,
            ]),
        ]);

        Mail::fake();

        $this->repositoryMock->shouldReceive('getById')->with($order->id)->andReturn($orderValue);
        $this->repositoryMock->shouldNotReceive('update');

        $this->service->sendAssumedDeliveryMerchantNotification($order->id);

        Mail::assertNothingSent();
    }

    public function testItDoesNotSendAssumedDeliveryMerchantNotificationOrderInTrial(): void
    {
        $store = Store::withoutEvents(function () {
            return Store::factory()->create();
        });
        App::context(store: $store);

        $order = Order::factory()->create([
            'store_id' => $store->id,
            'status' => OrderStatus::IN_TRIAL,
        ]);
        $orderValue = OrderValue::from($order);
        /** @noinspection PhpFieldAssignmentTypeMismatchInspection */
        $orderValue->lineItems = LineItemValue::collection([
            LineItemValue::builder()->create([
                'order_id' => $order->id,
                'status' => LineItemStatus::DELIVERED,
            ]),
            LineItemValue::builder()->create([
                'order_id' => $order->id,
                'status' => LineItemStatus::DELIVERED,
            ]),
        ]);

        Mail::fake();

        $this->repositoryMock->shouldReceive('getById')->with($order->id)->andReturn($orderValue);
        $this->repositoryMock->shouldNotReceive('update');

        $this->service->sendAssumedDeliveryMerchantNotification($order->id);

        Mail::assertNothingSent();
    }

    public function testItDoesNotSendAssumedDeliveryMerchantNotificationOrderHasNoLineItems(): void
    {
        $store = Store::withoutEvents(function () {
            return Store::factory()->create();
        });
        App::context(store: $store);

        $order = Order::factory()->create([
            'store_id' => $store->id,
            'status' => OrderStatus::OPEN,
        ]);
        $orderValue = OrderValue::from($order);

        Mail::fake();

        $this->repositoryMock->shouldReceive('getById')->with($order->id)->andReturn($orderValue);
        $this->repositoryMock->shouldNotReceive('update');

        $this->service->sendAssumedDeliveryMerchantNotification($order->id);

        Mail::assertNothingSent();
    }

    public function testItDoesNotSendAssumedDeliveryMerchantNotificationOrderHasNonOpenLineItems(): void
    {
        $store = Store::withoutEvents(function () {
            return Store::factory()->create();
        });
        App::context(store: $store);

        $order = Order::factory()->create([
            'store_id' => $store->id,
            'status' => OrderStatus::OPEN,
        ]);
        $orderValue = OrderValue::from($order);
        /** @noinspection PhpFieldAssignmentTypeMismatchInspection */
        $orderValue->lineItems = LineItemValue::collection([
            LineItemValue::builder()->create([
                'order_id' => $order->id,
                'status' => LineItemStatus::OPEN,
            ]),
            LineItemValue::builder()->create([
                'order_id' => $order->id,
                'status' => LineItemStatus::DELIVERED,
            ]),
        ]);

        Mail::fake();

        $this->repositoryMock->shouldReceive('getById')->with($order->id)->andReturn($orderValue);
        $this->repositoryMock->shouldNotReceive('update');

        $this->service->sendAssumedDeliveryMerchantNotification($order->id);

        Mail::assertNothingSent();
    }

    public function testItGetsMerchantEmail(): void
    {
        Http::fake([
            'http://localhost:8080/api/stores/settings' => Http::response([
                'settings' => [
                    'customerSupportEmail' => [
                        'value' => 'customersupport@merchant.com',
                    ],
                ],
            ], 200),
        ]);

        $email = $this->service->getMerchantEmail();

        $this->assertEquals('customersupport@merchant.com', $email);
    }

    public function testItDispatchAssumeDelivered(): void
    {
        $order = Order::factory()->create([
            'id' => (string) Str::uuid(),
            'status' => OrderStatus::OPEN,
            'store_id' => $this->store->id,
        ]);
        $orderValue = OrderValue::from($order);
        $lineItemModel = LineItem::factory()->create([
            'id' => Str::uuid(),
            'order_id' => $order->id,
            'status' => LineItemStatus::OPEN,
        ]);
        $orderValue->lineItems = LineItemValue::collection([
            LineItemValue::from($lineItemModel),
        ]);
        $lineItem = $orderValue->lineItems->first();

        Event::fake();

        $this->repositoryMock->shouldReceive('getById')->with($order->id)->andReturn($orderValue);
        $this->mock(LineItemService::class, function (MockInterface $mock) use ($lineItem) {
            $mock->shouldReceive('save')->withArgs(function (LineItem $lineItem) {
                $this->assertEquals(LineItemStatus::DELIVERED, $lineItem->status);
                $this->assertEquals(LineItemStatusUpdatedBy::ASSUMED_DELIVERY, $lineItem->statusUpdatedBy);

                return true;
            })->andReturn($lineItem);
        });
        $this->service->assumeDelivered($order->id);

        Event::assertDispatched(TrialableDeliveredEvent::class);
    }

    public function testItDoesNotDispatchAssumeDeliveredOrderNotFound(): void
    {
        $orderValue = OrderValue::builder()->create();
        $orderValue->id = 'non-existent-order-id';

        Event::fake();

        $this->repositoryMock->shouldReceive('getById')->with('non-existent-order-id')
            ->andThrow(new ModelNotFoundException());
        $this->service->assumeDelivered('non-existent-order-id');

        Event::assertNotDispatched(TrialableDeliveredEvent::class);
    }

    public function testItDoesNotDispatchAssumeDeliveredOrderCancelled(): void
    {
        $order = Order::factory()->create([
            'status' => OrderStatus::CANCELLED,
        ]);
        $orderValue = OrderValue::from($order);

        Event::fake();

        $this->repositoryMock->shouldReceive('getById')->with($order->id)->andReturn($orderValue);
        $this->service->assumeDelivered($order->id);

        Event::assertNotDispatched(TrialableDeliveredEvent::class);
    }

    public function testItDoesNotDispatchAssumeDeliveredOrderInTrial(): void
    {
        $order = Order::factory()->create([
            'status' => OrderStatus::IN_TRIAL,
        ]);
        $orderValue = OrderValue::from($order);

        Event::fake();

        $this->repositoryMock->shouldReceive('getById')->with($order->id)->andReturn($orderValue);
        $this->service->assumeDelivered($order->id);

        Event::assertNotDispatched(TrialableDeliveredEvent::class);
    }

    public function testItDoesNotDispatchAssumeDeliveredOrderCompleted(): void
    {
        $order = Order::factory()->create([
            'status' => OrderStatus::COMPLETED,
        ]);
        $orderValue = OrderValue::from($order);

        Event::fake();

        $this->repositoryMock->shouldReceive('getById')->with($order->id)->andReturn($orderValue);
        $this->service->assumeDelivered($order->id);

        Event::assertNotDispatched(TrialableDeliveredEvent::class);
    }

    public function testItDoesNotDispatchAssumeDeliveredOrderHasNonOpenLineItems(): void
    {
        $order = Order::factory()->create();
        $orderValue = OrderValue::from($order);
        $orderValue->lineItems = LineItemValue::collection([
            LineItemValue::builder()->create([
                'order_id' => $order->id,
                'status' => LineItemStatus::DELIVERED,
            ]),
        ]);

        Event::fake();

        $this->repositoryMock->shouldReceive('getById')->with($order->id)->andReturn($orderValue);
        $this->service->assumeDelivered($order->id);

        Event::assertNotDispatched(TrialableDeliveredEvent::class);
    }

    public function testItGetsOrderByPaymentTermsId(): void
    {
        $order = Order::withoutEvents(function () {
            return Order::factory()->create([
                'store_id' => $this->store->id,
                'payment_terms_id' => '123456789',
            ]);
        });

        $this->repositoryMock->shouldReceive('getByPaymentTermsId')
            ->with($order->payment_terms_id)->andReturn(OrderValue::from($order->toArray()));

        $orderResult = $this->service->getByPaymentTermsId($order->payment_terms_id);

        $this->assertEquals($order->payment_terms_id, $orderResult->paymentTermsId);
    }

    public function testItCreatesSimpleOrderFromWebhookData(): void
    {
        $graphData = $this->loadFixtureData('sellingPlans.json', 'Orders');

        Mail::fake();

        Http::fake([
            'http://localhost:8080/api/stores/settings' => Http::response([
                'settings' => [
                    'returnsPortalUrl' => [
                        'name' => 'returnsPortalUrl',
                        'value' => 'http://www.returns.com',
                    ],
                ],
            ]),
            'http://localhost:8080/api/stores/programs' => Http::response([
                'programs' => [
                    [
                        'storeId' => 12345,
                        'id' => 12345,
                        'shopifySellingPlanGroupId' => 'gid://shopify/SellingPlanGroup/12345',
                        'shopifySellingPlanId' => 'gid://shopify/SellingPlan/1209630859',
                        'name' => '7-day Try Before You Buy trial',
                        'tryPeriodDays' => 7,
                        'depositType' => 'percentage',
                        'depositValue' => 10,
                        'currency' => 'CAD',
                        'dropOffDays' => 5,
                    ],
                ],
            ]),
            App::context()->store->domain . '/admin/api/2024-01/graphql.json' => Http::response($graphData),
        ]);

        $this->trialService->shouldReceive('initiateTrial')->once();

        $order = Order::factory()->create([
            'store_id' => $this->store->id,
            'order_data' => $this->simpleOrderJson,
        ]);
        $orderValue = OrderValue::from($order);

        $this->repositoryMock->shouldReceive('getBySourceId')->andThrow(new ModelNotFoundException('No query results for model [App\Domain\Orders\Models\Order]'));
        $this->repositoryMock->shouldReceive('create')->andReturn($orderValue);
        $this->repositoryMock->shouldReceive('getById')->andReturn($orderValue);
        $this->service->createOrderFromWebhook($this->simpleOrderJson);

        Mail::assertSent(OrderConfirmation::class);
    }

    public function testItCreatesComplexOrderFromWebhookData(): void
    {
        $graphData = $this->loadFixtureData('sellingPlans-complex.json', 'Orders');

        Mail::fake();

        Http::fake([
            'http://localhost:8080/api/stores/settings' => Http::response([
                'settings' => [
                    'returnsPortalUrl' => [
                        'name' => 'returnsPortalUrl',
                        'value' => 'http://www.returns.com',
                    ],
                ],
            ]),
            'http://localhost:8080/api/stores/programs' => Http::response([
                'programs' => [
                    [
                        'storeId' => $this->store->id,
                        'id' => 12345,
                        'shopifySellingPlanGroupId' => 'gid://shopify/SellingPlanGroup/12345',
                        'shopifySellingPlanId' => 'gid://shopify/SellingPlan/1209630859',
                        'name' => '7-day Try Before You Buy trial',
                        'tryPeriodDays' => 7,
                        'depositType' => 'percentage',
                        'depositValue' => 10,
                        'currency' => 'CAD',
                        'dropOffDays' => 5,
                    ],
                ],
            ]),
            App::context()->store->domain . '/admin/api/2024-01/graphql.json' => Http::response($graphData),
        ]);

        $this->trialService->shouldReceive('initiateTrial')->times(4);

        $order = Order::factory()->create([
            'store_id' => $this->store->id,
            'order_data' => $this->complexOrderJson,
        ]);
        $orderValue = OrderValue::from($order);

        $this->repositoryMock->shouldReceive('getBySourceId')->andThrow(new ModelNotFoundException('No query results for model [App\Domain\Orders\Models\Order]'));
        $this->repositoryMock->shouldReceive('create')->andReturn($orderValue);
        $this->repositoryMock->shouldReceive('getById')->andReturn($orderValue);
        $this->service->createOrderFromWebhook($this->complexOrderJson);

        Mail::assertSent(OrderConfirmation::class);
    }

    public function testItCreatesComplexOrderFromWebhookDataNotAllTbybItems(): void
    {
        // This represents a mixed cart with different selling plans in the same order
        // 3 of the items are Blackcart selling plan, 1 item is not Blackcart selling plan
        $graphData = $this->loadFixtureData('sellingPlans-partial-blackcart.json', 'Orders');

        Mail::fake();

        Http::fake([
            'http://localhost:8080/api/stores/settings' => Http::response([
                'settings' => [
                    'returnsPortalUrl' => [
                        'name' => 'returnsPortalUrl',
                        'value' => 'http://www.returns.com',
                    ],
                ],
            ]),
            'http://localhost:8080/api/stores/programs' => Http::response([
                'programs' => [
                    [
                        'storeId' => $this->store->id,
                        'id' => 12345,
                        'shopifySellingPlanGroupId' => 'gid://shopify/SellingPlanGroup/12345',
                        'shopifySellingPlanId' => 'gid://shopify/SellingPlan/1209630859',
                        'name' => '7-day Try Before You Buy trial',
                        'tryPeriodDays' => 7,
                        'depositType' => 'percentage',
                        'depositValue' => 10,
                        'currency' => 'CAD',
                        'dropOffDays' => 5,
                    ],
                ],
            ]),
            App::context()->store->domain . '/admin/api/2024-01/graphql.json' => Http::response($graphData),
        ]);

        $this->trialService->shouldReceive('initiateTrial')->times(4);

        $order = Order::factory()->create([
            'store_id' => $this->store->id,
            'order_data' => $this->complexOrderJson,
        ]);
        $orderValue = OrderValue::from($order);

        $this->repositoryMock->shouldReceive('getBySourceId')->andThrow(new ModelNotFoundException('No query results for model [App\Domain\Orders\Models\Order]'));
        $this->repositoryMock->shouldReceive('create')->andReturn($orderValue);
        $this->repositoryMock->shouldReceive('getById')->andReturn($orderValue);
        $this->service->createOrderFromWebhook($this->complexOrderJson);

        Mail::assertSent(OrderConfirmation::class);
    }

    public function testItDoesNotCreateOrderFromWebhookOrderAlreadyExists(): void
    {
        Mail::fake();
        Http::fake();

        $order = Order::factory()->create([
            'store_id' => $this->store->id,
            'order_data' => $this->complexOrderJson,
        ]);
        $orderValue = OrderValue::from($order);

        $this->repositoryMock->shouldReceive('getBySourceId')->andReturn($orderValue);
        $this->trialService->shouldNotReceive('initiateTrial');
        $this->repositoryMock->shouldNotReceive('getById');
        $this->repositoryMock->shouldNotReceive('create');

        $this->service->createOrderFromWebhook($this->complexOrderJson);

        Http::assertNothingSent();
        Mail::assertNotSent(OrderConfirmation::class);
    }

    public function testItDoesNotCreateOrderFromWebhookNoProgram(): void
    {
        Mail::fake();

        Http::fake([
            'http://localhost:8080/api/stores/programs' => Http::response([
                'programs' => [],
            ]),
        ]);

        $this->repositoryMock->shouldReceive('getBySourceId')->andThrow(new ModelNotFoundException('No query results for model [App\Domain\Orders\Models\Order]'));
        $this->trialService->shouldNotReceive('initiateTrial');
        $this->repositoryMock->shouldNotReceive('getById');
        $this->repositoryMock->shouldNotReceive('create');

        $this->service->createOrderFromWebhook($this->complexOrderJson);

        Mail::assertNotSent(OrderConfirmation::class);
    }

    public function testItDoesNotCreateOrderFromWebhookNoBlackcartTbybItems(): void
    {
        // This represents a mixed cart where none of the items are Blackcart's selling plan
        $graphData = $this->loadFixtureData('sellingPlans-no-blackcart.json', 'Orders');

        Mail::fake();

        Http::fake([
            'http://localhost:8080/api/stores/programs' => Http::response([
                'programs' => [
                    [
                        'storeId' => $this->store->id,
                        'id' => 12345,
                        'shopifySellingPlanGroupId' => 'gid://shopify/SellingPlanGroup/12345',
                        'shopifySellingPlanId' => 'gid://shopify/SellingPlan/1209630859',
                        'name' => '7-day Try Before You Buy trial',
                        'tryPeriodDays' => 7,
                        'depositType' => 'percentage',
                        'depositValue' => 10,
                        'currency' => 'CAD',
                        'dropOffDays' => 5,
                    ],
                ],
            ]),
            App::context()->store->domain . '/admin/api/2024-01/graphql.json' => Http::response($graphData),
        ]);

        $this->repositoryMock->shouldReceive('getBySourceId')->andThrow(new ModelNotFoundException('No query results for model [App\Domain\Orders\Models\Order]'));
        $this->trialService->shouldNotReceive('initiateTrial');
        $this->repositoryMock->shouldNotReceive('getById');
        $this->repositoryMock->shouldNotReceive('create');

        $this->service->createOrderFromWebhook($this->complexOrderJson);

        Mail::assertNotSent(OrderConfirmation::class);
    }

    public function testItCreatesComplexOrderFromWebhookDataOrderConfirmationEmailNotSentOnFeatureFlagOff(): void
    {
        $graphData = $this->loadFixtureData('sellingPlans-complex.json', 'Orders');

        Feature::fake(['shopify-perm-b-merchant-order-confirm-email']);

        Mail::fake();

        Http::fake([
            'http://localhost:8080/api/stores/programs' => Http::response([
                'programs' => [
                    [
                        'storeId' => $this->store->id,
                        'id' => 12345,
                        'shopifySellingPlanGroupId' => 'gid://shopify/SellingPlanGroup/12345',
                        'shopifySellingPlanId' => 'gid://shopify/SellingPlan/1209630859',
                        'name' => '7-day Try Before You Buy trial',
                        'tryPeriodDays' => 7,
                        'depositType' => 'percentage',
                        'depositValue' => 10,
                        'currency' => 'CAD',
                        'dropOffDays' => 5,
                    ],
                ],
            ]),
            App::context()->store->domain . '/admin/api/2024-01/graphql.json' => Http::response($graphData),
        ]);

        $this->mock(ShopifyGraphqlService::class)->shouldReceive('post')->andReturn($graphData);
        $this->trialService->shouldReceive('initiateTrial')->times(4);

        $order = Order::factory()->create([
            'store_id' => $this->store->id,
            'order_data' => $this->complexOrderJson,
        ]);
        $orderValue = OrderValue::from($order);

        $this->repositoryMock->shouldReceive('getBySourceId')->andThrow(new ModelNotFoundException('No query results for model [App\Domain\Orders\Models\Order]'));
        $this->repositoryMock->shouldReceive('create')->andReturn($orderValue);
        $this->repositoryMock->shouldReceive('getById')->andReturn($orderValue);
        $this->service->createOrderFromWebhook($this->complexOrderJson);

        Mail::assertNotSent(OrderConfirmation::class);
    }

    public function testItUpdatesOrderStatusIfLineItemStatusIsFulfilledAfterLineItemSaved(): void
    {
        $order = Order::withoutEvents(function () {
            return Order::factory()->create([
                'store_id' => $this->store->id,
                'status' => OrderStatus::OPEN,
            ]);
        });
        $orderValue = OrderValue::from($order);
        $orderValue->lineItems = LineItemValue::collection([
            LineItemValue::builder()->create([
                'order_id' => $order->id,
                'status' => LineItemStatus::FULFILLED,
            ]),
        ]);

        $updatedOrderValue = OrderValue::from($orderValue->toArray());
        $updatedOrderValue->status = OrderStatus::FULFILLMENT_PARTIALLY_FULFILLED;

        $this->repositoryMock->shouldReceive('getById')->with($order->id)->andReturn($orderValue);
        $this->repositoryMock->shouldReceive('update')
        ->withArgs(function () use ($updatedOrderValue) {
            $this->assertEquals(
                OrderStatus::FULFILLMENT_PARTIALLY_FULFILLED,
                $updatedOrderValue->status
            );

            return true;
        })
        ->andReturn($updatedOrderValue);

        $this->service->updateOrderStatusAfterLineItemSaved($order->id);
    }

    public function testItUpdatesOrderStatusIfLineItemStatusIsDeliveredAfterLineItemSaved(): void
    {
        $order = Order::withoutEvents(function () {
            return Order::factory()->create([
                'store_id' => $this->store->id,
                'status' => OrderStatus::OPEN,
            ]);
        });
        $orderValue = OrderValue::from($order);
        $orderValue->lineItems = LineItemValue::collection([
            LineItemValue::builder()->create([
                'order_id' => $order->id,
                'status' => LineItemStatus::DELIVERED,
            ]),
        ]);
        $updatedOrderValue = OrderValue::from($orderValue->toArray());
        $updatedOrderValue->status = OrderStatus::FULFILLMENT_PARTIALLY_FULFILLED;

        $this->repositoryMock->shouldReceive('getById')->with($order->id)->andReturn($orderValue);
        $this->repositoryMock->shouldReceive('update')
        ->withArgs(function () use ($updatedOrderValue) {
            $this->assertEquals(
                OrderStatus::FULFILLMENT_PARTIALLY_FULFILLED,
                $updatedOrderValue->status
            );

            return true;
        })
        ->andReturn($updatedOrderValue);

        $this->service->updateOrderStatusAfterLineItemSaved($order->id);
    }

    public function testItDoesNotUpdateOrderStatusIfOrderStatusCancelledAfterLineItemSaved(): void
    {
        $order = Order::withoutEvents(function () {
            return Order::factory()->create([
                'store_id' => $this->store->id,
                'status' => OrderStatus::CANCELLED,
            ]);
        });
        $orderValue = OrderValue::from($order);
        $orderValue->lineItems = LineItemValue::collection([
            LineItemValue::builder()->create([
                'order_id' => $order->id,
                'status' => LineItemStatus::DELIVERED,
            ]),
        ]);

        $this->repositoryMock->shouldReceive('getById')->with($order->id)->andReturn($orderValue);
        $this->repositoryMock->shouldNotReceive('update');

        $this->service->updateOrderStatusAfterLineItemSaved($order->id);

        $this->assertEquals(OrderStatus::CANCELLED, $order->refresh()->status);
    }
}
