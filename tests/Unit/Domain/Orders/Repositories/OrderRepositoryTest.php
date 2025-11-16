<?php
declare(strict_types=1);

namespace Tests\Unit\Domain\Orders\Repositories;

use App;
use App\Domain\Orders\Enums\OrderStatus;
use App\Domain\Orders\Events\OrderCreatedEvent;
use App\Domain\Orders\Models\LineItem;
use App\Domain\Orders\Models\Order;
use App\Domain\Orders\Repositories\OrderRepository;
use App\Domain\Orders\Values\Order as OrderValue;
use App\Domain\Stores\Models\Store;
use App\Domain\Stores\Values\Store as StoreValue;
use Carbon\CarbonImmutable;
use Event;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Str;
use Tests\TestCase;

class OrderRepositoryTest extends TestCase
{
    protected Store $store;
    protected OrderRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->store = Store::factory()->create();
        App::context(store: $this->store);
        $this->repository = resolve(OrderRepository::class);
    }

    public function testItGetsOrderById(): void
    {
        $order = Order::withoutEvents(function () {
            return Order::factory()->create([
                'store_id' => $this->store->id,
            ]);
        });
        LineItem::factory()->for($order)->create();
        $orderValue = OrderValue::from($order->load(['lineItems', 'refunds', 'returns', 'transactions']));

        $result = $this->repository->getById($order->id);

        $this->assertEquals($orderValue, $result);
        $this->assertCount(1, $result->lineItems);
    }

    public function testItGetsOrderBySourceId(): void
    {
        $order = Order::withoutEvents(function () {
            return Order::factory()->create([
                'store_id' => $this->store->id,
            ]);
        });
        LineItem::factory()->for($order)->create();
        $orderValue = OrderValue::from($order->load(['lineItems', 'refunds', 'returns', 'transactions']));

        $result = $this->repository->getBySourceId($orderValue->sourceId);

        $this->assertEquals($orderValue, $result);
        $this->assertCount(1, $result->lineItems);
    }

    public function testItGetsOrderBySourceIdWithoutGid(): void
    {
        $order = Order::withoutEvents(function () {
            return Order::factory()->create([
                'store_id' => $this->store->id,
            ]);
        });
        LineItem::factory()->for($order)->create();
        $orderValue = OrderValue::from($order->load(['lineItems', 'refunds', 'returns', 'transactions']));

        $result = $this->repository->getBySourceId(Str::afterLast($orderValue->sourceId, '/'));

        $this->assertEquals($orderValue, $result);
        $this->assertCount(1, $result->lineItems);
    }

    public function testItGetsUnsafeOrderById(): void
    {
        $order = Order::withoutEvents(function () {
            return Order::factory()->create([
                'store_id' => $this->store->id,
            ]);
        });

        // Add relations
        LineItem::factory()->for($order)->create();

        App::context(store: StoreValue::from([]));

        $result = $this->repository->getById($order->id, false);
        $this->assertNotNull($result);
    }

    public function testItFailsToGetWithoutScope(): void
    {
        $order = Order::withoutEvents(function () {
            return Order::factory()->create([
                'store_id' => $this->store->id,
            ]);
        });

        // Add relations
        LineItem::factory()->for($order)->create();

        App::context(store: StoreValue::from([]));

        $this->expectException(App\Domain\Stores\Exceptions\MissingStoreContextException::class);
        $this->repository->getById($order->id);
    }

    public function testItGetsOrderByTrialGroupId(): void
    {
        $order = Order::withoutEvents(function () {
            return Order::factory()->create([
                'store_id' => $this->store->id,
            ]);
        });
        $lineItem = LineItem::factory(['trial_group_id' => '12345'])->for($order)->create();

        $orderValue = OrderValue::from($order->load(['lineItems', 'refunds', 'returns', 'transactions']));

        $result = $this->repository->getByTrialGroupId($lineItem->trial_group_id);

        $this->assertEquals($orderValue, $result);
    }

    public function testItThrowsExceptionOnNotFound(): void
    {
        $this->expectException(ModelNotFoundException::class);
        $this->repository->getById('12345');
    }

    public function testItCreatesOrder(): void
    {
        Event::fake([
            OrderCreatedEvent::class,
        ]);

        $val = OrderValue::builder()->create();

        $newOrder = $this->repository->create($val);
        $this->assertDatabaseCount('orders', 1);

        Event::assertDispatched(
            OrderCreatedEvent::class,
            function (OrderCreatedEvent $event) use ($newOrder) {
                $this->assertEquals($event->order->id, $newOrder->id);

                return true;
            },
        );
    }

    public function testItUpdatesOrder(): void
    {
        $order = Order::withoutEvents(function () {
            return Order::factory()->create([
                'status' => OrderStatus::ARCHIVED,
                'store_id' => $this->store->id,
            ]);
        });
        $val = OrderValue::from($order);
        $val->status = OrderStatus::OPEN;

        $updatedVal = $this->repository->update($val);
        $order = $order->refresh();

        $this->assertEquals(OrderStatus::OPEN, $updatedVal->status);
        $this->assertEquals(OrderStatus::OPEN, $order->status);
    }

    public function testItGetsAllOrders(): void
    {
        Order::withoutEvents(function () {
            return Order::factory()->count(2)->create([
                'store_id' => $this->store->id,
            ]);
        });

        $allOrders = $this->repository->all();
        $this->assertCount(2, $allOrders);
    }

    public function testItGetsStoreIdsByDateCorrectly(): void
    {
        $store1 = Store::factory()->create();
        $store2 = Store::factory()->create();
        $store3 = Store::factory()->create();

        Order::withoutEvents(function () use ($store1) {
            return Order::factory()->count(2)->create([
                'store_id' => $store1->id,
            ]);
        });

        Order::withoutEvents(function () use ($store2) {
            return Order::factory()->create([
                'store_id' => $store2->id,
            ]);
        });

        Order::withoutEvents(function () use ($store3) {
            return Order::factory()->count(6)->create([
                'store_id' => $store3->id,
                'created_at' => CarbonImmutable::now()->subYear(),
            ]);
        });

        $return = $this->repository->getStoreIdsByDate(CarbonImmutable::now()->subMonths(6));

        $this->assertCount(2, $return);
    }

    public function testItGetsGrossSalesCorrectly(): void
    {
        $startDate = CarbonImmutable::now()->subMonths(6);

        Order::withoutEvents(function () use ($startDate) {
            Order::factory()->count(2)->create([
                'store_id' => $this->store->id,
                'original_tbyb_gross_sales_shop_amount' => 12500,
                'created_at' => CarbonImmutable::now()->subSecond(),
            ]);
            Order::factory()->create([
                'store_id' => $this->store->id,
                'original_tbyb_gross_sales_shop_amount' => 5000,
                'created_at' => $startDate->addDay(),
            ]);

            // This order is not included it's before the start date
            Order::factory()->create([
                'store_id' => $this->store->id,
                'original_tbyb_gross_sales_shop_amount' => 6000,
                'created_at' => $startDate->subDay(),
            ]);
            // This order is not included as the query looks for 1 second less than end date
            Order::factory()->create([
                'store_id' => $this->store->id,
                'original_tbyb_gross_sales_shop_amount' => 5000,
                'created_at' => CarbonImmutable::now(),
            ]);
            // This order is not included as it's from a different store
            Order::factory()->count(2)->create([
                'store_id' => 'different-store-id-2',
                'original_tbyb_gross_sales_shop_amount' => 12500,
                'created_at' => CarbonImmutable::now(),
            ]);
        });

        $return = $this->repository->getGrossSales(CarbonImmutable::now(), $startDate);

        $this->assertEquals(30000, $return);
    }

    public function testItGetsDiscountsCorrectly(): void
    {
        $startDate = CarbonImmutable::now()->subMonths(6);

        Order::withoutEvents(function () use ($startDate) {
            Order::factory()->count(2)->create([
                'store_id' => $this->store->id,
                'original_total_discounts_shop_amount' => 12500,
                'created_at' => CarbonImmutable::now()->subSecond(),
            ]);
            Order::factory()->create([
                'store_id' => $this->store->id,
                'original_total_discounts_shop_amount' => 5000,
                'created_at' => $startDate->addDay(),
            ]);

            // This order is not included as it's the before the start date
            Order::factory()->create([
                'store_id' => $this->store->id,
                'original_total_discounts_shop_amount' => 6000,
                'created_at' => $startDate->subDay(),
            ]);
            // This order is not included as the query looks for 1 second less than end date
            Order::factory()->create([
                'store_id' => $this->store->id,
                'original_total_discounts_shop_amount' => 5000,
                'created_at' => CarbonImmutable::now(),
            ]);
            // This order is not included as it's from a different store
            Order::factory()->count(2)->create([
                'store_id' => 'different-store-id-2',
                'original_total_discounts_shop_amount' => 12500,
                'created_at' => CarbonImmutable::now(),
            ]);
        });

        $return = $this->repository->getTotalDiscounts(CarbonImmutable::now(), $startDate);

        $this->assertEquals(30000, $return);
    }

    public function testGetOrdersWithinDateRange(): void
    {
        $start = CarbonImmutable::now()->setMonth(4);
        $end = CarbonImmutable::now()->setMonth(5);
        Order::withoutEvents(function () {
            return Order::factory()->count(2)->create([
                'store_id' => $this->store->id,
                'created_at' => CarbonImmutable::now()->setMonth(4)->setDay(1)->setTime(12, 0),
            ]);
        });
        Order::withoutEvents(function () {
            return Order::factory()->create([
                'store_id' => $this->store->id,
                'created_at' => CarbonImmutable::now()->setMonth(5)->setDay(31)->setTime(12, 0),
            ]);
        });

        //Don't get order day after limit
        Order::withoutEvents(function () {
            return Order::factory()->count(5)->create([
                'store_id' => $this->store->id,
                'created_at' => CarbonImmutable::now()->setMonth(6)->setDay(1)->setTime(12, 0),
            ]);
        });
        //Don't get order day before limit
        Order::withoutEvents(function () {
            return Order::factory()->count(3)->create([
                'store_id' => $this->store->id,
                'created_at' => CarbonImmutable::now()->setMonth(3)->setDay(31)->setTime(12, 0),
            ]);
        });
        $orders = $this->repository->getOrdersByDateRange($start, $end);
        $this->assertEquals(3, $orders->count());
    }

    public function testItGetsOrderByPaymentTermsId(): void
    {
        $order = Order::withoutEvents(function () {
            return Order::factory()->create([
                'store_id' => $this->store->id,
                'payment_terms_id' => '123456789',
            ]);
        });

        $orderResult = $this->repository->getByPaymentTermsId($order->payment_terms_id);

        $this->assertEquals($order->payment_terms_id, $orderResult->paymentTermsId);
    }
}
