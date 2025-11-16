<?php
declare(strict_types=1);

namespace Tests\Unit\Domain\Orders\Listeners;

use App\Domain\Orders\Listeners\AddOrderRefundAdjustmentListener;
use App\Domain\Orders\Models\Order;
use App\Domain\Orders\Services\OrderService;
use App\Domain\Orders\Values\Refund;
use App\Domain\Orders\Values\RefundCreatedEvent;
use App\Domain\Stores\Models\Store;
use Brick\Money\Money;
use Illuminate\Support\Facades\App;
use Mockery\MockInterface;
use Tests\TestCase;

class AddOrderRefundAdjustmentListenerTest extends TestCase
{
    public function testItCreatesAdjustments(): void
    {
        $store = Store::factory()->create();
        App::context(store: $store);

        $order = Order::factory()->create(['store_id' => $store->id, 'shop_currency' => 'CAD', 'source_id' => 'gid://shopify/Order/467284042']);

        $this->mock(OrderService::class, function (MockInterface $mock) use ($order) {
            $mock->shouldReceive('addOrderRefundAdjustment')->once()->withArgs(function (string $orderId, Money $amount) use ($order) {
                $this->assertEquals($order->id, $orderId);
                $this->assertTrue($amount->isEqualTo(Money::ofMinor(10000, 'USD')));

                return true;
            });
        });

        $listener = app(AddOrderRefundAdjustmentListener::class);
        $event = RefundCreatedEvent::builder()->create([
            'refund' => Refund::builder()->create([
                'order_id' => $order->id,
                'refunded_customer_amount' => 10000,
            ]),
        ]);

        $listener->handle($event);
    }
}
