<?php
declare(strict_types=1);

namespace Tests\Feature\Domain\Orders\Listeners;

use App\Domain\Orders\Enums\OrderStatus;
use App\Domain\Orders\Events\OrderCompletedEvent;
use App\Domain\Orders\Listeners\RecalculateOrderTotalsAfterRefundCreatedListener;
use App\Domain\Orders\Models\Order;
use App\Domain\Orders\Models\Refund;
use App\Domain\Orders\Models\Transaction;
use App\Domain\Orders\Values\Order as OrderValue;
use App\Domain\Orders\Values\Refund as RefundValue;
use App\Domain\Orders\Values\RefundCreatedEvent;
use App\Domain\Stores\Models\Store;
use App\Domain\Stores\Values\Store as StoreValue;
use Event;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Date;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\Fixtures\Domains\Orders\Traits\OrderRecalculateTotalsData;
use Tests\TestCase;

class RecalculateOrderTotalsAfterRefundCreatedListenerTest extends TestCase
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

    public function testItDoesNotRecalculateOrderOnOrderNotFound(): void
    {
        $this->expectException(ModelNotFoundException::class);

        $refund = Refund::withoutEvents(function () {
            return Refund::factory()->create([
                'order_id' => '12345',
                'store_id' => $this->store->id,
            ]);
        });

        $listener = resolve(RecalculateOrderTotalsAfterRefundCreatedListener::class);
        $listener->handle(new RefundCreatedEvent(RefundValue::from($refund)));
    }

    #[DataProvider('orderRecalculateTotalsDataProviderWithRefunds')]
    public function testItRecalculatesOrderTotalsAfterTransactionCreated(
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

        $newestRefund = null;
        foreach ($refunds as $refund) {
            $newestRefund = Refund::withoutEvents(function () use ($order, $refund) {
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

        $listener = resolve(RecalculateOrderTotalsAfterRefundCreatedListener::class);
        $listener->handle(new RefundCreatedEvent(RefundValue::from($newestRefund)));

        $updatedOrderValue = OrderValue::from($order->refresh());

        $this->assertEquals($assertions['order']['tbyb_refund_gross_sales_shop_amount'], $updatedOrderValue->tbybRefundGrossSalesShopAmount->getMinorAmount()->toInt());
        $this->assertEquals($assertions['order']['tbyb_refund_gross_sales_customer_amount'], $updatedOrderValue->tbybRefundGrossSalesCustomerAmount->getMinorAmount()->toInt());
        $this->assertEquals($assertions['order']['upfront_refund_gross_sales_shop_amount'], $updatedOrderValue->upfrontRefundGrossSalesShopAmount->getMinorAmount()->toInt());
        $this->assertEquals($assertions['order']['upfront_refund_gross_sales_customer_amount'], $updatedOrderValue->upfrontRefundGrossSalesCustomerAmount->getMinorAmount()->toInt());
        $this->assertEquals($assertions['order']['tbyb_refund_discounts_shop_amount'], $updatedOrderValue->tbybRefundDiscountsShopAmount->getMinorAmount()->toInt());
        $this->assertEquals($assertions['order']['tbyb_refund_discounts_customer_amount'], $updatedOrderValue->tbybRefundDiscountsCustomerAmount->getMinorAmount()->toInt());
        $this->assertEquals($assertions['order']['upfront_refund_discounts_shop_amount'], $updatedOrderValue->upfrontRefundDiscountsShopAmount->getMinorAmount()->toInt());
        $this->assertEquals($assertions['order']['upfront_refund_discounts_customer_amount'], $updatedOrderValue->upfrontRefundDiscountsCustomerAmount->getMinorAmount()->toInt());
        $this->assertEquals($assertions['order']['total_order_level_refunds_shop_amount'], $updatedOrderValue->totalOrderLevelRefundsShopAmount->getMinorAmount()->toInt());
        $this->assertEquals($assertions['order']['total_order_level_refunds_customer_amount'], $updatedOrderValue->totalOrderLevelRefundsCustomerAmount->getMinorAmount()->toInt());
        // net sales
        $this->assertEquals($assertions['order']['tbyb_net_sales_shop_amount'], $updatedOrderValue->tbybNetSalesShopAmount->getMinorAmount()->toInt());
        $this->assertEquals($assertions['order']['tbyb_net_sales_customer_amount'], $updatedOrderValue->tbybNetSalesCustomerAmount->getMinorAmount()->toInt());
        $this->assertEquals($assertions['order']['upfront_net_sales_shop_amount'], $updatedOrderValue->upfrontNetSalesShopAmount->getMinorAmount()->toInt());
        $this->assertEquals($assertions['order']['upfront_net_sales_customer_amount'], $updatedOrderValue->upfrontNetSalesCustomerAmount->getMinorAmount()->toInt());
        $this->assertEquals($assertions['order']['total_net_sales_shop_amount'], $updatedOrderValue->totalNetSalesShopAmount->getMinorAmount()->toInt());
        $this->assertEquals($assertions['order']['total_net_sales_customer_amount'], $updatedOrderValue->totalNetSalesCustomerAmount->getMinorAmount()->toInt());
        // outstanding
        $this->assertEquals($assertions['order']['outstanding_shop_amount'], $updatedOrderValue->outstandingShopAmount->getMinorAmount()->toInt());
        $this->assertEquals($assertions['order']['outstanding_customer_amount'], $updatedOrderValue->outstandingCustomerAmount->getMinorAmount()->toInt());

        if ($assertions['order']['completed']) {
            $this->assertEquals(Date::now(), $updatedOrderValue->completedAt);
            $this->assertEquals(OrderStatus::COMPLETED, $updatedOrderValue->status);

            Event::assertDispatched(
                OrderCompletedEvent::class,
                function (OrderCompletedEvent $event) use ($updatedOrderValue) {
                    $this->assertEquals($updatedOrderValue->id, $event->order->id);

                    return true;
                }
            );
        } else {
            $this->assertNull($updatedOrderValue->completedAt);
            $this->assertEquals($order->status, $updatedOrderValue->status);
            Event::assertNothingDispatched();
        }
    }
}
