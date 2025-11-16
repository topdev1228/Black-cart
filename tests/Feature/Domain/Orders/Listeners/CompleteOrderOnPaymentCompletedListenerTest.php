<?php
declare(strict_types=1);

namespace Tests\Feature\Domain\Orders\Listeners;

use App\Domain\Orders\Enums\LineItemDecisionStatus;
use App\Domain\Orders\Enums\OrderStatus;
use App\Domain\Orders\Events\OrderCompletedEvent;
use App\Domain\Orders\Listeners\CompleteOrderOnPaymentCompletedListener;
use App\Domain\Orders\Mail\TrialComplete;
use App\Domain\Orders\Models\LineItem;
use App\Domain\Orders\Models\Order;
use App\Domain\Orders\Values\Order as OrderValue;
use App\Domain\Orders\Values\PaymentCompleteEvent;
use App\Domain\Stores\Models\Store;
use App\Domain\Stores\Values\Store as StoreValue;
use Event;
use Feature;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Tests\Fixtures\Domains\Orders\Traits\ShopifyAddTagsResponsesTestData;
use Tests\TestCase;

class CompleteOrderOnPaymentCompletedListenerTest extends TestCase
{
    use ShopifyAddTagsResponsesTestData;

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

        $this->store = Store::withoutEvents(function () {
            return Store::factory()->create();
        });
        App::context(store: StoreValue::from($this->store));
    }

    public function testItHandles(): void
    {
        Http::fake([
            App::context()->store->domain . '/admin/api/*/graphql.json' => Http::sequence()
                ->push($this->getShopifyAddTagsSuccessResponse()),
        ]);

        Mail::fake();
        Event::fake([OrderCompletedEvent::class]);

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
                'status' => OrderStatus::IN_TRIAL,
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
                'decision_status' => LineItemDecisionStatus::RETURNED,
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

        $orderValue = OrderValue::from($order);

        $listener = resolve(CompleteOrderOnPaymentCompletedListener::class);
        $listener->handle(new PaymentCompleteEvent($orderValue->sourceId, false));

        Http::assertSent(function (Request $request) {
            return $request->method() === 'POST' &&
                $request->hasHeader('X-Shopify-Access-Token', App::context()->store->accessToken);
        });

        Event::assertNotDispatched(OrderCompletedEvent::class);

        Mail::assertSent(TrialComplete::class);
    }

    public function testItHandleButTrialCompleteEmailNotSentOnFeatureFlagOff(): void
    {
        Feature::fake(['shopify-perm-b-merchant-trial-ended-email']);

        Http::fake([
            App::context()->store->domain . '/admin/api/*/graphql.json' => Http::sequence()
                ->push($this->getShopifyAddTagsSuccessResponse()),
        ]);

        Mail::fake();
        Event::fake([OrderCompletedEvent::class]);

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
                'status' => OrderStatus::IN_TRIAL,
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
                'decision_status' => LineItemDecisionStatus::RETURNED,
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

        $orderValue = OrderValue::from($order);

        $listener = resolve(CompleteOrderOnPaymentCompletedListener::class);
        $listener->handle(new PaymentCompleteEvent($orderValue->sourceId, false));

        Http::assertSent(function (Request $request) {
            return $request->method() === 'POST' &&
                $request->hasHeader('X-Shopify-Access-Token', App::context()->store->accessToken);
        });

        Event::assertNotDispatched(OrderCompletedEvent::class);

        Mail::assertNotSent(TrialComplete::class);
    }

    public function testItHandlesWithOutstandingAmountZeroAlready(): void
    {
        Http::fake([
            App::context()->store->domain . '/admin/api/*/graphql.json' => Http::sequence()
                ->push($this->getShopifyAddTagsSuccessResponse()),
        ]);

        Mail::fake();
        Event::fake([OrderCompletedEvent::class]);

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
                'status' => OrderStatus::IN_TRIAL,
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
                'decision_status' => LineItemDecisionStatus::RETURNED,
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

        $orderValue = OrderValue::from($order);

        $listener = resolve(CompleteOrderOnPaymentCompletedListener::class);
        $listener->handle(new PaymentCompleteEvent($orderValue->sourceId, true));

        Http::assertSent(function (Request $request) {
            return $request->method() === 'POST' &&
                $request->hasHeader('X-Shopify-Access-Token', App::context()->store->accessToken);
        });

        Mail::assertSent(TrialComplete::class);
    }
}
