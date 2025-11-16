<?php

declare(strict_types=1);

namespace Tests\Feature\Domain\Orders\Jobs;

use App\Domain\Orders\Enums\LineItemStatus;
use App\Domain\Orders\Enums\OrderStatus;
use App\Domain\Orders\Events\TrialableDeliveredEvent;
use App\Domain\Orders\Jobs\AssumeOrderDeliveredJob;
use App\Domain\Orders\Models\Order;
use App\Domain\Orders\Services\OrderService;
use App\Domain\Orders\Values\LineItem;
use App\Domain\Orders\Values\Order as OrderValue;
use App\Domain\Stores\Models\Store;
use App\Domain\Stores\Values\Store as StoreValue;
use Event;
use Illuminate\Support\Facades\App;
use Tests\TestCase;

class AssumeOrderDeliveredJobTest extends TestCase
{
    protected Store $store;
    protected OrderService $orderService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->orderService = resolve(OrderService::class);
        $this->store = Store::withoutEvents(function () {
            return Store::factory()->create();
        });

        Event::fake([TrialableDeliveredEvent::class]);
        App::context(store: StoreValue::from($this->store));
    }

    public function testItDoesNotAssumeDeliveryWithNoOrder(): void
    {
        $orderValue = OrderValue::builder()->create();
        $orderValue->id = 'non-existent-order-id';

        $job = new AssumeOrderDeliveredJob($orderValue);
        $job->handle($this->orderService);

        Event::assertNotDispatched(TrialableDeliveredEvent::class);
    }

    public function testItDoesNotAssumeDeliveryWithOrderCancelled(): void
    {
        $order = Order::factory()->create([
            'id' => $this->store->id,
            'status' => OrderStatus::CANCELLED,
        ]);
        $orderValue = OrderValue::from($order);
        $job = new AssumeOrderDeliveredJob($orderValue);
        $job->handle($this->orderService);

        Event::assertNotDispatched(TrialableDeliveredEvent::class);
    }

    public function testItDoesNotAssumeDeliveryWithOrderInTrial(): void
    {
        $order = Order::factory()->create([
            'id' => $this->store->id,
            'status' => OrderStatus::IN_TRIAL,
        ]);
        $orderValue = OrderValue::from($order);
        $job = new AssumeOrderDeliveredJob($orderValue);
        $job->handle($this->orderService);

        Event::assertNotDispatched(TrialableDeliveredEvent::class);
    }

    public function testItDoesNotAssumeDeliveryWithOrderCompleted(): void
    {
        $order = Order::factory()->create([
            'id' => $this->store->id,
            'status' => OrderStatus::COMPLETED,
        ]);
        $orderValue = OrderValue::from($order);
        $job = new AssumeOrderDeliveredJob($orderValue);
        $job->handle($this->orderService);

        Event::assertNotDispatched(TrialableDeliveredEvent::class);
    }

    public function testItDoesNotAssumeDeliveryWithNonOpenLineItems(): void
    {
        $order = Order::factory()->create([
            'id' => $this->store->id,
            'status' => OrderStatus::OPEN,
        ]);
        $orderValue = OrderValue::from($order);
        $orderValue->lineItems = LineItem::collection([
            LineItem::builder()->create([
                'order_id' => $order->id,
                'status' => LineItemStatus::DELIVERED,
            ]),
        ]);
        $job = new AssumeOrderDeliveredJob($orderValue);
        $job->handle($this->orderService);

        Event::assertNotDispatched(TrialableDeliveredEvent::class);
    }
}
