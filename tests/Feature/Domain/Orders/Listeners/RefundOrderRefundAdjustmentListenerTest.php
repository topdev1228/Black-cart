<?php
declare(strict_types=1);

namespace Tests\Feature\Domain\Orders\Listeners;

use App\Domain\Orders\Enums\LineItemStatus;
use App\Domain\Orders\Enums\ShopifyRefundLineItemRestockType;
use App\Domain\Orders\Listeners\RefundOrderRefundAdjustmentListener;
use App\Domain\Orders\Models\LineItem;
use App\Domain\Orders\Models\Order;
use App\Domain\Orders\Models\Transaction;
use App\Domain\Orders\Values\Order as OrderValue;
use App\Domain\Orders\Values\OrderCompletedEvent;
use App\Domain\Stores\Models\Store;
use Http;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\App;
use Tests\TestCase;

class RefundOrderRefundAdjustmentListenerTest extends TestCase
{
    public function testItDoesNotRefundWithoutAdjustments(): void
    {
        $store = Store::factory()->create();
        App::context(store: $store);

        $order = Order::factory()->create(['store_id' => $store->id]);
        Transaction::factory()->create(['order_id' => $order->id, 'store_id' => $store->id]);

        Http::fake();

        $event = OrderCompletedEvent::builder()->create(['order' => OrderValue::builder()->create(['id' => $order->id])]);

        $listener = resolve(RefundOrderRefundAdjustmentListener::class);
        $listener->handle($event);

        Http::assertSentCount(0);
    }

    public function testItRefundsAdjustments(): void
    {
        $store = Store::factory()->create();
        App::context(store: $store);

        $order = Order::factory(['store_id' => $store->id])->create();
        $transaction = Transaction::factory(['source_id' => 'gid://shopify/OrderTransaction/574890741', 'order_id' => $order->id, 'store_id' => $store->id])->create();

        LineItem::factory(['order_id' => $order->id, 'status' => LineItemStatus::ARCHIVED])->create();
        $lineItem = LineItem::factory(['order_id' => $order->id, 'status' => LineItemStatus::INTERNAL])->create();

        Http::fake([
            '*' => Http::response([
                'data' => [
                    '__typename' => 'Mutation',
                    'refundCreate' => [
                        '__typename' => 'RefundCreatePayload',
                        'refund' => [
                            '__typename' => 'Refund',
                            'id' => 'test-refund-id',
                        ],
                        'userErrors' => [],
                    ],
                ],
            ]),
        ]);

        $event = OrderCompletedEvent::builder()->create(['order' => OrderValue::builder()->create(['id' => $order->id])]);

        $listener = resolve(RefundOrderRefundAdjustmentListener::class);
        $listener->handle($event);

        Http::assertSent(function (Request $request) use ($lineItem, $transaction, $order) {
            $body = json_decode($request->body(), true);
            $this->assertEquals($order->source_id, $body['variables']['orderId']);
            $this->assertEquals(config('shopify.order_refund_adjustment.staff_note'), $body['variables']['note']);
            $this->assertEquals($order->customer_currency->value, $body['variables']['currency']);
            $this->assertEquals(0, $body['variables']['amount']);
            $this->assertEquals($transaction->gateway, $body['variables']['gateway']);
            $this->assertEquals('gid://shopify/OrderTransaction/574890741', $body['variables']['parentTransactionId']);
            $this->assertEquals([
                [
                    'lineItemId' => $lineItem->source_id,
                    'restockType' => ShopifyRefundLineItemRestockType::NO_RESTOCK->name,
                    'quantity' => 1,
                    'locationId' => null,
                ],
            ], $body['variables']['refundLineItems']);

            return true;
        });

        Http::assertSentCount(1);
    }

    public function testItDoesNotRefundMultipleTimes()
    {
        $store = Store::factory()->create();
        App::context(store: $store);

        $order = Order::factory(['store_id' => $store->id])->create();
        $transaction = Transaction::factory(['source_id' => 'gid://shopify/OrderTransaction/574890741', 'order_id' => $order->id, 'store_id' => $store->id])->create();

        LineItem::factory(['order_id' => $order->id, 'status' => LineItemStatus::ARCHIVED])->create();
        $lineItem = LineItem::factory(['order_id' => $order->id, 'status' => LineItemStatus::INTERNAL])->create();

        Http::fake([
            '*' => Http::response([
                'data' => [
                    '__typename' => 'Mutation',
                    'refundCreate' => [
                        '__typename' => 'RefundCreatePayload',
                        'refund' => [
                            '__typename' => 'Refund',
                            'id' => 'test-refund-id',
                        ],
                        'userErrors' => [],
                    ],
                ],
            ]),
        ]);

        $event = OrderCompletedEvent::builder()->create(['order' => OrderValue::builder()->create(['id' => $order->id])]);

        $listener = resolve(RefundOrderRefundAdjustmentListener::class);
        $listener->handle($event);
        $listener->handle($event);

        Http::assertSent(function (Request $request) use ($lineItem, $transaction, $order) {
            $body = json_decode($request->body(), true);
            $this->assertEquals($order->source_id, $body['variables']['orderId']);
            $this->assertEquals(config('shopify.order_refund_adjustment.staff_note'), $body['variables']['note']);
            $this->assertEquals($order->customer_currency->value, $body['variables']['currency']);
            $this->assertEquals(0, $body['variables']['amount']);
            $this->assertEquals($transaction->gateway, $body['variables']['gateway']);
            $this->assertEquals('gid://shopify/OrderTransaction/574890741', $body['variables']['parentTransactionId']);
            $this->assertEquals([
                [
                    'lineItemId' => $lineItem->source_id,
                    'restockType' => ShopifyRefundLineItemRestockType::NO_RESTOCK->name,
                    'quantity' => 1,
                    'locationId' => null,
                ],
            ], $body['variables']['refundLineItems']);

            return true;
        });

        Http::assertSentCount(1);
    }
}
