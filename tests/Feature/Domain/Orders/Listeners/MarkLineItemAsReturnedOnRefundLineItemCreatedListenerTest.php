<?php
declare(strict_types=1);

namespace Tests\Feature\Domain\Orders\Listeners;

use App\Domain\Orders\Enums\LineItemDecisionStatus;
use App\Domain\Orders\Listeners\MarkLineItemAsReturnedOnRefundLineItemCreatedListener;
use App\Domain\Orders\Models\LineItem;
use App\Domain\Orders\Models\Order;
use App\Domain\Orders\Models\RefundLineItem;
use App\Domain\Orders\Values\RefundLineItem as RefundLineItemValue;
use App\Domain\Orders\Values\RefundLineItemCreatedEvent as RefundLineItemCreatedEventValue;
use App\Domain\Stores\Models\Store;
use App\Domain\Stores\Values\Store as StoreValue;
use Illuminate\Support\Facades\App;
use Tests\TestCase;

class MarkLineItemAsReturnedOnRefundLineItemCreatedListenerTest extends TestCase
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

    public function testItMarksLineItemAsReturned(): void
    {
        $order = Order::withoutEvents(function () {
            return Order::factory()->create(['store_id' => $this->store->id]);
        });

        $lineItem = LineItem::factory()->create(['order_id' => $order->id]);

        $refundLineItem = RefundLineItem::withoutEvents(function () use ($lineItem) {
            return RefundLineItem::factory()->create(['line_item_id' => $lineItem->source_id]);
        });

        $refundLineItemValue = RefundLineItemValue::from($refundLineItem);

        $event = new RefundLineItemCreatedEventValue($refundLineItemValue);

        $listener = resolve(MarkLineItemAsReturnedOnRefundLineItemCreatedListener::class);
        $listener->handle($event);

        $lineItem->refresh();

        $this->assertEquals(LineItemDecisionStatus::RETURNED, $lineItem->decision_status);
    }
}
