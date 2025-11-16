<?php
declare(strict_types=1);

namespace Tests\Unit\Domain\Orders\Services;

use App;
use App\Domain\Orders\Enums\LineItemStatus;
use App\Domain\Orders\Enums\OrderStatus;
use App\Domain\Orders\Events\PaymentRequiredEvent;
use App\Domain\Orders\Mail\OrderDelivered;
use App\Domain\Orders\Models\LineItem;
use App\Domain\Orders\Models\Order;
use App\Domain\Orders\Services\OrderService;
use App\Domain\Stores\Models\Store;
use Carbon\CarbonImmutable;
use Event;
use Feature;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Support\Facades\Http;
use Mail;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\Fixtures\Domains\Orders\Traits\ShopifyUpdatePaymentTermsResponsesTestData;
use Tests\Fixtures\Domains\Shared\Shopify\Traits\ShopifyErrorsTestData;
use Tests\TestCase;

class OrderServiceStartTrialByTrialGroupIdTest extends TestCase
{
    use ShopifyUpdatePaymentTermsResponsesTestData;
    use ShopifyErrorsTestData;

    protected Store $store;
    protected string $trialGroupId;
    protected Order $order;
    protected int $try_period_days;
    protected int $drop_off_days;

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
        App::context(store: $this->store);

        $this->trialGroupId = 'test-group-key';

        $this->order = Order::withoutEvents(function () {
            return Order::factory()->create([
                'store_id' => $this->store->id,
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
                'trial_group_id' => $this->trialGroupId,
                'is_tbyb' => true,
            ],
            [
                'product_title' => 'The Collection Snowboard: Oxygen',
                'variant_title' => 'Black',
                'source_id' => 'gid://shopify/LineItem/12878423883915',
                'trialable_id' => '55555',
                'trial_group_id' => $this->trialGroupId,
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
            ->for($this->order)
            ->create();

        CarbonImmutable::setTestNow(CarbonImmutable::create(2024, 4, 30, 12));
        $this->try_period_days = 7;
        $this->drop_off_days = 5;
    }

    public function testItDoesNotStartTrialByTrialGroupIdOnCompletedOrder(): void
    {
        Event::fake([
            PaymentRequiredEvent::class,
        ]);
        Mail::fake();

        $this->order->status = OrderStatus::COMPLETED;
        $this->order->save();

        $orderService = resolve(OrderService::class);
        $orderService->startTrialByTrialGroupId($this->trialGroupId);

        foreach ($this->order->refresh()->lineItems as $lineItem) {
            switch ($lineItem->source_id) {
                case 'gid://shopify/LineItem/12878423851147':
                    $this->assertTrue($lineItem->is_tbyb);
                    $this->assertNotEquals(LineItemStatus::IN_TRIAL, $lineItem->status);
                    break;
                case 'gid://shopify/LineItem/12878423883915':
                    $this->assertTrue($lineItem->is_tbyb);
                    $this->assertNotEquals(LineItemStatus::IN_TRIAL, $lineItem->status);
                    break;
                case 'gid://shopify/LineItem/0987654567654':
                    $this->assertFalse($lineItem->is_tbyb);
                    $this->assertNotEquals(LineItemStatus::IN_TRIAL, $lineItem->status);
                    break;
            }
        }

        $this->assertNotEquals(OrderStatus::IN_TRIAL, $this->order->status);
        $this->assertNull($this->order->trial_expires_at);

        Event::assertNotDispatched(PaymentRequiredEvent::class);
        Mail::assertNotSent(OrderDelivered::class);
    }

    #[DataProvider('shopifyErrorExceptionsForMutationProvider')]
    public function testItDoesNotStartTrialByTrialGroupIdOnUpdateShopifyPaymentScheduleDueDateError(
        array $responseJson,
        int $httpStatusCode,
        string $expectedException,
    ): void {
        $this->expectException($expectedException);

        Mail::fake();
        Http::fake([
            'http://localhost:8080/api/stores/programs' => Http::response([
                'programs' => [
                    [
                        'storeId' => 12345,
                        'id' => 12345,
                        'shopifySellingPlanGroupId' => 'gid://shopify/SellingPlanGroup/12345',
                        'shopifySellingPlanId' => 'gid://shopify/SellingPlan/1209630859',
                        'name' => '7-day Try Before You Buy trial',
                        'tryPeriodDays' => $this->try_period_days,
                        'depositType' => 'percentage',
                        'depositValue' => 10,
                        'currency' => 'CAD',
                        'dropOffDays' => $this->drop_off_days,
                    ],
                ],
            ]),
            App::context()->store->domain . '/admin/api/*/graphql.json' => Http::response(
                $responseJson,
                $httpStatusCode
            ),
        ]);

        $orderService = resolve(OrderService::class);
        $orderService->startTrialByTrialGroupId($this->trialGroupId);

        foreach ($this->order->refresh()->lineItems as $lineItem) {
            switch ($lineItem->source_id) {
                case 'gid://shopify/LineItem/12878423851147':
                    $this->assertTrue($lineItem->is_tbyb);
                    $this->assertNotEquals(LineItemStatus::IN_TRIAL, $lineItem->status);
                    break;
                case 'gid://shopify/LineItem/12878423883915':
                    $this->assertTrue($lineItem->is_tbyb);
                    $this->assertNotEquals(LineItemStatus::IN_TRIAL, $lineItem->status);
                    break;
                case 'gid://shopify/LineItem/0987654567654':
                    $this->assertFalse($lineItem->is_tbyb);
                    $this->assertNotEquals(LineItemStatus::IN_TRIAL, $lineItem->status);
                    break;
            }
        }

        $this->assertNotEquals(OrderStatus::IN_TRIAL, $this->order->status);
        $this->assertNull($this->order->trial_expires_at);

        Mail::assertNotSent(OrderDelivered::class);
    }

    public function testItDoesNotStartTrialByTrialGroupIdOnUpdateShopifyPaymentScheduleDueDatePaidOrderError(): void
    {
        Event::fake([
            PaymentRequiredEvent::class,
        ]);
        Mail::fake();
        Http::fake([
            'http://localhost:8080/api/stores/programs' => Http::response([
                'programs' => [
                    [
                        'storeId' => 12345,
                        'id' => 12345,
                        'shopifySellingPlanGroupId' => 'gid://shopify/SellingPlanGroup/12345',
                        'shopifySellingPlanId' => 'gid://shopify/SellingPlan/1209630859',
                        'name' => '7-day Try Before You Buy trial',
                        'tryPeriodDays' => $this->try_period_days,
                        'depositType' => 'percentage',
                        'depositValue' => 10,
                        'currency' => 'CAD',
                        'dropOffDays' => $this->drop_off_days,
                    ],
                ],
            ]),
            App::context()->store->domain . '/admin/api/*/graphql.json' => Http::response(
                $this->getShopifyUpdatePaymentTermsOnPaidOrderErrorResponse()
            ),
        ]);

        $orderService = resolve(OrderService::class);
        $orderService->startTrialByTrialGroupId($this->trialGroupId);

        foreach ($this->order->refresh()->lineItems as $lineItem) {
            switch ($lineItem->source_id) {
                case 'gid://shopify/LineItem/12878423851147':
                    $this->assertTrue($lineItem->is_tbyb);
                    $this->assertNotEquals(LineItemStatus::IN_TRIAL, $lineItem->status);
                    break;
                case 'gid://shopify/LineItem/12878423883915':
                    $this->assertTrue($lineItem->is_tbyb);
                    $this->assertNotEquals(LineItemStatus::IN_TRIAL, $lineItem->status);
                    break;
                case 'gid://shopify/LineItem/0987654567654':
                    $this->assertFalse($lineItem->is_tbyb);
                    $this->assertNotEquals(LineItemStatus::IN_TRIAL, $lineItem->status);
                    break;
            }
        }

        $this->assertNotEquals(OrderStatus::IN_TRIAL, $this->order->status);
        $this->assertNull($this->order->trial_expires_at);

        Event::assertDispatched(PaymentRequiredEvent::class, function (PaymentRequiredEvent $event) {
            $this->assertEquals($this->order->id, $event->orderId);
            $this->assertEquals($this->order->source_id, $event->sourceOrderId);
            $this->assertEquals($this->trialGroupId, $event->trialGroupId);
            $this->assertEquals($this->order->outstanding_customer_amount, $event->amount);

            return true;
        });
        Mail::assertNotSent(OrderDelivered::class);
    }

    public function testItStartsTrialByTrialGroupId(): void
    {
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
                        'tryPeriodDays' => $this->try_period_days,
                        'depositType' => 'percentage',
                        'depositValue' => 10,
                        'currency' => 'CAD',
                        'dropOffDays' => $this->drop_off_days,
                    ],
                ],
            ]),
            App::context()->store->domain . '/admin/api/*/graphql.json' => Http::response(
                $this->getShopifyUpdatePaymentTermsSuccessResponse()
            ),
        ]);

        $orderService = resolve(OrderService::class);
        $orderService->startTrialByTrialGroupId($this->trialGroupId);

        foreach ($this->order->refresh()->lineItems as $lineItem) {
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

        $this->assertEquals(OrderStatus::IN_TRIAL, $this->order->status);
        $this->assertEquals(
            CarbonImmutable::now()->addDays($this->try_period_days + $this->drop_off_days),
            $this->order->trial_expires_at
        );

        Mail::assertSent(OrderDelivered::class);
    }

    public function testItStartsTrialByTrialGroupIdOrderDeliveredEmailNotSentOnFeatureFlagOff(): void
    {
        Feature::fake(['shopify-perm-b-merchant-trial-start-email']);

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
                        'tryPeriodDays' => $this->try_period_days,
                        'depositType' => 'percentage',
                        'depositValue' => 10,
                        'currency' => 'CAD',
                        'dropOffDays' => $this->drop_off_days,
                    ],
                ],
            ]),
            App::context()->store->domain . '/admin/api/*/graphql.json' => Http::response(
                $this->getShopifyUpdatePaymentTermsSuccessResponse()
            ),
        ]);

        $orderService = resolve(OrderService::class);
        $orderService->startTrialByTrialGroupId($this->trialGroupId);

        foreach ($this->order->refresh()->lineItems as $lineItem) {
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

        $this->assertEquals(OrderStatus::IN_TRIAL, $this->order->status);
        $this->assertEquals(
            CarbonImmutable::now()->addDays($this->try_period_days + $this->drop_off_days),
            $this->order->trial_expires_at
        );

        Mail::assertNotSent(OrderDelivered::class);
    }
}
