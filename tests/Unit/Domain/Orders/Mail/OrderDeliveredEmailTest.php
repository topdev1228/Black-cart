<?php
declare(strict_types=1);

namespace Tests\Unit\Domain\Orders\Mail;

use App\Domain\Orders\Enums\LineItemDecisionStatus;
use App\Domain\Orders\Enums\OrderStatus;
use App\Domain\Orders\Events\OrderCreatedEvent;
use App\Domain\Orders\Listeners\WebhookOrdersCreateListener;
use App\Domain\Orders\Mail\OrderDelivered;
use App\Domain\Orders\Models\LineItem;
use App\Domain\Orders\Models\Order;
use App\Domain\Orders\Services\LineItemService;
use App\Domain\Orders\Services\OrderService;
use App\Domain\Orders\Values\Order as OrderValue;
use App\Domain\Stores\Models\Store;
use App\Domain\Stores\Values\Store as StoreValue;
use Event;
use Http;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Support\Facades\App;
use Tests\TestCase;

class OrderDeliveredEmailTest extends TestCase
{
    protected $store;
    protected $complexOrderJson;

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

        $this->orderService = resolve(OrderService::class);
        $this->lineItemService = resolve(LineItemService::class);
        $this->complexOrderJson = collect($this->loadFixtureData('order-complex.json', 'Orders'));

        $this->store = StoreValue::from(Store::withoutEvents(function () {
            Store::factory()->count(5)->create();

            return Store::factory()->create();
        }));
        App::context(store: $this->store);
    }

    public function testOrderDeliveredEmailDisplaysOrderContent(): void
    {
        $order = Order::withoutEvents(function () {
            return Order::factory()->create([
                'store_id' => $this->store->id,
                'order_data' => [
                    'line_items' => [
                        [
                            'admin_graphql_api_id' => 'gid://shopify/LineItem/12878423851147',
                        ],
                        [
                            'admin_graphql_api_id' => 'gid://shopify/LineItem/12878423883915',
                        ],
                        [
                            'admin_graphql_api_id' => 'gid://shopify/LineItem/0987654567654',
                        ],
                    ],
                    'email' => 'matthew+test@blackcart.com',
                    'customer' => [
                        'first_name' => 'Matthew',
                    ],
                    'name' => '#1001',
                ],
                'status' => OrderStatus::OPEN,
                'completed_at' => null,
            ]);
        });

        LineItem::factory()->count(3)->state(new Sequence(
            [
                'product_title' => 'The Collection Snowboard: Hydrogen',
                'variant_title' => 'Blue',
                'source_id' => 'gid://shopify/LineItem/12878423851147',
                'is_tbyb' => true,
                'decision_status' => LineItemDecisionStatus::KEPT,
                'price_customer_amount' => 51000,
            ],
            [
                'product_title' => 'The Collection Snowboard: Oxygen',
                'variant_title' => 'Black',
                'source_id' => 'gid://shopify/LineItem/12878423883915',
                'is_tbyb' => true,
                'decision_status' => LineItemDecisionStatus::KEPT,
                'price_customer_amount' => 38000,
            ],
            [
                'product_title' => 'The Collection Snowboard: Liquid',
                'variant_title' => 'Beige',
                'source_id' => 'gid://shopify/LineItem/0987654567654',
                'is_tbyb' => false,
                'decision_status' => LineItemDecisionStatus::KEPT,
                'price_customer_amount' => 45000,
            ],
        ))
        ->for($order)
        ->create();

        $orderValue = OrderValue::from($order->fresh());

        $mailable = new OrderDelivered($orderValue, 7, 'https://www.google.ca');

        $mailable->assertSeeInHtml($orderValue->orderName());
        $mailable->assertSeeInHtml($orderValue->customerFirstName());
        $mailable->assertSeeInHtml('7 day');
        $mailable->assertDontSeeInHtml('Your Blackcart Order');
    }

    protected function createOrder()
    {
        $graphData = $this->loadFixtureData('sellingPlans.json', 'Orders');

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
            $this->store->domain . '/admin/api/2024-01/graphql.json' => Http::response($graphData),
        ]);

        Event::fake([
            OrderCreatedEvent::class,
        ]);

        $listener = new WebhookOrdersCreateListener($this->orderService, $this->lineItemService);
        $listener->handle($this->complexOrderJson);
    }
}
