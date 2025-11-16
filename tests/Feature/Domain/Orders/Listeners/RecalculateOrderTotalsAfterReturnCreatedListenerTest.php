<?php
declare(strict_types=1);

namespace Feature\Domain\Orders\Listeners;

use App\Domain\Orders\Listeners\RecalculateOrderTotalsAfterReturnCreatedListener;
use App\Domain\Orders\Models\OrderReturn;
use App\Domain\Orders\Services\OrderService;
use App\Domain\Orders\Values\OrderReturn as OrderReturnValue;
use App\Domain\Orders\Values\ReturnCreatedEvent;
use App\Domain\Stores\Models\Store;
use App\Domain\Stores\Values\Store as StoreValue;
use Illuminate\Support\Facades\App;
use Tests\Fixtures\Domains\Orders\Traits\OrderRecalculateTotalsData;
use Tests\TestCase;

class RecalculateOrderTotalsAfterReturnCreatedListenerTest extends TestCase
{
    use OrderRecalculateTotalsData;

    protected Store $store;

    protected function setUp(): void
    {
        parent::setUp();

        $this->store = Store::withoutEvents(function () {
            return Store::factory()->create();
        });
        App::context(store: StoreValue::from($this->store));
    }

    public function testItDoesNotRecalculateOrder(): void
    {
        $this->mock(OrderService::class, function ($mock) {
            $mock->shouldReceive('recalculateOrderTotals')->never();
        });

        $return = OrderReturn::withoutEvents(function () {
            return OrderReturn::factory()->create([
                'order_id' => '12345',
                'store_id' => $this->store->id,
            ]);
        });

        $listener = resolve(RecalculateOrderTotalsAfterReturnCreatedListener::class);
        $listener->handle(new ReturnCreatedEvent(OrderReturnValue::from($return)));
    }
}
