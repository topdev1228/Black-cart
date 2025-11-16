<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Orders\Services;

use App\Domain\Orders\Enums\DepositType;
use App\Domain\Orders\Enums\LineItemStatus;
use App\Domain\Orders\Models\LineItem;
use App\Domain\Orders\Models\Order;
use App\Domain\Orders\Services\LineItemService;
use App\Domain\Orders\Services\TrialService;
use App\Domain\Orders\Values\LineItem as LineItemValue;
use App\Domain\Orders\Values\Order as OrderValue;
use App\Domain\Stores\Models\Store;
use Http;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Support\Facades\App;
use Mockery\MockInterface;
use PHPUnit\Framework\Attributes\DataProvider;
use PrinsFrank\Standards\Currency\CurrencyAlpha3;
use Tests\TestCase;

class LineItemServiceTest extends TestCase
{
    protected LineItemService $lineItemService;
    protected Store $store;
    protected MockInterface $trialServiceMock;

    protected function setUp(): void
    {
        parent::setUp();

        $this->store = Store::withoutEvents(function () {
            return Store::factory()->create();
        });
        App::context(store: $this->store);

        $this->trialServiceMock = $this->mock(TrialService::class);
        $this->lineItemService = resolve(LineItemService::class);
    }

    public function testThatItSyncsCollectionToOrderOnNullProgram(): void
    {
        Http::fake([
            'http://localhost:8080/api/stores/programs' => Http::sequence()
                ->push([], 500),
        ]);
        $this->trialServiceMock->shouldReceive('initiateTrial')->times(3)
            ->withSomeOfArgs(TrialService::DEFAULT_TRIAL_DAYS);

        $order = Order::withoutEvents(function () {
            return Order::factory()->create();
        });
        $orderValue = OrderValue::from($order->toArray());

        $lineItemValues = LineItemValue::collection([
            LineItemValue::builder()->create([
                'order_id' => $order->id,
                'price_shop_amount' => 1200,
                'price_customer_amount' => 1200,
            ]),
            LineItemValue::builder()->create([
                'order_id' => $order->id,
                'price_shop_amount' => 2200,
                'price_customer_amount' => 2200,
            ]),
            LineItemValue::builder()->create([
                'order_id' => $order->id,
                'price_shop_amount' => 3200,
                'price_customer_amount' => 3200,
            ]),
        ]);
        $this->lineItemService->syncCollectionToOrder($lineItemValues, $orderValue);

        foreach ($lineItemValues as $lineItemValue) {
            $expectedData = [
                'order_id' => $lineItemValue->orderId,
                'source_id' => $lineItemValue->sourceId,
                'shop_currency' => CurrencyAlpha3::US_Dollar,
                'customer_currency' => CurrencyAlpha3::US_Dollar,
                'price_shop_amount' => $lineItemValue->priceShopAmount->getMinorAmount()->toInt(),
                'price_customer_amount' => $lineItemValue->priceCustomerAmount->getMinorAmount()->toInt(),
                'is_tbyb' => true,
                'selling_plan_id' => null,
                'deposit_type' => null,
                'deposit_value' => 0,
                'deposit_shop_amount' => 0,
                'deposit_customer_amount' => 0,
            ];

            $this->assertDatabaseHas('orders_line_items', $expectedData);
        }
    }

    public function testThatItSyncsCollectionToOrderNoDepositOnMismatchedSellingPlanId(): void
    {
        Http::fake([
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
                        'currency' => 'USD',
                        'dropOffDays' => 5,
                    ],
                ],
            ]),
        ]);
        $this->trialServiceMock->shouldReceive('initiateTrial')->once()
            ->withSomeOfArgs(TrialService::DEFAULT_TRIAL_DAYS);

        $order = Order::withoutEvents(function () {
            return Order::factory()->create();
        });
        $orderValue = OrderValue::from($order->toArray());

        $lineItemValues = LineItemValue::collection([
            LineItemValue::builder()->create([
                'order_id' => $order->id,
                'shop_currency' => CurrencyAlpha3::Canadian_Dollar,
                'customer_currency' => CurrencyAlpha3::US_Dollar,
                'price_shop_amount' => 1350,
                'price_customer_amount' => 1000,
            ]),
        ]);
        $this->lineItemService->syncCollectionToOrder($lineItemValues, $orderValue);

        $this->assertNotEquals('gid://shopify/SellingPlan/1209630859', $lineItemValues->first()->sellingPlanId);
        $this->assertDatabaseHas('orders_line_items', [
            'order_id' => $order->id,
            'shop_currency' => CurrencyAlpha3::Canadian_Dollar,
            'customer_currency' => CurrencyAlpha3::US_Dollar,
            'price_shop_amount' => 1350,
            'price_customer_amount' => 1000,
            'is_tbyb' => true,
            // the properties below are not set because of mismatch of selling plan ID
            'selling_plan_id' => null,
            'deposit_type' => null,
            'deposit_value' => 0,
            'deposit_shop_amount' => 0,
            'deposit_customer_amount' => 0,
        ]);
    }

    public function testThatItSyncsCollectionToOrderOnPercentageDepositSameCurrency(): void
    {
        Http::fake([
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
                        'currency' => 'USD',
                        'dropOffDays' => 5,
                    ],
                ],
            ]),
        ]);
        $this->trialServiceMock->shouldReceive('initiateTrial')->times(3)
            ->withSomeOfArgs(TrialService::DEFAULT_TRIAL_DAYS);

        $order = Order::withoutEvents(function () {
            return Order::factory()->create();
        });
        $orderValue = OrderValue::from($order->toArray());

        $lineItemValues = LineItemValue::collection([
            LineItemValue::builder()->create([
                'order_id' => $order->id,
                'shop_currency' => CurrencyAlpha3::US_Dollar,
                'customer_currency' => CurrencyAlpha3::US_Dollar,
                'selling_plan_id' => 'gid://shopify/SellingPlan/1209630859',
                'price_shop_amount' => 1000,
                'price_customer_amount' => 1000,
            ]),
            LineItemValue::builder()->create([
                'order_id' => $order->id,
                'shop_currency' => CurrencyAlpha3::US_Dollar,
                'customer_currency' => CurrencyAlpha3::US_Dollar,
                'selling_plan_id' => 'gid://shopify/SellingPlan/1209630859',
                'price_shop_amount' => 2000,
                'price_customer_amount' => 2000,
            ]),
            LineItemValue::builder()->create([
                'order_id' => $order->id,
                'shop_currency' => CurrencyAlpha3::US_Dollar,
                'customer_currency' => CurrencyAlpha3::US_Dollar,
                'selling_plan_id' => 'gid://shopify/SellingPlan/1209630859',
                'price_shop_amount' => 3000,
                'price_customer_amount' => 3000,
            ]),
        ]);
        $this->lineItemService->syncCollectionToOrder($lineItemValues, $orderValue);

        $expectedData = [
            'order_id' => $order->id,
            'shop_currency' => CurrencyAlpha3::US_Dollar,
            'customer_currency' => CurrencyAlpha3::US_Dollar,
            'is_tbyb' => true,
            'selling_plan_id' => 'gid://shopify/SellingPlan/1209630859',
            'deposit_type' => DepositType::PERCENTAGE->value,
            'deposit_value' => 10,
        ];
        $this->assertDatabaseHas('orders_line_items', array_merge($expectedData, [
            'price_shop_amount' => 1000,
            'price_customer_amount' => 1000,
            'deposit_shop_amount' => 100,
            'deposit_customer_amount' => 100,
        ]));
        $this->assertDatabaseHas('orders_line_items', array_merge($expectedData, [
            'price_shop_amount' => 2000,
            'price_customer_amount' => 2000,
            'deposit_shop_amount' => 200,
            'deposit_customer_amount' => 200,
        ]));
        $this->assertDatabaseHas('orders_line_items', array_merge($expectedData, [
            'price_shop_amount' => 3000,
            'price_customer_amount' => 3000,
            'deposit_shop_amount' => 300,
            'deposit_customer_amount' => 300,
        ]));
    }

    public function testThatItSyncsCollectionToOrderOnPercentageDepositDifferentCurrencies(): void
    {
        Http::fake([
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
                        'currency' => 'USD',
                        'dropOffDays' => 5,
                    ],
                ],
            ]),
        ]);
        $this->trialServiceMock->shouldReceive('initiateTrial')->times(3)
            ->withSomeOfArgs(TrialService::DEFAULT_TRIAL_DAYS);

        $order = Order::withoutEvents(function () {
            return Order::factory()->create();
        });
        $orderValue = OrderValue::from($order->toArray());

        $lineItemValues = LineItemValue::collection([
            LineItemValue::builder()->create([
                'order_id' => $order->id,
                'shop_currency' => CurrencyAlpha3::Canadian_Dollar,
                'customer_currency' => CurrencyAlpha3::US_Dollar,
                'selling_plan_id' => 'gid://shopify/SellingPlan/1209630859',
                'price_shop_amount' => 1350,
                'price_customer_amount' => 1000,
            ]),
            LineItemValue::builder()->create([
                'order_id' => $order->id,
                'shop_currency' => CurrencyAlpha3::Canadian_Dollar,
                'customer_currency' => CurrencyAlpha3::US_Dollar,
                'selling_plan_id' => 'gid://shopify/SellingPlan/1209630859',
                'price_shop_amount' => 2700,
                'price_customer_amount' => 2000,
            ]),
            LineItemValue::builder()->create([
                'order_id' => $order->id,
                'shop_currency' => CurrencyAlpha3::Canadian_Dollar,
                'customer_currency' => CurrencyAlpha3::US_Dollar,
                'selling_plan_id' => 'gid://shopify/SellingPlan/1209630859',
                'price_shop_amount' => 4050,
                'price_customer_amount' => 3000,
            ]),
        ]);
        $this->lineItemService->syncCollectionToOrder($lineItemValues, $orderValue);

        $expectedData = [
            'order_id' => $order->id,
            'shop_currency' => CurrencyAlpha3::Canadian_Dollar,
            'customer_currency' => CurrencyAlpha3::US_Dollar,
            'is_tbyb' => true,
            'selling_plan_id' => 'gid://shopify/SellingPlan/1209630859',
            'deposit_type' => DepositType::PERCENTAGE->value,
            'deposit_value' => 10,
        ];
        $this->assertDatabaseHas('orders_line_items', array_merge($expectedData, [
            'price_shop_amount' => 1350,
            'price_customer_amount' => 1000,
            'deposit_shop_amount' => 135,
            'deposit_customer_amount' => 100,
        ]));
        $this->assertDatabaseHas('orders_line_items', array_merge($expectedData, [
            'price_shop_amount' => 2700,
            'price_customer_amount' => 2000,
            'deposit_shop_amount' => 270,
            'deposit_customer_amount' => 200,
        ]));
        $this->assertDatabaseHas('orders_line_items', array_merge($expectedData, [
            'price_shop_amount' => 4050,
            'price_customer_amount' => 3000,
            'deposit_shop_amount' => 405,
            'deposit_customer_amount' => 300,
        ]));
    }

    public function testThatItSyncsCollectionToOrderOnPercentageDepositDifferentCurrenciesMultipleQuantities(): void
    {
        Http::fake([
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
                        'currency' => 'USD',
                        'dropOffDays' => 5,
                    ],
                ],
            ]),
        ]);
        $this->trialServiceMock->shouldReceive('initiateTrial')->times(3)
            ->withSomeOfArgs(TrialService::DEFAULT_TRIAL_DAYS);

        $order = Order::withoutEvents(function () {
            return Order::factory()->create();
        });
        $orderValue = OrderValue::from($order->toArray());

        $lineItemValues = LineItemValue::collection([
            LineItemValue::builder()->create([
                'order_id' => $order->id,
                'shop_currency' => CurrencyAlpha3::Canadian_Dollar,
                'customer_currency' => CurrencyAlpha3::US_Dollar,
                'selling_plan_id' => 'gid://shopify/SellingPlan/1209630859',
                'price_shop_amount' => 1350,
                'price_customer_amount' => 1000,
                'quantity' => 2,
            ]),
            LineItemValue::builder()->create([
                'order_id' => $order->id,
                'shop_currency' => CurrencyAlpha3::Canadian_Dollar,
                'customer_currency' => CurrencyAlpha3::US_Dollar,
                'selling_plan_id' => 'gid://shopify/SellingPlan/1209630859',
                'price_shop_amount' => 2700,
                'price_customer_amount' => 2000,
                'quantity' => 5,
            ]),
            LineItemValue::builder()->create([
                'order_id' => $order->id,
                'shop_currency' => CurrencyAlpha3::Canadian_Dollar,
                'customer_currency' => CurrencyAlpha3::US_Dollar,
                'selling_plan_id' => 'gid://shopify/SellingPlan/1209630859',
                'price_shop_amount' => 4050,
                'price_customer_amount' => 3000,
                'quantity' => 3,
            ]),
        ]);
        $this->lineItemService->syncCollectionToOrder($lineItemValues, $orderValue);

        $expectedData = [
            'order_id' => $order->id,
            'shop_currency' => CurrencyAlpha3::Canadian_Dollar,
            'customer_currency' => CurrencyAlpha3::US_Dollar,
            'is_tbyb' => true,
            'selling_plan_id' => 'gid://shopify/SellingPlan/1209630859',
            'deposit_type' => DepositType::PERCENTAGE->value,
            'deposit_value' => 10,
        ];
        $this->assertDatabaseHas('orders_line_items', array_merge($expectedData, [
            'quantity' => 2,
            'price_shop_amount' => 1350,
            'price_customer_amount' => 1000,
            'deposit_shop_amount' => 270,
            'deposit_customer_amount' => 200,
        ]));
        $this->assertDatabaseHas('orders_line_items', array_merge($expectedData, [
            'quantity' => 5,
            'price_shop_amount' => 2700,
            'price_customer_amount' => 2000,
            'deposit_shop_amount' => 1350,
            'deposit_customer_amount' => 1000,
        ]));
        $this->assertDatabaseHas('orders_line_items', array_merge($expectedData, [
            'quantity' => 3,
            'price_shop_amount' => 4050,
            'price_customer_amount' => 3000,
            'deposit_shop_amount' => 1215,
            'deposit_customer_amount' => 900,
        ]));
    }

    public function testThatItSyncsCollectionToOrderOnFixedDepositSameCurrency(): void
    {
        Http::fake([
            'http://localhost:8080/api/stores/programs' => Http::response([
                'programs' => [
                    [
                        'storeId' => 12345,
                        'id' => 12345,
                        'shopifySellingPlanGroupId' => 'gid://shopify/SellingPlanGroup/12345',
                        'shopifySellingPlanId' => 'gid://shopify/SellingPlan/1209630859',
                        'name' => '7-day Try Before You Buy trial',
                        'tryPeriodDays' => 7,
                        'depositType' => 'fixed',
                        'depositValue' => 200,
                        'currency' => 'USD',
                        'dropOffDays' => 5,
                    ],
                ],
            ]),
        ]);
        $this->trialServiceMock->shouldReceive('initiateTrial')->times(3)
            ->withSomeOfArgs(TrialService::DEFAULT_TRIAL_DAYS);

        $order = Order::withoutEvents(function () {
            return Order::factory()->create();
        });
        $orderValue = OrderValue::from($order->toArray());

        $lineItemValues = LineItemValue::collection([
            LineItemValue::builder()->create([
                'order_id' => $order->id,
                'shop_currency' => CurrencyAlpha3::US_Dollar,
                'customer_currency' => CurrencyAlpha3::US_Dollar,
                'selling_plan_id' => 'gid://shopify/SellingPlan/1209630859',
                'price_shop_amount' => 1000,
                'price_customer_amount' => 1000,
            ]),
            LineItemValue::builder()->create([
                'order_id' => $order->id,
                'shop_currency' => CurrencyAlpha3::US_Dollar,
                'customer_currency' => CurrencyAlpha3::US_Dollar,
                'selling_plan_id' => 'gid://shopify/SellingPlan/1209630859',
                'price_shop_amount' => 2000,
                'price_customer_amount' => 2000,
            ]),
            LineItemValue::builder()->create([
                'order_id' => $order->id,
                'shop_currency' => CurrencyAlpha3::US_Dollar,
                'customer_currency' => CurrencyAlpha3::US_Dollar,
                'selling_plan_id' => 'gid://shopify/SellingPlan/1209630859',
                'price_shop_amount' => 3000,
                'price_customer_amount' => 3000,
            ]),
        ]);
        $this->lineItemService->syncCollectionToOrder($lineItemValues, $orderValue);

        $expectedData = [
            'order_id' => $order->id,
            'shop_currency' => CurrencyAlpha3::US_Dollar,
            'customer_currency' => CurrencyAlpha3::US_Dollar,
            'is_tbyb' => true,
            'selling_plan_id' => 'gid://shopify/SellingPlan/1209630859',
            'deposit_type' => DepositType::FIXED->value,
            'deposit_value' => 200,
        ];
        $this->assertDatabaseHas('orders_line_items', array_merge($expectedData, [
            'price_shop_amount' => 1000,
            'price_customer_amount' => 1000,
            'deposit_shop_amount' => 200,
            'deposit_customer_amount' => 200,
        ]));
        $this->assertDatabaseHas('orders_line_items', array_merge($expectedData, [
            'price_shop_amount' => 2000,
            'price_customer_amount' => 2000,
            'deposit_shop_amount' => 200,
            'deposit_customer_amount' => 200,
        ]));
        $this->assertDatabaseHas('orders_line_items', array_merge($expectedData, [
            'price_shop_amount' => 3000,
            'price_customer_amount' => 3000,
            'deposit_shop_amount' => 200,
            'deposit_customer_amount' => 200,
        ]));
    }

    public function testThatItSyncsCollectionToOrderOnFixedDepositDifferentCurrencies(): void
    {
        Http::fake([
            'http://localhost:8080/api/stores/programs' => Http::response([
                'programs' => [
                    [
                        'storeId' => 12345,
                        'id' => 12345,
                        'shopifySellingPlanGroupId' => 'gid://shopify/SellingPlanGroup/12345',
                        'shopifySellingPlanId' => 'gid://shopify/SellingPlan/1209630859',
                        'name' => '7-day Try Before You Buy trial',
                        'tryPeriodDays' => 7,
                        'depositType' => 'fixed',
                        'depositValue' => 200,
                        'currency' => 'USD',
                        'dropOffDays' => 5,
                    ],
                ],
            ]),
        ]);
        $this->trialServiceMock->shouldReceive('initiateTrial')->times(3)
            ->withSomeOfArgs(TrialService::DEFAULT_TRIAL_DAYS);

        $order = Order::withoutEvents(function () {
            return Order::factory()->create();
        });
        $orderValue = OrderValue::from($order->toArray());

        $lineItemValues = LineItemValue::collection([
            LineItemValue::builder()->create([
                'order_id' => $order->id,
                'shop_currency' => CurrencyAlpha3::Canadian_Dollar,
                'customer_currency' => CurrencyAlpha3::US_Dollar,
                'selling_plan_id' => 'gid://shopify/SellingPlan/1209630859',
                'price_shop_amount' => 1350,
                'price_customer_amount' => 1000,
            ]),
            LineItemValue::builder()->create([
                'order_id' => $order->id,
                'shop_currency' => CurrencyAlpha3::Canadian_Dollar,
                'customer_currency' => CurrencyAlpha3::US_Dollar,
                'selling_plan_id' => 'gid://shopify/SellingPlan/1209630859',
                'price_shop_amount' => 2700,
                'price_customer_amount' => 2000,
            ]),
            LineItemValue::builder()->create([
                'order_id' => $order->id,
                'shop_currency' => CurrencyAlpha3::Canadian_Dollar,
                'customer_currency' => CurrencyAlpha3::US_Dollar,
                'selling_plan_id' => 'gid://shopify/SellingPlan/1209630859',
                'price_shop_amount' => 4050,
                'price_customer_amount' => 3000,
            ]),
        ]);
        $this->lineItemService->syncCollectionToOrder($lineItemValues, $orderValue);

        $expectedData = [
            'order_id' => $order->id,
            'shop_currency' => CurrencyAlpha3::Canadian_Dollar,
            'customer_currency' => CurrencyAlpha3::US_Dollar,
            'is_tbyb' => true,
            'selling_plan_id' => 'gid://shopify/SellingPlan/1209630859',
            'deposit_type' => DepositType::FIXED->value,
            'deposit_value' => 200,
        ];
        $this->assertDatabaseHas('orders_line_items', array_merge($expectedData, [
            'price_shop_amount' => 1350,
            'price_customer_amount' => 1000,
            'deposit_shop_amount' => 200,
            'deposit_customer_amount' => 148,
        ]));
        $this->assertDatabaseHas('orders_line_items', array_merge($expectedData, [
            'price_shop_amount' => 2700,
            'price_customer_amount' => 2000,
            'deposit_shop_amount' => 200,
            'deposit_customer_amount' => 148,
        ]));
        $this->assertDatabaseHas('orders_line_items', array_merge($expectedData, [
            'price_shop_amount' => 4050,
            'price_customer_amount' => 3000,
            'deposit_shop_amount' => 200,
            'deposit_customer_amount' => 148,
        ]));
    }

    public function testThatItSyncsCollectionToOrderOnFixedDepositDifferentCurrenciesMultipleQuantities(): void
    {
        Http::fake([
            'http://localhost:8080/api/stores/programs' => Http::response([
                'programs' => [
                    [
                        'storeId' => 12345,
                        'id' => 12345,
                        'shopifySellingPlanGroupId' => 'gid://shopify/SellingPlanGroup/12345',
                        'shopifySellingPlanId' => 'gid://shopify/SellingPlan/1209630859',
                        'name' => '7-day Try Before You Buy trial',
                        'tryPeriodDays' => 7,
                        'depositType' => 'fixed',
                        'depositValue' => 200,
                        'currency' => 'USD',
                        'dropOffDays' => 5,
                    ],
                ],
            ]),
        ]);
        $this->trialServiceMock->shouldReceive('initiateTrial')->times(3)
            ->withSomeOfArgs(TrialService::DEFAULT_TRIAL_DAYS);

        $order = Order::withoutEvents(function () {
            return Order::factory()->create();
        });
        $orderValue = OrderValue::from($order->toArray());

        $lineItemValues = LineItemValue::collection([
            LineItemValue::builder()->create([
                'order_id' => $order->id,
                'shop_currency' => CurrencyAlpha3::Canadian_Dollar,
                'customer_currency' => CurrencyAlpha3::US_Dollar,
                'selling_plan_id' => 'gid://shopify/SellingPlan/1209630859',
                'price_shop_amount' => 1350,
                'price_customer_amount' => 1000,
                'quantity' => 1,
            ]),
            LineItemValue::builder()->create([
                'order_id' => $order->id,
                'shop_currency' => CurrencyAlpha3::Canadian_Dollar,
                'customer_currency' => CurrencyAlpha3::US_Dollar,
                'selling_plan_id' => 'gid://shopify/SellingPlan/1209630859',
                'price_shop_amount' => 2700,
                'price_customer_amount' => 2000,
                'quantity' => 4,
            ]),
            LineItemValue::builder()->create([
                'order_id' => $order->id,
                'shop_currency' => CurrencyAlpha3::Canadian_Dollar,
                'customer_currency' => CurrencyAlpha3::US_Dollar,
                'selling_plan_id' => 'gid://shopify/SellingPlan/1209630859',
                'price_shop_amount' => 4050,
                'price_customer_amount' => 3000,
                'quantity' => 6,
            ]),
        ]);
        $this->lineItemService->syncCollectionToOrder($lineItemValues, $orderValue);

        $expectedData = [
            'order_id' => $order->id,
            'shop_currency' => CurrencyAlpha3::Canadian_Dollar,
            'customer_currency' => CurrencyAlpha3::US_Dollar,
            'is_tbyb' => true,
            'selling_plan_id' => 'gid://shopify/SellingPlan/1209630859',
            'deposit_type' => DepositType::FIXED->value,
            'deposit_value' => 200,
        ];
        $this->assertDatabaseHas('orders_line_items', array_merge($expectedData, [
            'quantity' => 1,
            'price_shop_amount' => 1350,
            'price_customer_amount' => 1000,
            'deposit_shop_amount' => 200,
            'deposit_customer_amount' => 148,
        ]));
        $this->assertDatabaseHas('orders_line_items', array_merge($expectedData, [
            'quantity' => 4,
            'price_shop_amount' => 2700,
            'price_customer_amount' => 2000,
            'deposit_shop_amount' => 800,
            'deposit_customer_amount' => 592,
        ]));
        $this->assertDatabaseHas('orders_line_items', array_merge($expectedData, [
            'quantity' => 6,
            'price_shop_amount' => 4050,
            'price_customer_amount' => 3000,
            'deposit_shop_amount' => 1200,
            'deposit_customer_amount' => 888,
        ]));
    }

    public function testItGetsByStatus(): void
    {
        LineItem::factory(['order_id' => 'test-order-id'])
            ->count(count(LineItemStatus::cases()))
            ->state(
                new Sequence(
                    ...collect(LineItemStatus::cases())
                        ->map(fn (LineItemStatus $status) => ['status' => $status])
                )
            )
            ->create();

        foreach (LineItemStatus::cases() as $status) {
            $lineItems = $this->lineItemService->getByStatus('test-order-id', $status);
            $this->assertCount(1, $lineItems);
            $this->assertEquals($status, $lineItems->first()->status);
        }
    }

    #[DataProvider('getAdjustQuantityData')]
    public function testItAdjustsQuantity(int $initialQuantity, LineItemStatus $initialStatus, int $removeQuantity, int $addQuantity, int $expectedQuantity, LineItemStatus $expectedStatus)
    {
        if ($expectedStatus === LineItemStatus::CANCELLED || $expectedStatus === LineItemStatus::INTERNAL_CANCELLED) {
            $this->trialServiceMock->shouldReceive('cancelTrial')->once();
        } else {
            $this->trialServiceMock->shouldNotReceive('cancelTrial');
        }

        $lineItem = LineItem::factory(['quantity' => $initialQuantity, 'original_quantity' => $initialQuantity, 'status' => $initialStatus])->create();

        $this->lineItemService->adjustQuantity(LineItemValue::from($lineItem), $removeQuantity, $addQuantity);

        $this->assertDatabaseHas('orders_line_items', [
            'id' => $lineItem->id,
            'source_id' => $lineItem->source_id,
            'quantity' => $expectedQuantity,
            'original_quantity' => $initialQuantity,
            'status' => $expectedStatus,
        ]);
    }

    public static function getAdjustQuantityData(): array
    {
        return [
            '1 item, remove 1' => [1, LineItemStatus::OPEN, 1, 0, 0, LineItemStatus::CANCELLED],
            '1 item, add 1' => [1, LineItemStatus::OPEN, 0, 1, 2, LineItemStatus::OPEN],
            '2 items, remove 1' => [2, LineItemStatus::DELIVERED, 1, 0, 1, LineItemStatus::DELIVERED],
            '2 items, add 1' => [2, LineItemStatus::OPEN, 0, 1, 3, LineItemStatus::OPEN],
            '2 items, remove 1, add 1' => [2, LineItemStatus::OPEN, 1, 1, 2, LineItemStatus::OPEN],
            '2 items, remove 2' => [2, LineItemStatus::DELIVERED, 2, 0, 0, LineItemStatus::CANCELLED],
            '2 items, add 2' => [2, LineItemStatus::OPEN, 0, 2, 4, LineItemStatus::OPEN],
            '2 items, remove 2, add 2' => [2, LineItemStatus::OPEN, 2, 2, 2, LineItemStatus::OPEN],
            '1 internal item, remove 1' => [1, LineItemStatus::INTERNAL, 1, 0, 0, LineItemStatus::INTERNAL_CANCELLED],
            '1 internal item, add 1' => [1, LineItemStatus::INTERNAL, 0, 1, 2, LineItemStatus::INTERNAL],
            '2 internal items, remove 1' => [2, LineItemStatus::INTERNAL, 1, 0, 1, LineItemStatus::INTERNAL],
            '2 internal items, add 1' => [2, LineItemStatus::INTERNAL, 0, 1, 3, LineItemStatus::INTERNAL],
            '2 internal items, remove 1, add 1' => [2, LineItemStatus::INTERNAL, 1, 1, 2, LineItemStatus::INTERNAL],
            '2 internal items, remove 2' => [2, LineItemStatus::INTERNAL, 2, 0, 0, LineItemStatus::INTERNAL_CANCELLED],
            '2 internal items, add 2' => [2, LineItemStatus::INTERNAL, 0, 2, 4, LineItemStatus::INTERNAL],
            '2 internal items, remove 2, add 2' => [2, LineItemStatus::INTERNAL, 2, 2, 2, LineItemStatus::INTERNAL],
        ];
    }
}
