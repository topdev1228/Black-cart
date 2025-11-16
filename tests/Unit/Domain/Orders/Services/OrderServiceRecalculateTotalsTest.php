<?php
declare(strict_types=1);

namespace Tests\Unit\Domain\Orders\Services;

use App;
use App\Domain\Orders\Enums\OrderStatus;
use App\Domain\Orders\Events\OrderCompletedEvent;
use App\Domain\Orders\Models\Order;
use App\Domain\Orders\Models\Refund;
use App\Domain\Orders\Models\Transaction;
use App\Domain\Orders\Services\OrderService;
use App\Domain\Stores\Models\Store;
use Event;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Date;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\Fixtures\Domains\Orders\Traits\OrderRecalculateTotalsData;
use Tests\TestCase;

class OrderServiceRecalculateTotalsTest extends TestCase
{
    use OrderRecalculateTotalsData;

    protected OrderService $service;
    protected Store $store;

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = resolve(OrderService::class);

        $this->store = Store::withoutEvents(function () {
            return Store::factory()->create();
        });
        App::context(store: $this->store);
    }

    public function testItDoesNotRecalculateOrderTotalsOnOrderNotFound(): void
    {
        $this->expectException(ModelNotFoundException::class);
        $this->service->recalculateOrderTotals('12345');
    }

    #[DataProvider('orderRecalculateTotalsDataProvider')]
    public function testItRecalculatesOrderTotals(
        array $order,
        array $refunds,
        array $transactions,
        array $assertions
    ): void {
        Date::setTestNow(Date::create(2024, 3, 10, 15, 0, 0, 'UTC'));
        Event::fake([
            OrderCompletedEvent::class,
        ]);

        $order = Order::withoutEvents(function () use ($order) {
            return Order::factory()->state($order)->create([
                'store_id' => $this->store->id,
            ]);
        });
        foreach ($refunds as $refund) {
            Refund::withoutEvents(function () use ($order, $refund) {
                return Refund::factory()->for($order)->state($refund)->create([
                    'store_id' => $this->store->id,
                ]);
            });
        }
        foreach ($transactions as $transaction) {
            Transaction::withoutEvents(function () use ($order, $transaction) {
                return Transaction::factory()->for($order)->state($transaction)->create([
                    'store_id' => $this->store->id,
                ]);
            });
        }

        $updatedOrder = $this->service->recalculateOrderTotals($order->id);

        $this->assertEquals($assertions['order']['tbyb_refund_gross_sales_shop_amount'], $updatedOrder->tbybRefundGrossSalesShopAmount->getMinorAmount()->toInt());
        $this->assertEquals($assertions['order']['tbyb_refund_gross_sales_customer_amount'], $updatedOrder->tbybRefundGrossSalesCustomerAmount->getMinorAmount()->toInt());
        $this->assertEquals($assertions['order']['upfront_refund_gross_sales_shop_amount'], $updatedOrder->upfrontRefundGrossSalesShopAmount->getMinorAmount()->toInt());
        $this->assertEquals($assertions['order']['upfront_refund_gross_sales_customer_amount'], $updatedOrder->upfrontRefundGrossSalesCustomerAmount->getMinorAmount()->toInt());
        $this->assertEquals($assertions['order']['tbyb_refund_discounts_shop_amount'], $updatedOrder->tbybRefundDiscountsShopAmount->getMinorAmount()->toInt());
        $this->assertEquals($assertions['order']['tbyb_refund_discounts_customer_amount'], $updatedOrder->tbybRefundDiscountsCustomerAmount->getMinorAmount()->toInt());
        $this->assertEquals($assertions['order']['upfront_refund_discounts_shop_amount'], $updatedOrder->upfrontRefundDiscountsShopAmount->getMinorAmount()->toInt());
        $this->assertEquals($assertions['order']['upfront_refund_discounts_customer_amount'], $updatedOrder->upfrontRefundDiscountsCustomerAmount->getMinorAmount()->toInt());
        $this->assertEquals($assertions['order']['total_order_level_refunds_shop_amount'], $updatedOrder->totalOrderLevelRefundsShopAmount->getMinorAmount()->toInt());
        $this->assertEquals($assertions['order']['total_order_level_refunds_customer_amount'], $updatedOrder->totalOrderLevelRefundsCustomerAmount->getMinorAmount()->toInt());
        // net sales
        $this->assertEquals($assertions['order']['tbyb_net_sales_shop_amount'], $updatedOrder->tbybNetSalesShopAmount->getMinorAmount()->toInt());
        $this->assertEquals($assertions['order']['tbyb_net_sales_customer_amount'], $updatedOrder->tbybNetSalesCustomerAmount->getMinorAmount()->toInt());
        $this->assertEquals($assertions['order']['upfront_net_sales_shop_amount'], $updatedOrder->upfrontNetSalesShopAmount->getMinorAmount()->toInt());
        $this->assertEquals($assertions['order']['upfront_net_sales_customer_amount'], $updatedOrder->upfrontNetSalesCustomerAmount->getMinorAmount()->toInt());
        $this->assertEquals($assertions['order']['total_net_sales_shop_amount'], $updatedOrder->totalNetSalesShopAmount->getMinorAmount()->toInt());
        $this->assertEquals($assertions['order']['total_net_sales_customer_amount'], $updatedOrder->totalNetSalesCustomerAmount->getMinorAmount()->toInt());
        // outstanding
        $this->assertEquals($assertions['order']['outstanding_shop_amount'], $updatedOrder->outstandingShopAmount->getMinorAmount()->toInt());
        $this->assertEquals($assertions['order']['outstanding_customer_amount'], $updatedOrder->outstandingCustomerAmount->getMinorAmount()->toInt());

        if ($assertions['order']['completed']) {
            $this->assertEquals(Date::now(), $updatedOrder->completedAt);
            $this->assertEquals(OrderStatus::COMPLETED, $updatedOrder->status);

            Event::assertDispatched(
                OrderCompletedEvent::class,
                function (OrderCompletedEvent $event) use ($updatedOrder) {
                    $this->assertEquals($updatedOrder->id, $event->order->id);

                    return true;
                }
            );
        } else {
            $this->assertNull($updatedOrder->completedAt);
            $this->assertEquals($order->status, $updatedOrder->status);
            Event::assertNothingDispatched();
        }
    }
}
