<?php
declare(strict_types=1);

namespace Tests\Feature\Domain\Orders\Console\Commands;

use App\Domain\Orders\Enums\LineItemStatus;
use App\Domain\Orders\Enums\OrderStatus;
use App\Domain\Orders\Events\PaymentRequiredEvent;
use App\Domain\Orders\Models\LineItem;
use App\Domain\Orders\Models\Order;
use App\Domain\Orders\Services\ShopifyOrderService;
use App\Domain\Stores\Models\Store;
use Carbon\CarbonImmutable;
use Event;
use Illuminate\Support\Facades\Http;
use Mockery\MockInterface;
use Tests\Fixtures\Domains\Orders\Traits\ShopifyUpdatePaymentTermsResponsesTestData;
use Tests\TestCase;

class SyncPaymentSchedulesDueDateTest extends TestCase
{
    use ShopifyUpdatePaymentTermsResponsesTestData;

    public function testItSyncsShopifyPaymentSchedulesDue(): void
    {
        CarbonImmutable::setTestNow(CarbonImmutable::create(2024, 4, 30, 12, 11, 10));

        $store = Store::withoutEvents(function () {
            return Store::factory()->create();
        });

        $order = Order::withoutEvents(function () use ($store) {
            return Order::factory()->create([
                'store_id' => $store->id,
                'status' => OrderStatus::IN_TRIAL,
                'trial_expires_at' => null,
                'created_at' => CarbonImmutable::create(2024, 4, 22, 12, 11, 10),
            ]);
        });

        LineItem::withoutEvents(function () use ($order) {
            LineItem::factory()->create([
                'order_id' => $order->id,
                'status' => LineItemStatus::DELIVERED,
                'updated_at' => CarbonImmutable::create(2024, 4, 25, 12, 11, 10),
            ]);
            LineItem::factory()->create([
                'order_id' => $order->id,
                'status' => LineItemStatus::DELIVERED,
                'updated_at' => CarbonImmutable::create(2024, 4, 28, 12, 11, 10),
            ]);
            LineItem::factory()->create([
                'order_id' => $order->id,
                'status' => LineItemStatus::DELIVERED,
                'updated_at' => CarbonImmutable::create(2024, 4, 30, 12, 11, 10),
            ]);
        });

        Event::fake([
            PaymentRequiredEvent::class,
        ]);

        Http::fake([
            'http://localhost:8080/api/stores/programs' => Http::response([
                'programs' => [
                    [
                        'storeId' => $store->id,
                        'id' => 12345,
                        'shopifySellingPlanGroupId' => 'gid://shopify/SellingPlanGroup/12345',
                        'shopifySellingPlanId' => 'gid://shopify/SellingPlan/1209630859',
                        'name' => '12-day Try Before You Buy trial',
                        'tryPeriodDays' => 12,
                        'depositType' => 'percentage',
                        'depositValue' => 10,
                        'currency' => 'USD',
                        'dropOffDays' => 7,
                    ],
                ],
            ]),
        ]);

        $expectedTrialExpiresAt = CarbonImmutable::create(2024, 5, 19, 12, 11, 10);

        $this->mock(ShopifyOrderService::class, function (MockInterface $mock) use ($order, $expectedTrialExpiresAt) {
            $mock->shouldReceive('updateShopifyPaymentScheduleDueDate')
                ->withArgs(
                    function ($paymentTermsId, $trialExpiresAt) use ($order, $expectedTrialExpiresAt) {
                        $this->assertEquals($order->payment_terms_id, $paymentTermsId);
                        $this->assertEquals($expectedTrialExpiresAt, $trialExpiresAt);

                        return true;
                    }
                )->andReturn($this->getShopifyUpdatePaymentTermsSuccessResponse());
        });

        $this->artisan('orders:sync-payment-schedules-due-date');

        $order->refresh();
        $this->assertEquals($expectedTrialExpiresAt, $order->trial_expires_at);

        Event::assertNotDispatched(PaymentRequiredEvent::class);
    }

    public function testItSyncsShopifyPaymentSchedulesDueProgramNotFound(): void
    {
        CarbonImmutable::setTestNow(CarbonImmutable::create(2024, 4, 30, 12, 11, 10));

        $store = Store::withoutEvents(function () {
            return Store::factory()->create();
        });

        $order = Order::withoutEvents(function () use ($store) {
            return Order::factory()->create([
                'store_id' => $store->id,
                'status' => OrderStatus::IN_TRIAL,
                'trial_expires_at' => null,
                'created_at' => CarbonImmutable::create(2024, 4, 22, 12, 11, 10),
            ]);
        });

        LineItem::withoutEvents(function () use ($order) {
            LineItem::factory()->create([
                'order_id' => $order->id,
                'status' => LineItemStatus::DELIVERED,
                'updated_at' => CarbonImmutable::create(2024, 4, 25, 12, 11, 10),
            ]);
            LineItem::factory()->create([
                'order_id' => $order->id,
                'status' => LineItemStatus::DELIVERED,
                'updated_at' => CarbonImmutable::create(2024, 4, 28, 12, 11, 10),
            ]);
            LineItem::factory()->create([
                'order_id' => $order->id,
                'status' => LineItemStatus::DELIVERED,
                'updated_at' => CarbonImmutable::create(2024, 4, 30, 12, 11, 10),
            ]);
        });

        Event::fake([
            PaymentRequiredEvent::class,
        ]);

        Http::fake([
            'http://localhost:8080/api/stores/programs' => Http::response(),
        ]);

        $expectedTrialExpiresAt = CarbonImmutable::create(2024, 5, 12, 12, 11, 10);

        $this->mock(ShopifyOrderService::class, function (MockInterface $mock) use ($order, $expectedTrialExpiresAt) {
            $mock->shouldReceive('updateShopifyPaymentScheduleDueDate')
                ->withArgs(
                    function ($paymentTermsId, $trialExpiresAt) use ($order, $expectedTrialExpiresAt) {
                        $this->assertEquals($order->payment_terms_id, $paymentTermsId);
                        $this->assertEquals($expectedTrialExpiresAt, $trialExpiresAt);

                        return true;
                    }
                )->andReturn($this->getShopifyUpdatePaymentTermsSuccessResponse());
        });

        $this->artisan('orders:sync-payment-schedules-due-date');

        $order->refresh();
        $this->assertEquals($expectedTrialExpiresAt, $order->trial_expires_at);

        Event::assertNotDispatched(PaymentRequiredEvent::class);
    }

    public function testItSyncsShopifyPaymentSchedulesDueTrialExpired(): void
    {
        $store = Store::withoutEvents(function () {
            return Store::factory()->create();
        });

        $order = Order::withoutEvents(function () use ($store) {
            return Order::factory()->create([
                'store_id' => $store->id,
                'status' => OrderStatus::IN_TRIAL,
                'trial_expires_at' => null,
                'created_at' => CarbonImmutable::create(2024, 3, 22, 12, 11, 10),
            ]);
        });

        LineItem::withoutEvents(function () use ($order) {
            LineItem::factory()->create([
                'order_id' => $order->id,
                'status' => LineItemStatus::DELIVERED,
                'updated_at' => CarbonImmutable::create(2024, 3, 25, 12, 11, 10),
            ]);
            LineItem::factory()->create([
                'order_id' => $order->id,
                'status' => LineItemStatus::DELIVERED,
                'updated_at' => CarbonImmutable::create(2024, 3, 28, 12, 11, 10),
            ]);
            LineItem::factory()->create([
                'order_id' => $order->id,
                'status' => LineItemStatus::DELIVERED,
                'updated_at' => CarbonImmutable::create(2024, 3, 30, 12, 11, 10),
            ]);
        });

        Event::fake([
            PaymentRequiredEvent::class,
        ]);

        Http::fake([
            'http://localhost:8080/api/stores/programs' => Http::response([
                'programs' => [
                    [
                        'storeId' => $store->id,
                        'id' => 12345,
                        'shopifySellingPlanGroupId' => 'gid://shopify/SellingPlanGroup/12345',
                        'shopifySellingPlanId' => 'gid://shopify/SellingPlan/1209630859',
                        'name' => '12-day Try Before You Buy trial',
                        'tryPeriodDays' => 12,
                        'depositType' => 'percentage',
                        'depositValue' => 10,
                        'currency' => 'USD',
                        'dropOffDays' => 7,
                    ],
                ],
            ]),
        ]);

        $expectedTrialExpiresAt = CarbonImmutable::create(2024, 4, 18, 12, 11, 10);

        $this->mock(ShopifyOrderService::class, function (MockInterface $mock) use ($order, $expectedTrialExpiresAt) {
            $mock->shouldReceive('updateShopifyPaymentScheduleDueDate')
                ->withArgs(
                    function ($paymentTermsId, $trialExpiresAt) use ($order, $expectedTrialExpiresAt) {
                        $this->assertEquals($order->payment_terms_id, $paymentTermsId);
                        $this->assertEquals($expectedTrialExpiresAt, $trialExpiresAt);

                        return true;
                    }
                )->andReturn($this->getShopifyUpdatePaymentTermsSuccessResponse());
        });

        $this->artisan('orders:sync-payment-schedules-due-date');

        $order->refresh();
        $this->assertEquals($expectedTrialExpiresAt, $order->trial_expires_at);

        Event::assertDispatched(PaymentRequiredEvent::class, function (PaymentRequiredEvent $event) use ($order) {
            $this->assertEquals($order->id, $event->orderId);
            $this->assertEquals($order->source_id, $event->sourceOrderId);
            $this->assertEquals($order->id, $event->trialGroupId);
            $this->assertEquals($order->outstanding_customer_amount, $event->amount);

            return true;
        });
    }
}
