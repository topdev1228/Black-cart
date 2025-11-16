<?php
declare(strict_types=1);

namespace Tests\Feature\Domain\Orders\Listeners;

use App\Domain\Orders\Enums\LineItemStatus;
use App\Domain\Orders\Enums\OrderStatus;
use App\Domain\Orders\Events\LineItemSavedEvent;
use App\Domain\Orders\Listeners\UpdateOrderStatusAfterLineItemSavedListener;
use App\Domain\Orders\Models\LineItem;
use App\Domain\Orders\Models\Order;
use App\Domain\Orders\Values\LineItem as LineItemValue;
use App\Domain\Orders\Values\LineItemSavedEvent as LineItemSavedEventValue;
use App\Domain\Stores\Models\Store;
use App\Domain\Stores\Values\Store as StoreValue;
use Event;
use Illuminate\Support\Facades\App;
use Tests\TestCase;

class UpdateOrderStatusAfterLineItemSavedListenerTest extends TestCase
{
    protected Store $store;

    protected function setUp(): void
    {
        parent::setUp();

        $this->store = Store::withoutEvents(function () {
            return Store::factory()->create();
        });
        App::context(store: StoreValue::from($this->store));
    }

    public function testItUpdatesOrderStatusOnLineItemStatusFulfilled(): void
    {
        $order = Order::withoutEvents(function () {
            return Order::factory()->create([
                'store_id' => $this->store->id,
                'status' => OrderStatus::OPEN,
            ]);
        });

        $lineItem = LineItem::factory()->create([
            'status' => LineItemStatus::FULFILLED,
            'order_id' => $order->id,
        ]);

        $listener = resolve(UpdateOrderStatusAfterLineItemSavedListener::class);
        $listener->handle(new LineItemSavedEventValue(LineItemValue::from($lineItem)));

        $order->refresh();

        $this->assertEquals(OrderStatus::FULFILLMENT_PARTIALLY_FULFILLED, $order->status);
    }

    public function testItUpdatesOrderStatusOnLineItemStatusDelivered(): void
    {
        $order = Order::withoutEvents(function () {
            return Order::factory()->create([
                'store_id' => $this->store->id,
                'status' => OrderStatus::OPEN,
            ]);
        });

        $lineItem = LineItem::factory()->create([
            'status' => LineItemStatus::DELIVERED,
            'order_id' => $order->id,
        ]);

        $listener = resolve(UpdateOrderStatusAfterLineItemSavedListener::class);
        $listener->handle(new LineItemSavedEventValue(LineItemValue::from($lineItem)));

        $order->refresh();

        $this->assertEquals(OrderStatus::FULFILLMENT_PARTIALLY_FULFILLED, $order->status);
    }

    public function testItDoesNotUpdateOrderStatusOnBeforeOrderStatusCancelled(): void
    {
        $order = Order::withoutEvents(function () {
            return Order::factory()->create([
                'store_id' => $this->store->id,
                'status' => OrderStatus::CANCELLED,
            ]);
        });

        $lineItem = LineItem::factory()->create([
            'status' => LineItemStatus::DELIVERED,
            'order_id' => $order->id,
        ]);

        $listener = resolve(UpdateOrderStatusAfterLineItemSavedListener::class);
        $listener->handle(new LineItemSavedEventValue(LineItemValue::from($lineItem)));

        $order->refresh();

        $this->assertEquals(OrderStatus::CANCELLED, $order->status);
    }
}
