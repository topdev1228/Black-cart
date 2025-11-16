<?php
declare(strict_types=1);

namespace Tests\Feature\Domain\Orders\Jobs;

use App\Domain\Orders\Events\TbybNetSaleCreatedEvent;
use App\Domain\Orders\Jobs\CalculateSalesJob;
use App\Domain\Orders\Models\Order;
use App\Domain\Orders\Models\Refund;
use App\Domain\Orders\Models\TbybNetSale;
use App\Domain\Orders\Services\OrderService;
use App\Domain\Orders\Services\RefundService;
use App\Domain\Orders\Services\TbybNetSaleService;
use App\Domain\Orders\Values\Subscription;
use App\Domain\Stores\Models\Store;
use App\Domain\Stores\Services\StoreService;
use Carbon\CarbonImmutable;
use Event;
use Http;
use Illuminate\Support\Facades\App;
use Tests\TestCase;

class CalculateSalesJobTest extends TestCase
{
    protected Store $store;
    protected CalculateSalesJob $job;
    protected OrderService $orderService;
    protected RefundService $refundservice;
    protected TbybNetSaleService $tbybNetSaleService;
    protected StoreService $storeService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->store = Store::factory()->create();
        App::context(store: $this->store);

        Event::fake([TbybNetSaleCreatedEvent::class]);
        CarbonImmutable::setTestNow(CarbonImmutable::create(2024, 5, 1, 12, 0, 0));

        $this->job = new CalculateSalesJob($this->store->id);
    }

    public function testItDoesNotCalculateSalesOnInactiveSubscription(): void
    {
        Http::fake([
            'http://localhost:8080/api/stores/billings/subscriptions/active' => Http::response([
                'type' => 'request_error',
                'code' => 'resource_not_found',
                'message' => 'No active subscription found',
                'errors' => [],
            ], 404),
        ]);

        App::call([$this->job, 'handle']);

        $this->assertDatabaseEmpty('orders_tbyb_net_sales');
        Event::assertNotDispatched(TbybNetSaleCreatedEvent::class);
    }

    public function testItCalculatesSalesCorrectlyForFirstTime(): void
    {
        Order::factory()->count(2)->create([
            'store_id' => $this->store->id,
            'original_tbyb_gross_sales_shop_amount' => 12000,
            'original_total_discounts_shop_amount' => 1200,
            'created_at' => CarbonImmutable::now()->subSecond(),
        ]);
        Order::factory()->create([
            'store_id' => $this->store->id,
            'original_tbyb_gross_sales_shop_amount' => 8000,
            'original_total_discounts_shop_amount' => 800,
            'created_at' => CarbonImmutable::now()->subDay(),
        ]);
        Refund::factory()->count(2)->create([
            'store_id' => $this->store->id,
            'tbyb_gross_sales_shop_amount' => 6000,
            'tbyb_discounts_shop_amount' => 600,
            'created_at' => CarbonImmutable::now()->subSecond(),
        ]);
        Refund::factory()->create([
            'store_id' => $this->store->id,
            'tbyb_gross_sales_shop_amount' => 4000,
            'tbyb_discounts_shop_amount' => 400,
            'created_at' => CarbonImmutable::now()->subDay(),
        ]);

        $subscriptionActivatedAt = CarbonImmutable::now()->subDays(5);

        Http::fake([
            'http://localhost:8080/api/stores/billings/subscriptions/active' => Http::response([
                'subscription' => Subscription::builder()->active()->create([
                    'store_id' => $this->store->id,
                    'activated_at' => $subscriptionActivatedAt,
                    'current_period_start' => $subscriptionActivatedAt,
                    'current_period_end' => $subscriptionActivatedAt->addDays(30),
                    'trial_period_end' => $subscriptionActivatedAt->addDays(30),
                ])->toArray(),
            ]),
        ]);

        App::call([$this->job, 'handle']);

        $this->assertDatabaseHas('orders_tbyb_net_sales', [
            'store_id' => $this->store->id,
            'date_start' => $subscriptionActivatedAt->toDateTimeString(),
            'date_end' => CarbonImmutable::now()->toDateTimeString(),
            'time_range_start' => $subscriptionActivatedAt->toDateTimeString(),
            'time_range_end' => CarbonImmutable::now()->toDateTimeString(),
            'currency' => $this->store->currency,
            'tbyb_gross_sales' => 32000,
            'tbyb_discounts' => 3200,
            'tbyb_refunded_gross_sales' => 16000,
            'tbyb_refunded_discounts' => 1600,
            'tbyb_net_sales' => 14400,
            'is_first_of_billing_period' => true,
        ]);

        Event::assertDispatched(TbybNetSaleCreatedEvent::class);
    }

    public function testItCalculatesSalesCorrectlyForSecondTime(): void
    {
        Order::factory()->count(2)->create([
            'store_id' => $this->store->id,
            'original_tbyb_gross_sales_shop_amount' => 12000,
            'original_total_discounts_shop_amount' => 1200,
            'created_at' => CarbonImmutable::now()->subSecond(),
        ]);
        Order::factory()->create([
            'store_id' => $this->store->id,
            'original_tbyb_gross_sales_shop_amount' => 8000,
            'original_total_discounts_shop_amount' => 800,
            'created_at' => CarbonImmutable::now()->subDay(),
        ]);
        Refund::factory()->count(2)->create([
            'store_id' => $this->store->id,
            'tbyb_gross_sales_shop_amount' => 6000,
            'tbyb_discounts_shop_amount' => 600,
            'created_at' => CarbonImmutable::now()->subSecond(),
        ]);
        Refund::factory()->create([
            'store_id' => $this->store->id,
            'tbyb_gross_sales_shop_amount' => 4000,
            'tbyb_discounts_shop_amount' => 400,
            'created_at' => CarbonImmutable::now()->subDay(),
        ]);

        $subscriptionActivatedAt = CarbonImmutable::now()->subDays(5);

        Http::fake([
            'http://localhost:8080/api/stores/billings/subscriptions/active' => Http::response([
                'subscription' => Subscription::builder()->active()->create([
                    'store_id' => $this->store->id,
                    'activated_at' => $subscriptionActivatedAt,
                    'current_period_start' => $subscriptionActivatedAt,
                    'current_period_end' => $subscriptionActivatedAt->addDays(30),
                    'trial_period_end' => $subscriptionActivatedAt->addDays(30),
                ])->toArray(),
            ]),
        ]);

        App::call([$this->job, 'handle']);

        $this->assertDatabaseHas('orders_tbyb_net_sales', [
            'store_id' => $this->store->id,
            'date_start' => $subscriptionActivatedAt->toDateTimeString(),
            'date_end' => CarbonImmutable::now()->toDateTimeString(),
            'time_range_start' => $subscriptionActivatedAt->toDateTimeString(),
            'time_range_end' => CarbonImmutable::now()->toDateTimeString(),
            'currency' => $this->store->currency,
            'tbyb_gross_sales' => 32000,
            'tbyb_discounts' => 3200,
            'tbyb_refunded_gross_sales' => 16000,
            'tbyb_refunded_discounts' => 1600,
            'tbyb_net_sales' => 14400,
            'is_first_of_billing_period' => true,
        ]);

        $originalNetSale = TbybNetSale::first();

        CarbonImmutable::setTestNow(CarbonImmutable::now()->addDay());

        Order::factory()->create([
            'store_id' => $this->store->id,
            'original_tbyb_gross_sales_shop_amount' => 8000,
            'original_total_discounts_shop_amount' => 800,
            'created_at' => CarbonImmutable::now()->subSecond(),
        ]);

        Refund::factory()->create([
            'store_id' => $this->store->id,
            'tbyb_gross_sales_shop_amount' => 6000,
            'tbyb_discounts_shop_amount' => 600,
            'created_at' => CarbonImmutable::now()->subSecond(),
        ]);

        App::call([$this->job, 'handle']);

        $this->assertDatabaseHas('orders_tbyb_net_sales', [
            'store_id' => $this->store->id,
            'date_start' => $originalNetSale->date_end->toDateTimeString(),
            'date_end' => CarbonImmutable::now()->toDateTimeString(),
            'time_range_start' => $originalNetSale->time_range_end->toDateTimeString(),
            'time_range_end' => CarbonImmutable::now()->toDateTimeString(),
            'currency' => $this->store->currency,
            'tbyb_gross_sales' => 8000,
            'tbyb_discounts' => 800,
            'tbyb_refunded_gross_sales' => 6000,
            'tbyb_refunded_discounts' => 600,
            'tbyb_net_sales' => 1800,
            'is_first_of_billing_period' => false,
        ]);

        Event::assertDispatchedTimes(TbybNetSaleCreatedEvent::class, 2);
    }

    public function testItCalculatesSalesCorrectlyForFirstTimeWithoutRefunds(): void
    {
        Order::factory()->count(2)->create([
            'store_id' => $this->store->id,
            'original_tbyb_gross_sales_shop_amount' => 12000,
            'original_total_discounts_shop_amount' => 1200,
            'created_at' => CarbonImmutable::now()->subSecond(),
        ]);
        Order::factory()->create([
            'store_id' => $this->store->id,
            'original_tbyb_gross_sales_shop_amount' => 8000,
            'original_total_discounts_shop_amount' => 800,
            'created_at' => CarbonImmutable::now()->subDay(),
        ]);

        $subscriptionActivatedAt = CarbonImmutable::now()->subDays(5);

        Http::fake([
            'http://localhost:8080/api/stores/billings/subscriptions/active' => Http::response([
                'subscription' => Subscription::builder()->active()->create([
                    'store_id' => $this->store->id,
                    'activated_at' => $subscriptionActivatedAt,
                    'current_period_start' => $subscriptionActivatedAt,
                    'current_period_end' => $subscriptionActivatedAt->addDays(30),
                    'trial_period_end' => $subscriptionActivatedAt->addDays(30),
                ])->toArray(),
            ]),
        ]);

        App::call([$this->job, 'handle']);

        $this->assertDatabaseHas('orders_tbyb_net_sales', [
            'store_id' => $this->store->id,
            'date_start' => $subscriptionActivatedAt->toDateTimeString(),
            'date_end' => CarbonImmutable::now()->toDateTimeString(),
            'time_range_start' => $subscriptionActivatedAt->toDateTimeString(),
            'time_range_end' => CarbonImmutable::now()->toDateTimeString(),
            'currency' => $this->store->currency,
            'tbyb_gross_sales' => 32000,
            'tbyb_discounts' => 3200,
            'tbyb_refunded_gross_sales' => 0,
            'tbyb_refunded_discounts' => 0,
            'tbyb_net_sales' => 28800,
            'is_first_of_billing_period' => true,
        ]);

        Event::assertDispatched(TbybNetSaleCreatedEvent::class);
    }

    public function testItCalculatesSalesCorrectlyForSecondTimeWithoutRefunds(): void
    {
        Order::factory()->count(2)->create([
            'store_id' => $this->store->id,
            'original_tbyb_gross_sales_shop_amount' => 12000,
            'original_total_discounts_shop_amount' => 1200,
            'created_at' => CarbonImmutable::now()->subSecond(),
        ]);
        Order::factory()->create([
            'store_id' => $this->store->id,
            'original_tbyb_gross_sales_shop_amount' => 8000,
            'original_total_discounts_shop_amount' => 800,
            'created_at' => CarbonImmutable::now()->subDay(),
        ]);

        $subscriptionActivatedAt = CarbonImmutable::now()->subDays(5);

        Http::fake([
            'http://localhost:8080/api/stores/billings/subscriptions/active' => Http::response([
                'subscription' => Subscription::builder()->active()->create([
                    'store_id' => $this->store->id,
                    'activated_at' => $subscriptionActivatedAt,
                    'current_period_start' => $subscriptionActivatedAt,
                    'current_period_end' => $subscriptionActivatedAt->addDays(30),
                    'trial_period_end' => $subscriptionActivatedAt->addDays(30),
                ])->toArray(),
            ]),
        ]);

        App::call([$this->job, 'handle']);

        $this->assertDatabaseHas('orders_tbyb_net_sales', [
            'store_id' => $this->store->id,
            'date_start' => $subscriptionActivatedAt->toDateTimeString(),
            'date_end' => CarbonImmutable::now()->toDateTimeString(),
            'time_range_start' => $subscriptionActivatedAt->toDateTimeString(),
            'time_range_end' => CarbonImmutable::now()->toDateTimeString(),
            'currency' => $this->store->currency,
            'tbyb_gross_sales' => 32000,
            'tbyb_discounts' => 3200,
            'tbyb_refunded_gross_sales' => 0,
            'tbyb_refunded_discounts' => 0,
            'tbyb_net_sales' => 28800,
            'is_first_of_billing_period' => true,
        ]);

        $originalNetSale = TbybNetSale::first();

        CarbonImmutable::setTestNow(CarbonImmutable::now()->addDay());

        Order::factory()->create([
            'store_id' => $this->store->id,
            'original_tbyb_gross_sales_shop_amount' => 8000,
            'original_total_discounts_shop_amount' => 800,
            'created_at' => CarbonImmutable::now()->subSecond(),
        ]);

        App::call([$this->job, 'handle']);

        $this->assertDatabaseHas('orders_tbyb_net_sales', [
            'store_id' => $this->store->id,
            'date_start' => $originalNetSale->date_end->toDateTimeString(),
            'date_end' => CarbonImmutable::now()->toDateTimeString(),
            'time_range_start' => $originalNetSale->time_range_end->toDateTimeString(),
            'time_range_end' => CarbonImmutable::now()->toDateTimeString(),
            'currency' => $this->store->currency,
            'tbyb_gross_sales' => 8000,
            'tbyb_discounts' => 800,
            'tbyb_refunded_gross_sales' => 0,
            'tbyb_refunded_discounts' => 0,
            'tbyb_net_sales' => 7200,
            'is_first_of_billing_period' => false,
        ]);

        Event::assertDispatchedTimes(TbybNetSaleCreatedEvent::class, 2);
    }

    public function testItCalculatesSalesCorrectlyForFirstTimeWithoutOrders(): void
    {
        $subscriptionActivatedAt = CarbonImmutable::now()->subDays(5);

        Http::fake([
            'http://localhost:8080/api/stores/billings/subscriptions/active' => Http::response([
                'subscription' => Subscription::builder()->active()->create([
                    'store_id' => $this->store->id,
                    'activated_at' => $subscriptionActivatedAt,
                    'current_period_start' => $subscriptionActivatedAt,
                    'current_period_end' => $subscriptionActivatedAt->addDays(30),
                    'trial_period_end' => $subscriptionActivatedAt->addDays(30),
                ])->toArray(),
            ]),
        ]);

        App::call([$this->job, 'handle']);

        $this->assertDatabaseHas('orders_tbyb_net_sales', [
            'store_id' => $this->store->id,
            'date_start' => $subscriptionActivatedAt->toDateTimeString(),
            'date_end' => CarbonImmutable::now()->toDateTimeString(),
            'time_range_start' => $subscriptionActivatedAt->toDateTimeString(),
            'time_range_end' => CarbonImmutable::now()->toDateTimeString(),
            'currency' => $this->store->currency,
            'tbyb_gross_sales' => 0,
            'tbyb_discounts' => 0,
            'tbyb_refunded_gross_sales' => 0,
            'tbyb_refunded_discounts' => 0,
            'tbyb_net_sales' => 0,
            'is_first_of_billing_period' => true,
        ]);

        Event::assertDispatched(TbybNetSaleCreatedEvent::class);
    }

    public function testItCalculatesSalesCorrectlyForSecondTimeWithoutOrders(): void
    {
        $subscriptionActivatedAt = CarbonImmutable::now()->subDays(5);

        Http::fake([
            'http://localhost:8080/api/stores/billings/subscriptions/active' => Http::response([
                'subscription' => Subscription::builder()->active()->create([
                    'store_id' => $this->store->id,
                    'activated_at' => $subscriptionActivatedAt,
                    'current_period_start' => $subscriptionActivatedAt,
                    'current_period_end' => $subscriptionActivatedAt->addDays(30),
                    'trial_period_end' => $subscriptionActivatedAt->addDays(30),
                ])->toArray(),
            ]),
        ]);

        App::call([$this->job, 'handle']);

        $this->assertDatabaseHas('orders_tbyb_net_sales', [
            'store_id' => $this->store->id,
            'date_start' => $subscriptionActivatedAt->toDateTimeString(),
            'date_end' => CarbonImmutable::now()->toDateTimeString(),
            'time_range_start' => $subscriptionActivatedAt->toDateTimeString(),
            'time_range_end' => CarbonImmutable::now()->toDateTimeString(),
            'currency' => $this->store->currency,
            'tbyb_gross_sales' => 0,
            'tbyb_discounts' => 0,
            'tbyb_refunded_gross_sales' => 0,
            'tbyb_refunded_discounts' => 0,
            'tbyb_net_sales' => 0,
            'is_first_of_billing_period' => true,
        ]);

        $originalNetSale = TbybNetSale::first();

        CarbonImmutable::setTestNow(CarbonImmutable::now()->addDay());

        App::call([$this->job, 'handle']);

        $this->assertDatabaseHas('orders_tbyb_net_sales', [
            'store_id' => $this->store->id,
            'date_start' => $originalNetSale->date_end->toDateTimeString(),
            'date_end' => CarbonImmutable::now()->toDateTimeString(),
            'time_range_start' => $originalNetSale->time_range_end->toDateTimeString(),
            'time_range_end' => CarbonImmutable::now()->toDateTimeString(),
            'currency' => $this->store->currency,
            'tbyb_gross_sales' => 0,
            'tbyb_discounts' => 0,
            'tbyb_refunded_gross_sales' => 0,
            'tbyb_refunded_discounts' => 0,
            'tbyb_net_sales' => 0,
            'is_first_of_billing_period' => false,
        ]);

        $this->assertDatabaseCount('orders_tbyb_net_sales', 2);

        Event::assertDispatchedTimes(TbybNetSaleCreatedEvent::class, 2);
    }

    public function testItCalculatesSalesCorrectlyForFirstTimeWithoutOrdersWithRefunds(): void
    {
        Refund::factory()->count(2)->create([
            'store_id' => $this->store->id,
            'tbyb_gross_sales_shop_amount' => 6000,
            'tbyb_discounts_shop_amount' => 600,
            'created_at' => CarbonImmutable::now()->subSecond(),
        ]);

        $subscriptionActivatedAt = CarbonImmutable::now()->subDays(5);

        Http::fake([
            'http://localhost:8080/api/stores/billings/subscriptions/active' => Http::response([
                'subscription' => Subscription::builder()->active()->create([
                    'store_id' => $this->store->id,
                    'activated_at' => $subscriptionActivatedAt,
                    'current_period_start' => $subscriptionActivatedAt,
                    'current_period_end' => $subscriptionActivatedAt->addDays(30),
                    'trial_period_end' => $subscriptionActivatedAt->addDays(30),
                ])->toArray(),
            ]),
        ]);

        App::call([$this->job, 'handle']);

        $this->assertDatabaseHas('orders_tbyb_net_sales', [
            'store_id' => $this->store->id,
            'date_start' => $subscriptionActivatedAt->toDateTimeString(),
            'date_end' => CarbonImmutable::now()->toDateTimeString(),
            'time_range_start' => $subscriptionActivatedAt->toDateTimeString(),
            'time_range_end' => CarbonImmutable::now()->toDateTimeString(),
            'currency' => $this->store->currency,
            'tbyb_gross_sales' => 0,
            'tbyb_discounts' => 0,
            'tbyb_refunded_gross_sales' => 12000,
            'tbyb_refunded_discounts' => 1200,
            'tbyb_net_sales' => -10800,
            'is_first_of_billing_period' => true,
        ]);

        Event::assertDispatched(TbybNetSaleCreatedEvent::class);
    }

    public function testItCalculatesSalesCorrectlyForSecondTimeWithoutOrdersWithRefunds(): void
    {
        Refund::factory()->count(2)->create([
            'store_id' => $this->store->id,
            'tbyb_gross_sales_shop_amount' => 6000,
            'tbyb_discounts_shop_amount' => 600,
            'created_at' => CarbonImmutable::now()->subSecond(),
        ]);

        $subscriptionActivatedAt = CarbonImmutable::now()->subDays(5);

        Http::fake([
            'http://localhost:8080/api/stores/billings/subscriptions/active' => Http::response([
                'subscription' => Subscription::builder()->active()->create([
                    'store_id' => $this->store->id,
                    'activated_at' => $subscriptionActivatedAt,
                    'current_period_start' => $subscriptionActivatedAt,
                    'current_period_end' => $subscriptionActivatedAt->addDays(30),
                    'trial_period_end' => $subscriptionActivatedAt->addDays(30),
                ])->toArray(),
            ]),
        ]);

        App::call([$this->job, 'handle']);

        $this->assertDatabaseHas('orders_tbyb_net_sales', [
            'store_id' => $this->store->id,
            'date_start' => $subscriptionActivatedAt->toDateTimeString(),
            'date_end' => CarbonImmutable::now()->toDateTimeString(),
            'time_range_start' => $subscriptionActivatedAt->toDateTimeString(),
            'time_range_end' => CarbonImmutable::now()->toDateTimeString(),
            'currency' => $this->store->currency,
            'tbyb_gross_sales' => 0,
            'tbyb_discounts' => 0,
            'tbyb_refunded_gross_sales' => 12000,
            'tbyb_refunded_discounts' => 1200,
            'tbyb_net_sales' => -10800,
            'is_first_of_billing_period' => true,
        ]);

        $originalNetSale = TbybNetSale::first();

        CarbonImmutable::setTestNow(CarbonImmutable::now()->addDay());

        Refund::factory()->count(2)->create([
            'store_id' => $this->store->id,
            'tbyb_gross_sales_shop_amount' => 5000,
            'tbyb_discounts_shop_amount' => 100,
            'created_at' => CarbonImmutable::now()->subSecond(),
        ]);

        App::call([$this->job, 'handle']);

        $this->assertDatabaseHas('orders_tbyb_net_sales', [
            'store_id' => $this->store->id,
            'date_start' => $originalNetSale->date_end->toDateTimeString(),
            'date_end' => CarbonImmutable::now()->toDateTimeString(),
            'time_range_start' => $originalNetSale->time_range_end->toDateTimeString(),
            'time_range_end' => CarbonImmutable::now()->toDateTimeString(),
            'currency' => $this->store->currency,
            'tbyb_gross_sales' => 0,
            'tbyb_discounts' => 0,
            'tbyb_refunded_gross_sales' => 10000,
            'tbyb_refunded_discounts' => 200,
            'tbyb_net_sales' => -9800,
            'is_first_of_billing_period' => false,
        ]);

        Event::assertDispatchedTimes(TbybNetSaleCreatedEvent::class, 2);
    }

    public function testItCalculatesSalesCorrectlyForFirstTimeWithLargeNumbers(): void
    {
        Order::factory()->count(2)->create([
            'store_id' => $this->store->id,
            'original_tbyb_gross_sales_shop_amount' => 3000000000000,
            'original_total_discounts_shop_amount' => 500000000000,
            'created_at' => CarbonImmutable::now()->subSecond(),
        ]);
        Order::factory()->create([
            'store_id' => $this->store->id,
            'original_tbyb_gross_sales_shop_amount' => 8000,
            'original_total_discounts_shop_amount' => 800,
            'created_at' => CarbonImmutable::now()->subDay(),
        ]);
        Refund::factory()->count(2)->create([
            'store_id' => $this->store->id,
            'tbyb_gross_sales_shop_amount' => 1000000000000,
            'tbyb_discounts_shop_amount' => 500000000000,
            'created_at' => CarbonImmutable::now()->subSecond(),
        ]);
        Refund::factory()->create([
            'store_id' => $this->store->id,
            'tbyb_gross_sales_shop_amount' => 4000,
            'tbyb_discounts_shop_amount' => 400,
            'created_at' => CarbonImmutable::now()->subDay(),
        ]);

        $subscriptionActivatedAt = CarbonImmutable::now()->subDays(5);

        Http::fake([
            'http://localhost:8080/api/stores/billings/subscriptions/active' => Http::response([
                'subscription' => Subscription::builder()->active()->create([
                    'store_id' => $this->store->id,
                    'activated_at' => $subscriptionActivatedAt,
                    'current_period_start' => $subscriptionActivatedAt,
                    'current_period_end' => $subscriptionActivatedAt->addDays(30),
                    'trial_period_end' => $subscriptionActivatedAt->addDays(30),
                ])->toArray(),
            ]),
        ]);

        App::call([$this->job, 'handle']);

        $this->assertDatabaseHas('orders_tbyb_net_sales', [
            'store_id' => $this->store->id,
            'date_start' => $subscriptionActivatedAt->toDateTimeString(),
            'date_end' => CarbonImmutable::now()->toDateTimeString(),
            'time_range_start' => $subscriptionActivatedAt->toDateTimeString(),
            'time_range_end' => CarbonImmutable::now()->toDateTimeString(),
            'currency' => $this->store->currency,
            'tbyb_gross_sales' => 6000000008000,
            'tbyb_discounts' => 1000000000800,
            'tbyb_refunded_gross_sales' => 2000000004000,
            'tbyb_refunded_discounts' => 1000000000400,
            'tbyb_net_sales' => 4000000003600,
            'is_first_of_billing_period' => true,
        ]);

        Event::assertDispatched(TbybNetSaleCreatedEvent::class);
    }

    public function testItCalculatesSalesCorrectlyAcrossMulitpleBillingPeriods(): void
    {
        $subscriptionActivatedAt = CarbonImmutable::create(2024, 1, 5, 12, 0, 0, 'utc');

        // Billing period 1 orders and refunds: 2024-01-05 12:00:00pm to 2024-02-04 11:59:59
        Order::factory()->count(2)->create([
            'store_id' => $this->store->id,
            'original_tbyb_gross_sales_shop_amount' => 12000,
            'original_total_discounts_shop_amount' => 1200,
            'created_at' => CarbonImmutable::create(2024, 1, 5, 12, 0, 0, 'utc'),
        ]);
        Order::factory()->create([
            'store_id' => $this->store->id,
            'original_tbyb_gross_sales_shop_amount' => 8000,
            'original_total_discounts_shop_amount' => 800,
            'created_at' => CarbonImmutable::create(2024, 1, 18, 13, 35, 56, 'utc'),
        ]);
        Refund::factory()->count(2)->create([
            'store_id' => $this->store->id,
            'tbyb_gross_sales_shop_amount' => 6000,
            'tbyb_discounts_shop_amount' => 600,
            'created_at' => CarbonImmutable::create(2024, 1, 23, 19, 38, 50, 'utc'),
        ]);
        Refund::factory()->create([
            'store_id' => $this->store->id,
            'tbyb_gross_sales_shop_amount' => 4000,
            'tbyb_discounts_shop_amount' => 400,
            'created_at' => CarbonImmutable::create(2024, 2, 4, 11, 59, 59, 'utc'),
        ]);

        // Billing period 2 orders and refunds: 2024-02-04 12:00:00pm to 2024-03-05 11:59:59
        Order::factory()->create([
            'store_id' => $this->store->id,
            'original_tbyb_gross_sales_shop_amount' => 12000,
            'original_total_discounts_shop_amount' => 1200,
            'created_at' => CarbonImmutable::create(2024, 2, 4, 12, 0, 0, 'utc'),
        ]);
        Order::factory()->create([
            'store_id' => $this->store->id,
            'original_tbyb_gross_sales_shop_amount' => 8000,
            'original_total_discounts_shop_amount' => 800,
            'created_at' => CarbonImmutable::create(2024, 3, 5, 11, 59, 59, 'utc'),
        ]);

        // Billing period 3 orders and refunds: 2024-03-05 12:00:00pm to 2024-04-04 11:59:59
        Refund::factory()->create([
            'store_id' => $this->store->id,
            'tbyb_gross_sales_shop_amount' => 6000,
            'tbyb_discounts_shop_amount' => 600,
            'created_at' => CarbonImmutable::create(2024, 3, 5, 12, 0, 0, 'utc'),
        ]);
        Refund::factory()->create([
            'store_id' => $this->store->id,
            'tbyb_gross_sales_shop_amount' => 4000,
            'tbyb_discounts_shop_amount' => 400,
            'created_at' => CarbonImmutable::create(2024, 4, 4, 11, 59, 59, 'utc'),
        ]);

        // Billing period 4 orders and refunds: 2024-04-04 12:00:00pm to 2024-05-04 11:59:59
        Order::factory()->create([
            'store_id' => $this->store->id,
            'original_tbyb_gross_sales_shop_amount' => 12000,
            'original_total_discounts_shop_amount' => 1200,
            'created_at' => CarbonImmutable::create(2024, 4, 4, 12, 0, 0, 'utc'),
        ]);
        // this is not included as testnow is set to May 1, 2024
        Refund::factory()->create([
            'store_id' => $this->store->id,
            'tbyb_gross_sales_shop_amount' => 4000,
            'tbyb_discounts_shop_amount' => 400,
            'created_at' => CarbonImmutable::create(2024, 5, 4, 11, 59, 59, 'utc'),
        ]);

        Http::fake([
            'http://localhost:8080/api/stores/billings/subscriptions/active' => Http::response([
                'subscription' => Subscription::builder()->active()->create([
                    'store_id' => $this->store->id,
                    'activated_at' => $subscriptionActivatedAt,
                    'current_period_start' => CarbonImmutable::create(2024, 4, 4, 12, 0, 0, 'utc'),
                    'current_period_end' => CarbonImmutable::create(2024, 5, 4, 12, 0, 0, 'utc'),
                    'trial_period_end' => $subscriptionActivatedAt->addDays(30),
                ])->toArray(),
            ]),
        ]);

        App::call([$this->job, 'handle']);

        // Billing period 1 - 2024-01-05 12:00:00pm to 2024-02-04 11:59:59
        $this->assertDatabaseHas('orders_tbyb_net_sales', [
            'store_id' => $this->store->id,
            'date_start' => $subscriptionActivatedAt->toDateTimeString(),
            'date_end' => $subscriptionActivatedAt->addDays(CalculateSalesJob::SHOPIFY_MONTHLY_RECURRING_BILLING_PERIOD_DAYS)->toDateTimeString(),
            'time_range_start' => $subscriptionActivatedAt->toDateTimeString(),
            'time_range_end' => $subscriptionActivatedAt->addDays(CalculateSalesJob::SHOPIFY_MONTHLY_RECURRING_BILLING_PERIOD_DAYS)->toDateTimeString(),
            'currency' => $this->store->currency,
            'tbyb_gross_sales' => 32000,
            'tbyb_discounts' => 3200,
            'tbyb_refunded_gross_sales' => 16000,
            'tbyb_refunded_discounts' => 1600,
            'tbyb_net_sales' => 14400,
            'is_first_of_billing_period' => true,
        ]);

        // Billing period 2 - 2024-02-04 12:00:00pm to 2024-03-05 11:59:59
        $this->assertDatabaseHas('orders_tbyb_net_sales', [
            'store_id' => $this->store->id,
            'date_start' => $subscriptionActivatedAt->addDays(CalculateSalesJob::SHOPIFY_MONTHLY_RECURRING_BILLING_PERIOD_DAYS)->toDateTimeString(),
            'date_end' => $subscriptionActivatedAt->addDays(CalculateSalesJob::SHOPIFY_MONTHLY_RECURRING_BILLING_PERIOD_DAYS * 2)->toDateTimeString(),
            'time_range_start' => $subscriptionActivatedAt->addDays(CalculateSalesJob::SHOPIFY_MONTHLY_RECURRING_BILLING_PERIOD_DAYS)->toDateTimeString(),
            'time_range_end' => $subscriptionActivatedAt->addDays(CalculateSalesJob::SHOPIFY_MONTHLY_RECURRING_BILLING_PERIOD_DAYS * 2)->toDateTimeString(),
            'currency' => $this->store->currency,
            'tbyb_gross_sales' => 20000,
            'tbyb_discounts' => 2000,
            'tbyb_refunded_gross_sales' => 0,
            'tbyb_refunded_discounts' => 0,
            'tbyb_net_sales' => 18000,
            'is_first_of_billing_period' => true,
        ]);

        // Billing period 3 - 2024-03-05 12:00:00pm to 2024-04-04 11:59:59
        $this->assertDatabaseHas('orders_tbyb_net_sales', [
            'store_id' => $this->store->id,
            'date_start' => $subscriptionActivatedAt->addDays(CalculateSalesJob::SHOPIFY_MONTHLY_RECURRING_BILLING_PERIOD_DAYS * 2)->toDateTimeString(),
            'date_end' => $subscriptionActivatedAt->addDays(CalculateSalesJob::SHOPIFY_MONTHLY_RECURRING_BILLING_PERIOD_DAYS * 3)->toDateTimeString(),
            'time_range_start' => $subscriptionActivatedAt->addDays(CalculateSalesJob::SHOPIFY_MONTHLY_RECURRING_BILLING_PERIOD_DAYS * 2)->toDateTimeString(),
            'time_range_end' => $subscriptionActivatedAt->addDays(CalculateSalesJob::SHOPIFY_MONTHLY_RECURRING_BILLING_PERIOD_DAYS * 3)->toDateTimeString(),
            'currency' => $this->store->currency,
            'tbyb_gross_sales' => 0,
            'tbyb_discounts' => 0,
            'tbyb_refunded_gross_sales' => 10000,
            'tbyb_refunded_discounts' => 1000,
            'tbyb_net_sales' => -9000,
            'is_first_of_billing_period' => true,
        ]);

        // Billing period 4 - 2024-04-04 12:00:00pm to 2024-05-01 12:00:00 (partial)
        $this->assertDatabaseHas('orders_tbyb_net_sales', [
            'store_id' => $this->store->id,
            'date_start' => $subscriptionActivatedAt->addDays(CalculateSalesJob::SHOPIFY_MONTHLY_RECURRING_BILLING_PERIOD_DAYS * 3)->toDateTimeString(),
            'date_end' => CarbonImmutable::now()->toDateTimeString(),
            'time_range_start' => $subscriptionActivatedAt->addDays(CalculateSalesJob::SHOPIFY_MONTHLY_RECURRING_BILLING_PERIOD_DAYS * 3)->toDateTimeString(),
            'time_range_end' => CarbonImmutable::now()->toDateTimeString(),
            'currency' => $this->store->currency,
            'tbyb_gross_sales' => 12000,
            'tbyb_discounts' => 1200,
            'tbyb_refunded_gross_sales' => 0,
            'tbyb_refunded_discounts' => 0,
            'tbyb_net_sales' => 10800,
            'is_first_of_billing_period' => true,
        ]);

        Event::assertDispatchedTimes(TbybNetSaleCreatedEvent::class, 4);
    }

    public function testItCalculatesSalesCorrectlyAcrossMulitpleBillingPeriodsWithExistingCalculation(): void
    {
        $subscriptionActivatedAt = CarbonImmutable::create(2024, 1, 5, 12, 0, 0, 'utc');

        Order::factory()->create([
            'store_id' => $this->store->id,
            'original_tbyb_gross_sales_shop_amount' => 12000,
            'original_total_discounts_shop_amount' => 1200,
            'created_at' => CarbonImmutable::create(2024, 2, 4, 12, 0, 0, 'utc'),
        ]);
        Order::factory()->create([
            'store_id' => $this->store->id,
            'original_tbyb_gross_sales_shop_amount' => 8000,
            'original_total_discounts_shop_amount' => 800,
            'created_at' => CarbonImmutable::create(2024, 3, 5, 11, 59, 59, 'utc'),
        ]);
        Refund::factory()->create([
            'store_id' => $this->store->id,
            'tbyb_gross_sales_shop_amount' => 6000,
            'tbyb_discounts_shop_amount' => 600,
            'created_at' => CarbonImmutable::create(2024, 3, 5, 11, 59, 59, 'utc'),
        ]);

        $lastRunEndDate = CarbonImmutable::create(2024, 3, 6, 12, 0, 0, 'utc');
        TbybNetSale::withoutEvents(function () use ($lastRunEndDate) {
            return TbybNetSale::factory()->create([
                'store_id' => $this->store->id,
                'date_start' => CarbonImmutable::create(2024, 3, 5, 12, 0, 0, 'utc'),
                'date_end' => $lastRunEndDate,
                'time_range_start' => CarbonImmutable::create(2024, 3, 5, 12, 0, 0, 'utc'),
                'time_range_end' => $lastRunEndDate,
            ]);
        });

        Refund::factory()->create([
            'store_id' => $this->store->id,
            'tbyb_gross_sales_shop_amount' => 4000,
            'tbyb_discounts_shop_amount' => 400,
            'created_at' => CarbonImmutable::create(2024, 4, 4, 11, 59, 59, 'utc'),
        ]);

        // Next billing period
        Order::factory()->create([
            'store_id' => $this->store->id,
            'original_tbyb_gross_sales_shop_amount' => 12000,
            'original_total_discounts_shop_amount' => 1200,
            'created_at' => CarbonImmutable::create(2024, 4, 4, 12, 0, 0, 'utc'),
        ]);
        Refund::factory()->create([
            'store_id' => $this->store->id,
            'tbyb_gross_sales_shop_amount' => 3000,
            'tbyb_discounts_shop_amount' => 350,
            'created_at' => CarbonImmutable::create(2024, 5, 1, 11, 59, 59, 'utc'),
        ]);

        Http::fake([
            'http://localhost:8080/api/stores/billings/subscriptions/active' => Http::response([
                'subscription' => Subscription::builder()->active()->create([
                    'store_id' => $this->store->id,
                    'activated_at' => $subscriptionActivatedAt,
                    'current_period_start' => CarbonImmutable::create(2024, 4, 4, 12, 0, 0, 'utc'),
                    'current_period_end' => CarbonImmutable::create(2024, 5, 4, 12, 0, 0, 'utc'),
                    'trial_period_end' => $subscriptionActivatedAt->addDays(30),
                ])->toArray(),
            ]),
        ]);

        App::call([$this->job, 'handle']);

        $this->assertDatabaseHas('orders_tbyb_net_sales', [
            'store_id' => $this->store->id,
            'date_start' => $lastRunEndDate->toDateTimeString(),
            'date_end' => $subscriptionActivatedAt->addDays(CalculateSalesJob::SHOPIFY_MONTHLY_RECURRING_BILLING_PERIOD_DAYS * 3)->toDateTimeString(),
            'time_range_start' => $lastRunEndDate->toDateTimeString(),
            'time_range_end' => $subscriptionActivatedAt->addDays(CalculateSalesJob::SHOPIFY_MONTHLY_RECURRING_BILLING_PERIOD_DAYS * 3)->toDateTimeString(),
            'currency' => $this->store->currency,
            'tbyb_gross_sales' => 0,
            'tbyb_discounts' => 0,
            'tbyb_refunded_gross_sales' => 4000,
            'tbyb_refunded_discounts' => 400,
            'tbyb_net_sales' => -3600,
            'is_first_of_billing_period' => false,
        ]);

        $this->assertDatabaseHas('orders_tbyb_net_sales', [
            'store_id' => $this->store->id,
            'date_start' => $subscriptionActivatedAt->addDays(CalculateSalesJob::SHOPIFY_MONTHLY_RECURRING_BILLING_PERIOD_DAYS * 3)->toDateTimeString(),
            'date_end' => CarbonImmutable::now()->toDateTimeString(),
            'time_range_start' => $subscriptionActivatedAt->addDays(CalculateSalesJob::SHOPIFY_MONTHLY_RECURRING_BILLING_PERIOD_DAYS * 3)->toDateTimeString(),
            'time_range_end' => CarbonImmutable::now()->toDateTimeString(),
            'currency' => $this->store->currency,
            'tbyb_gross_sales' => 12000,
            'tbyb_discounts' => 1200,
            'tbyb_refunded_gross_sales' => 3000,
            'tbyb_refunded_discounts' => 350,
            'tbyb_net_sales' => 8150,
            'is_first_of_billing_period' => true,
        ]);

        Event::assertDispatchedTimes(TbybNetSaleCreatedEvent::class, 2);
    }
}
