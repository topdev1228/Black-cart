<?php

declare(strict_types=1);

namespace Tests\Feature\Domain\Orders\Listeners;

use App\Domain\Orders\Jobs\AssumeOrderDeliveredJob;
use App\Domain\Orders\Listeners\AssumeOrderDeliveredListener;
use App\Domain\Orders\Models\Order;
use App\Domain\Orders\Services\OrderService;
use App\Domain\Orders\Values\Order as OrderValue;
use App\Domain\Orders\Values\OrderCreatedEvent as OrderCreatedEventValue;
use App\Domain\Stores\Models\Store;
use App\Domain\Stores\Values\Store as StoreValue;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Bus;
use Tests\TestCase;

class AssumeOrderDeliveredListenerTest extends TestCase
{
    protected Store $store;
    protected OrderService $orderService;
    protected AssumeOrderDeliveredListener $listener;

    protected function setUp(): void
    {
        parent::setUp();

        $this->store = Store::withoutEvents(function () {
            return Store::factory()->create();
        });

        App::context(store: StoreValue::from($this->store));

        $this->orderService = resolve(OrderService::class);
        $this->listener = new AssumeOrderDeliveredListener($this->orderService);
    }

    public function testItCallsHandler(): void
    {
        $order = Order::withoutEvents(function () {
            return Order::factory()->tbybOnly(10000, 10000)
                ->netSales(10000, 10000, 0, 0)->create(['store_id' => $this->store->id]);
        });
        $orderValue = OrderValue::from($order);

        $event = new OrderCreatedEventValue($orderValue);

        Bus::fake();

        $this->listener->handle($event);

        Bus::assertDispatched(AssumeOrderDeliveredJob::class);
    }
}
