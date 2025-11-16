<?php
declare(strict_types=1);

namespace Tests\Feature\Domain\Orders\Listeners;

use App\Domain\Orders\Enums\OrderStatus;
use App\Domain\Orders\Events\OrderCreatedEvent;
use App\Domain\Orders\Listeners\WebhookOrdersCreateListener;
use App\Domain\Orders\Mail\OrderConfirmation;
use App\Domain\Orders\Models\Order;
use App\Domain\Orders\Services\LineItemService;
use App\Domain\Orders\Services\OrderService;
use App\Domain\Orders\Services\TrialService;
use App\Domain\Shared\Services\ShopifyGraphqlService;
use App\Domain\Stores\Models\Store;
use App\Domain\Stores\Values\Store as StoreValue;
use Brick\Math\RoundingMode;
use Brick\Money\Money;
use Event;
use Feature;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class WebhookOrdersCreateListenerTest extends TestCase
{
    protected $simpleOrderJson;
    protected $complexOrderJson;
    protected $orderService;
    protected $lineItemService;
    protected $shopifyGraphQl;
    protected $trialService;
    protected $currentStore;

    protected function setUp(): void
    {
        parent::setUp();

        Http::fake([
            'http://localhost:8080/api/stores/settings' => Http::response([
                'settings' => [
                    'customerSupportEmail' => [
                        'value' => 'customersupport@merchant.com',
                    ],
                    'returnsPortalUrl' => [
                        'name' => 'returnsPortalUrl',
                        'value' => 'http://www.returns.com',
                    ],
                ],
            ], 200),
        ]);

        $this->shopifyGraphQl = $this->mock(ShopifyGraphqlService::class);
        $this->trialService = $this->mock(TrialService::class);

        $this->orderService = resolve(OrderService::class);
        $this->lineItemService = resolve(LineItemService::class);

        $this->simpleOrderJson = collect($this->loadFixtureData('order.json', 'Orders'));
        $this->complexOrderJson = collect($this->loadFixtureData('order-complex.json', 'Orders'));

        $this->currentStore = StoreValue::from(Store::withoutEvents(function () {
            Store::factory()->count(5)->create();

            return Store::factory()->create();
        }));
        App::context(store: $this->currentStore);
    }

    public function testItCreatesSimpleOrderFromWebhookData(): void
    {
        Mail::fake();

        Http::fake([
            'http://localhost:8080/api/stores/settings' => Http::response([
                'settings' => [
                    'customerSupportEmail' => [
                        'value' => 'customersupport@merchant.com',
                    ],
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
        ]);

        Event::fake([
            OrderCreatedEvent::class,
        ]);

        $graphData = $this->loadFixtureData('sellingPlans.json', 'Orders');

        $this->shopifyGraphQl->shouldReceive('post')->andReturn($graphData);
        $this->trialService->shouldReceive('initiateTrial')->once();

        $listener = new WebhookOrdersCreateListener($this->orderService);
        $listener->handle($this->simpleOrderJson);

        $order = Order::first();

        $this->assertNotNull($order);
        $this->assertEquals('gid://shopify/Order/5561022087484', $order->source_id);
        $this->assertEquals(OrderStatus::OPEN, $order->status);
        $this->assertCount(1, $order->lineItems);

        // this includes shipping
        $this->assertEquals(1590, $order->total_shop_amount->getMinorAmount()->toInt());
        $this->assertEquals(100, $order->original_tbyb_gross_sales_shop_amount->getMinorAmount()->toInt());
        $this->assertEquals(0, $order->original_upfront_gross_sales_shop_amount->getMinorAmount()->toInt());
        $this->assertEquals(0, $order->original_tbyb_discounts_shop_amount->getMinorAmount()->toInt());

        Event::assertDispatched(
            OrderCreatedEvent::class,
            function (OrderCreatedEvent $event) use ($order) {
                $this->assertEquals($event->order->id, $order->id);

                return true;
            },
        );

        Mail::assertSent(OrderConfirmation::class);
    }

    public function testItCreatesComplexOrderFromWebhookData(): void
    {
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
                        'storeId' => $this->currentStore->id,
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
        ]);

        Event::fake([
            OrderCreatedEvent::class,
        ]);

        $graphData = $this->loadFixtureData('sellingPlans-complex.json', 'Orders');

        $this->shopifyGraphQl->shouldReceive('post')->andReturn($graphData);
        $this->trialService->shouldReceive('initiateTrial')->times(4);

        $listener = new WebhookOrdersCreateListener($this->orderService);
        $listener->handle($this->complexOrderJson);

        $order = Order::first();

        $this->assertNotNull($order);
        $this->assertEquals('gid://shopify/Order/5492763623680', $order->source_id);
        $this->assertEquals(OrderStatus::OPEN, $order->status);
        $this->assertCount(5, $order->lineItems);

        $this->assertEquals(600416, $order->total_net_sales_shop_amount->getMinorAmount()->toInt());
        $this->assertEquals(448920, $order->total_net_sales_customer_amount->getMinorAmount()->toInt());

        $this->assertEquals(536084, $order->tbyb_net_sales_shop_amount->getMinorAmount()->toInt());
        $this->assertEquals(400820, $order->tbyb_net_sales_customer_amount->getMinorAmount()->toInt());
        $this->assertEquals(64332, $order->upfront_net_sales_shop_amount->getMinorAmount()->toInt());
        $this->assertEquals(48100, $order->upfront_net_sales_customer_amount->getMinorAmount()->toInt());

        $this->assertEquals(528904, $order->outstanding_shop_amount->getMinorAmount()->toInt());
        $this->assertEquals(395453, $order->outstanding_customer_amount->getMinorAmount()->toInt());
        $this->assertEquals(528904, $order->outstanding_shop_amount->getMinorAmount()->toInt());
        $this->assertEquals(395453, $order->original_outstanding_customer_amount->getMinorAmount()->toInt());
        $this->assertEquals(528904, $order->original_outstanding_shop_amount->getMinorAmount()->toInt());

        foreach ($order->lineItems as $lineItem) {
            $this->assertEquals($order->id, $lineItem->order_id);
            $this->assertNotEmpty($lineItem->source_order_id);
            $this->assertStringStartsWith('gid://shopify/Order/', $lineItem->source_order_id);
            $this->assertTrue(strlen($lineItem->source_order_id) > strlen('gid://shopify/Order/'));
            $this->assertNotEmpty($lineItem->source_id);
            $this->assertStringStartsWith('gid://shopify/LineItem/', $lineItem->source_id);
            $this->assertTrue(strlen($lineItem->source_id) > strlen('gid://shopify/LineItem/'));
            $this->assertNotEmpty($lineItem->source_product_id);
            $this->assertStringStartsWith('gid://shopify/Product/', $lineItem->source_product_id);
            $this->assertTrue(strlen($lineItem->source_product_id) > strlen('gid://shopify/Product/'));
            $this->assertNotEmpty($lineItem->source_variant_id);
            $this->assertStringStartsWith('gid://shopify/Variant/', $lineItem->source_variant_id);
            $this->assertTrue(strlen($lineItem->source_variant_id) > strlen('gid://shopify/Variant/'));
            $this->assertNotEmpty($lineItem->product_title);
            $this->assertNull($lineItem->thumbnail); // will be null until we add fetch the thumbnail from Shopify

            if ($lineItem->source_id === 'gid://shopify/LineItem/13672131264768') {
                $this->assertFalse($lineItem->is_tbyb);
                $this->assertNull($lineItem->selling_plan_id);
                $this->assertNull($lineItem->deposit_type);
                $this->assertEquals(0, $lineItem->deposit_value);
                $this->assertEquals(0, $lineItem->deposit_shop_amount->getMinorAmount()->toInt());
                $this->assertEquals(0, $lineItem->deposit_customer_amount->getMinorAmount()->toInt());
                continue;
            }

            $this->assertTrue($lineItem->is_tbyb);
            $this->assertEquals('gid://shopify/SellingPlan/1209630859', $lineItem->selling_plan_id);

            foreach ($this->complexOrderJson['line_items'] as $lineItemArray) {
                if ($lineItemArray['admin_graphql_api_id'] === $lineItem->source_id) {
                    $foundLineItem = $lineItemArray;
                    break;
                }
            }

            $shopTaxAmount = collect($foundLineItem['tax_lines'])->reduce(function ($carry, $item) {
                return $carry->plus($item['price_set']['shop_money']['amount']);
            }, Money::zero($order->shop_currency->value));
            $this->assertEquals($shopTaxAmount->getMinorAmount()->toInt(), $lineItem->tax_shop_amount->getMinorAmount()->toInt());

            $customerTaxAmount = collect($foundLineItem['tax_lines'])->reduce(function ($carry, $item) {
                return $carry->plus($item['price_set']['presentment_money']['amount']);
            }, Money::zero($order->customer_currency->value));
            $this->assertEquals($customerTaxAmount->getMinorAmount()->toInt(), $lineItem->tax_customer_amount->getMinorAmount()->toInt());

            $lineItemPriceShop = $foundLineItem['price_set']['shop_money'];
            $this->assertEquals(
                $lineItem->deposit_shop_amount,
                Money::of($lineItemPriceShop['amount'], $lineItemPriceShop['currency_code'])
                    ->multipliedBy($lineItem->deposit_value / 100, RoundingMode::HALF_EVEN)
                    ->multipliedBy($lineItem->quantity)
            );

            $lineItemPriceCustomer = $foundLineItem['price_set']['presentment_money'];
            $this->assertEquals(
                $lineItem->deposit_customer_amount,
                Money::of($lineItemPriceCustomer['amount'], $lineItemPriceCustomer['currency_code'])
                    ->multipliedBy($lineItem->deposit_value / 100, RoundingMode::HALF_EVEN)
                    ->multipliedBy($lineItem->quantity)
            );
        }

        Event::assertDispatched(
            OrderCreatedEvent::class,
            function (OrderCreatedEvent $event) use ($order) {
                $this->assertEquals($event->order->id, $order->id);

                return true;
            },
        );

        Mail::assertSent(OrderConfirmation::class);
    }

    public function testItCreatesComplexOrderFromWebhookDataOrderConfirmationEmailNotSentOnFeatureFlagOff(): void
    {
        Feature::fake(['shopify-perm-b-merchant-order-confirm-email']);

        Mail::fake();

        Http::fake([
            'http://localhost:8080/api/stores/programs' => Http::response([
                'programs' => [
                    [
                        'storeId' => $this->currentStore->id,
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
        ]);

        Event::fake([
            OrderCreatedEvent::class,
        ]);

        $graphData = $this->loadFixtureData('sellingPlans-complex.json', 'Orders');

        $this->shopifyGraphQl->shouldReceive('post')->andReturn($graphData);
        $this->trialService->shouldReceive('initiateTrial')->times(4);

        $listener = new WebhookOrdersCreateListener($this->orderService);
        $listener->handle($this->complexOrderJson);

        $order = Order::first();

        $this->assertNotNull($order);
        $this->assertEquals('gid://shopify/Order/5492763623680', $order->source_id);
        $this->assertEquals(OrderStatus::OPEN, $order->status);
        $this->assertCount(5, $order->lineItems);

        $this->assertEquals(600416, $order->total_net_sales_shop_amount->getMinorAmount()->toInt());
        $this->assertEquals(448920, $order->total_net_sales_customer_amount->getMinorAmount()->toInt());

        $this->assertEquals(536084, $order->tbyb_net_sales_shop_amount->getMinorAmount()->toInt());
        $this->assertEquals(400820, $order->tbyb_net_sales_customer_amount->getMinorAmount()->toInt());
        $this->assertEquals(64332, $order->upfront_net_sales_shop_amount->getMinorAmount()->toInt());
        $this->assertEquals(48100, $order->upfront_net_sales_customer_amount->getMinorAmount()->toInt());

        $this->assertEquals(528904, $order->outstanding_shop_amount->getMinorAmount()->toInt());
        $this->assertEquals(395453, $order->outstanding_customer_amount->getMinorAmount()->toInt());
        $this->assertEquals(528904, $order->outstanding_shop_amount->getMinorAmount()->toInt());
        $this->assertEquals(395453, $order->original_outstanding_customer_amount->getMinorAmount()->toInt());
        $this->assertEquals(528904, $order->original_outstanding_shop_amount->getMinorAmount()->toInt());

        foreach ($order->lineItems as $lineItem) {
            $this->assertEquals($order->id, $lineItem->order_id);
            $this->assertNotEmpty($lineItem->source_order_id);
            $this->assertStringStartsWith('gid://shopify/Order/', $lineItem->source_order_id);
            $this->assertTrue(strlen($lineItem->source_order_id) > strlen('gid://shopify/Order/'));
            $this->assertNotEmpty($lineItem->source_id);
            $this->assertStringStartsWith('gid://shopify/LineItem/', $lineItem->source_id);
            $this->assertTrue(strlen($lineItem->source_id) > strlen('gid://shopify/LineItem/'));
            $this->assertNotEmpty($lineItem->source_product_id);
            $this->assertStringStartsWith('gid://shopify/Product/', $lineItem->source_product_id);
            $this->assertTrue(strlen($lineItem->source_product_id) > strlen('gid://shopify/Product/'));
            $this->assertNotEmpty($lineItem->source_variant_id);
            $this->assertStringStartsWith('gid://shopify/Variant/', $lineItem->source_variant_id);
            $this->assertTrue(strlen($lineItem->source_variant_id) > strlen('gid://shopify/Variant/'));
            $this->assertNotEmpty($lineItem->product_title);
            $this->assertNull($lineItem->thumbnail); // will be null until we add fetch the thumbnail from Shopify

            if ($lineItem->source_id === 'gid://shopify/LineItem/13672131264768') {
                $this->assertFalse($lineItem->is_tbyb);
                $this->assertNull($lineItem->selling_plan_id);
                $this->assertNull($lineItem->deposit_type);
                $this->assertEquals(0, $lineItem->deposit_value);
                $this->assertEquals(0, $lineItem->deposit_shop_amount->getMinorAmount()->toInt());
                $this->assertEquals(0, $lineItem->deposit_customer_amount->getMinorAmount()->toInt());
                continue;
            }

            $this->assertTrue($lineItem->is_tbyb);
            $this->assertEquals('gid://shopify/SellingPlan/1209630859', $lineItem->selling_plan_id);

            foreach ($this->complexOrderJson['line_items'] as $lineItemArray) {
                if ($lineItemArray['admin_graphql_api_id'] === $lineItem->source_id) {
                    $foundLineItem = $lineItemArray;
                    break;
                }
            }

            $shopTaxAmount = collect($foundLineItem['tax_lines'])->reduce(function ($carry, $item) {
                return $carry->plus($item['price_set']['shop_money']['amount']);
            }, Money::zero($order->shop_currency->value));
            $this->assertEquals($shopTaxAmount->getMinorAmount()->toInt(), $lineItem->tax_shop_amount->getMinorAmount()->toInt());

            $customerTaxAmount = collect($foundLineItem['tax_lines'])->reduce(function ($carry, $item) {
                return $carry->plus($item['price_set']['presentment_money']['amount']);
            }, Money::zero($order->customer_currency->value));
            $this->assertEquals($customerTaxAmount->getMinorAmount()->toInt(), $lineItem->tax_customer_amount->getMinorAmount()->toInt());

            $lineItemPriceShop = $foundLineItem['price_set']['shop_money'];
            $this->assertEquals(
                $lineItem->deposit_shop_amount,
                Money::of($lineItemPriceShop['amount'], $lineItemPriceShop['currency_code'])
                    ->multipliedBy($lineItem->deposit_value / 100, RoundingMode::HALF_EVEN)
                    ->multipliedBy($lineItem->quantity)
            );

            $lineItemPriceCustomer = $foundLineItem['price_set']['presentment_money'];
            $this->assertEquals(
                $lineItem->deposit_customer_amount,
                Money::of($lineItemPriceCustomer['amount'], $lineItemPriceCustomer['currency_code'])
                    ->multipliedBy($lineItem->deposit_value / 100, RoundingMode::HALF_EVEN)
                    ->multipliedBy($lineItem->quantity)
            );
        }

        Event::assertDispatched(
            OrderCreatedEvent::class,
            function (OrderCreatedEvent $event) use ($order) {
                $this->assertEquals($event->order->id, $order->id);

                return true;
            },
        );

        Mail::assertNotSent(OrderConfirmation::class);
    }

    public function testItDoesntCreateNonSellingPlanOrder(): void
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
                        'currency' => 'CAD',
                        'dropOffDays' => 5,
                    ],
                ],
            ]),
        ]);

        $graphData = $this->loadFixtureData('sellingPlans-no-blackcart.json', 'Orders');

        $this->shopifyGraphQl->shouldReceive('post')->andReturn($graphData);
        $this->trialService->shouldReceive('initiateTrial')->never();

        $listener = new WebhookOrdersCreateListener($this->orderService);

        $orderData = $this->loadFixtureData('order.json', 'Orders');
        $listener->handle(collect($orderData));

        $order = Order::first();
        $this->assertNull($order);
    }
}
