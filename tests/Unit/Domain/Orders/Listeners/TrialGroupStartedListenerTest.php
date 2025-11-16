<?php
declare(strict_types=1);

namespace Tests\Unit\Domain\Orders\Listeners;

use App;
use App\Domain\Orders\Enums\LineItemStatus;
use App\Domain\Orders\Enums\OrderStatus;
use App\Domain\Orders\Listeners\TrialGroupStartedListener;
use App\Domain\Orders\Mail\OrderDelivered;
use App\Domain\Orders\Models\LineItem;
use App\Domain\Orders\Models\Order;
use App\Domain\Orders\Services\OrderService;
use App\Domain\Orders\Values\TrialGroupStartedEvent as TrialGroupStartedEventValue;
use App\Domain\Stores\Models\Store;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Http;
use Mail;
use Tests\Fixtures\Domains\Orders\Traits\ShopifyUpdatePaymentTermsResponsesTestData;
use Tests\TestCase;

class TrialGroupStartedListenerTest extends TestCase
{
    use ShopifyUpdatePaymentTermsResponsesTestData;

    protected $currentStore;
    protected OrderService $service;

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

        $this->service = resolve(OrderService::class);
        $this->currentStore = Store::factory()->create();
        App::context(store: $this->currentStore);
    }

    public function testItHandlesTrialGroupStartedEvent(): void
    {
        CarbonImmutable::setTestNow(CarbonImmutable::create(2024, 4, 30, 12));
        $try_period_days = 7;
        $drop_off_days = 5;

        Event::fake();
        Mail::fake();
        Http::fake([
            'http://localhost:8080/api/stores/settings' => Http::response([
                'settings' => [
                    'returnsPortalUrl' => [
                        'name' => 'returnsPortalUrl',
                        'value' => 'http://www.google.com',
                    ],
                ],
            ], 201),
            'http://localhost:8080/api/stores/programs' => Http::response([
                'programs' => [
                    [
                        'storeId' => 12345,
                        'id' => 12345,
                        'shopifySellingPlanGroupId' => 'gid://shopify/SellingPlanGroup/12345',
                        'shopifySellingPlanId' => 'gid://shopify/SellingPlan/1209630859',
                        'name' => '7-day Try Before You Buy trial',
                        'tryPeriodDays' => $try_period_days,
                        'depositType' => 'percentage',
                        'depositValue' => 10,
                        'currency' => 'CAD',
                        'dropOffDays' => $drop_off_days,
                    ],
                ],
            ]),
        ]);
        Http::fake([
            App::context()->store->domain . '/admin/api/*/graphql.json' => Http::sequence()
                ->push($this->getShopifyUpdatePaymentTermsSuccessResponse()),
        ]);

        $groupKey = 'test-group-key';
        $orderId = 'test-order-id';
        $sourceId = 'test-source-id';

        $order = Order::withoutEvents(function () use ($orderId, $sourceId) {
            return Order::factory()->create([
                'id' => $orderId,
                'source_id' => $sourceId,
                'store_id' => $this->currentStore->id,
                'payment_terms_id' => '123456789',
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
            ]);
        });

        LineItem::factory()->count(3)->state(new Sequence(
            [
                'product_title' => 'The Collection Snowboard: Hydrogen',
                'variant_title' => 'Blue',
                'source_id' => 'gid://shopify/LineItem/12878423851147',
                'trialable_id' => '12345',
                'trial_group_id' => $groupKey,
                'is_tbyb' => true,
            ],
            [
                'product_title' => 'The Collection Snowboard: Oxygen',
                'variant_title' => 'Black',
                'source_id' => 'gid://shopify/LineItem/12878423883915',
                'trialable_id' => '55555',
                'trial_group_id' => $groupKey,
                'is_tbyb' => true,
            ],
            [
                'product_title' => 'The Collection Snowboard: Liquid',
                'variant_title' => 'Beige',
                'source_id' => 'gid://shopify/LineItem/0987654567654',
                'trialable_id' => null,
                'trial_group_id' => null,
                'is_tbyb' => false,
            ],
        ))
            ->for($order)
            ->create();

        $trialGroupExpiredListener = resolve(TrialGroupStartedListener::class);

        $trialGroupExpiredListener->handle(new TrialGroupStartedEventValue($groupKey));

        foreach ($order->refresh()->lineItems as $lineItem) {
            switch ($lineItem->source_id) {
                case 'gid://shopify/LineItem/12878423851147':
                    $this->assertTrue($lineItem->is_tbyb);
                    $this->assertEquals(LineItemStatus::IN_TRIAL, $lineItem->status);
                    break;
                case 'gid://shopify/LineItem/12878423883915':
                    $this->assertTrue($lineItem->is_tbyb);
                    $this->assertEquals(LineItemStatus::IN_TRIAL, $lineItem->status);
                    break;
                case 'gid://shopify/LineItem/0987654567654':
                    $this->assertFalse($lineItem->is_tbyb);
                    $this->assertNotEquals(LineItemStatus::IN_TRIAL, $lineItem->status);
                    break;
                default:
                    $this->assertTrue(false);
                    break;
            }
        }

        $this->assertEquals(OrderStatus::IN_TRIAL, $order->status);
        $this->assertEquals(CarbonImmutable::now()->addDays($try_period_days + $drop_off_days), $order->trial_expires_at);

        Http::assertSent(function (Request $request) {
            return $request->method() === 'POST' &&
                $request->hasHeader('X-Shopify-Access-Token', App::context()->store->accessToken);
        });

        Mail::assertSent(OrderDelivered::class);
    }
}
