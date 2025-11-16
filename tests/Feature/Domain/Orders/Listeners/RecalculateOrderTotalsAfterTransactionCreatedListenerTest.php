<?php
declare(strict_types=1);

namespace Tests\Feature\Domain\Orders\Listeners;

use App\Domain\Orders\Enums\OrderStatus;
use App\Domain\Orders\Enums\TransactionKind;
use App\Domain\Orders\Events\OrderCompletedEvent;
use App\Domain\Orders\Listeners\RecalculateOrderTotalsAfterTransactionCreatedListener;
use App\Domain\Orders\Models\Order;
use App\Domain\Orders\Models\Refund;
use App\Domain\Orders\Models\Transaction;
use App\Domain\Orders\Values\Order as OrderValue;
use App\Domain\Orders\Values\Transaction as TransactionValue;
use App\Domain\Orders\Values\TransactionCreatedEvent;
use App\Domain\Stores\Models\Store;
use Event;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Date;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\Fixtures\Domains\Orders\Traits\OrderRecalculateTotalsData;
use Tests\TestCase;

class RecalculateOrderTotalsAfterTransactionCreatedListenerTest extends TestCase
{
    use OrderRecalculateTotalsData;

    protected Store $store;

    protected function setUp(): void
    {
        parent::setUp();

        $this->store = Store::withoutEvents(function () {
            return Store::factory()->create();
        });
        App::context(store: $this->store);
    }

    #[DataProvider('orderRecalculateTotalsDataProvider')]
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
        foreach ($refunds as $refund) {
            Refund::withoutEvents(function () use ($order, $refund) {
                return Refund::factory()->for($order)->state($refund)->create([
                    'store_id' => $this->store->id,
                ]);
            });
        }

        $newestTransaction = null;
        foreach ($transactions as $transaction) {
            $newestTransaction = Transaction::withoutEvents(function () use ($order, $transaction) {
                return Transaction::factory()->for($order)->state($transaction)->create([
                    'store_id' => $this->store->id,
                ]);
            });
        }

        $listener = resolve(RecalculateOrderTotalsAfterTransactionCreatedListener::class);
        $listener->handle(new TransactionCreatedEvent(TransactionValue::from($newestTransaction)));

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

    public function testItRecalculatesOrderTotalsAfterOneCaptureTransaction(): void
    {
        $order = Order::withoutEvents(function () {
            return Order::factory()->create(
                [
                    'store_id' => $this->store->id,
                    'total_shop_amount' => 1500,
                    'total_customer_amount' => 1500,
                ]
            );
        });

        $transaction = Transaction::withoutEvents(function () use ($order) {
            return Transaction::factory()->create(
                [
                    'order_id' => $order->id,
                    'store_id' => $this->store->id,
                    'kind' => TransactionKind::CAPTURE,
                    'customer_amount' => 800,
                    'shop_amount' => 800,
                ]
            );
        });

        $listener = resolve(RecalculateOrderTotalsAfterTransactionCreatedListener::class);
        $listener->handle(new TransactionCreatedEvent(TransactionValue::from($transaction)));

        $order->refresh();

        $this->assertEquals(700, $order->outstanding_shop_amount->getMinorAmount()->toInt());
        $this->assertEquals(700, $order->outstanding_customer_amount->getMinorAmount()->toInt());
    }

    public function testItDoesntRecalculatesOrderTotalsAfterOneAuthTransaction(): void
    {
        $order = Order::withoutEvents(function () {
            return Order::factory()->create(
                [
                    'store_id' => $this->store->id,
                    'total_shop_amount' => 1500,
                    'total_customer_amount' => 1500,
                ]
            );
        });

        $transaction = Transaction::withoutEvents(function () use ($order) {
            return TransactionValue::builder()->create(
                [
                    'order_id' => $order->id,
                    'kind' => TransactionKind::AUTHORIZATION,
                    'customer_amount' => 800,
                    'shop_amount' => 800,
                ]
            );
        });

        $listener = resolve(RecalculateOrderTotalsAfterTransactionCreatedListener::class);
        $listener->handle(new TransactionCreatedEvent(TransactionValue::from($transaction)));

        $order->refresh();

        $this->assertEquals(1500, $order->outstanding_shop_amount->getMinorAmount()->toInt());
        $this->assertEquals(1500, $order->outstanding_customer_amount->getMinorAmount()->toInt());
    }

    public function testItRecalculatesOrderTotalsAfterSaleAndCaptureTransactionsAndCompletesOrder(): void
    {
        Event::fake([
            OrderCompletedEvent::class,
        ]);

        $order = Order::withoutEvents(function () {
            return Order::factory()->create(
                [
                    'store_id' => $this->store->id,
                    'total_shop_amount' => 1500,
                    'total_customer_amount' => 1500,
                ]
            );
        });

        Transaction::withoutEvents(function () use ($order) {
            return Transaction::factory()->create(
                [
                    'order_id' => $order->id,
                    'store_id' => $this->store->id,
                    'kind' => TransactionKind::SALE,
                    'customer_amount' => 800,
                    'shop_amount' => 800,
                ]
            );
        });

        $authTransaction = Transaction::withoutEvents(function () use ($order) {
            return Transaction::factory()->create(
                [
                    'order_id' => $order->id,
                    'store_id' => $this->store->id,
                    'kind' => TransactionKind::AUTHORIZATION,
                    'customer_amount' => 700,
                    'shop_amount' => 700,
                    'authorization_expires_at' => Date::now()->addDays(7),
                ]
            );
        });

        $transaction = Transaction::withoutEvents(function () use ($order, $authTransaction) {
            return Transaction::factory()->create(
                [
                    'order_id' => $order->id,
                    'store_id' => $this->store->id,
                    'kind' => TransactionKind::CAPTURE,
                    'customer_amount' => 700,
                    'shop_amount' => 700,
                    'parent_transaction_id' => $authTransaction->id,
                    'parent_transaction_source_id' => $authTransaction->source_id,
                ]
            );
        });

        $listener = resolve(RecalculateOrderTotalsAfterTransactionCreatedListener::class);
        $listener->handle(new TransactionCreatedEvent(TransactionValue::from($transaction)));

        $order->refresh();

        Event::assertDispatched(
            OrderCompletedEvent::class,
            function (OrderCompletedEvent $event) use ($order) {
                return $event->order->id === $order->id;
            }
        );
        $this->assertEquals(0, $order->outstanding_shop_amount->getMinorAmount()->toInt());
        $this->assertEquals(0, $order->outstanding_customer_amount->getMinorAmount()->toInt());
        $this->assertEquals(OrderStatus::COMPLETED, $order->status);
        $this->assertNotNull($order->completed_at);
    }

    public function testItCompletesOrderWhenCustomerAmountIsZero(): void
    {
        Event::fake([
            OrderCompletedEvent::class,
        ]);

        $order = Order::withoutEvents(function () {
            return Order::factory()->create(
                [
                    'store_id' => $this->store->id,
                    'total_shop_amount' => 1500,
                    'total_customer_amount' => 1500,
                ]
            );
        });

        Transaction::withoutEvents(function () use ($order) {
            return Transaction::factory()->create(
                [
                    'order_id' => $order->id,
                    'store_id' => $this->store->id,
                    'kind' => TransactionKind::SALE,
                    'customer_amount' => 800,
                    'shop_amount' => 800,
                ]
            );
        });

        $authTransaction = Transaction::withoutEvents(function () use ($order) {
            return Transaction::factory()->create(
                [
                    'order_id' => $order->id,
                    'store_id' => $this->store->id,
                    'kind' => TransactionKind::AUTHORIZATION,
                    'customer_amount' => 700,
                    'shop_amount' => 700,
                    'authorization_expires_at' => Date::now()->addDays(7),
                ]
            );
        });

        $transaction = Transaction::withoutEvents(function () use ($order, $authTransaction) {
            return Transaction::factory()->create(
                [
                    'order_id' => $order->id,
                    'store_id' => $this->store->id,
                    'kind' => TransactionKind::CAPTURE,
                    'customer_amount' => 700,
                    'shop_amount' => 690,
                    'parent_transaction_id' => $authTransaction->id,
                    'parent_transaction_source_id' => $authTransaction->source_id,
                ]
            );
        });

        $listener = resolve(RecalculateOrderTotalsAfterTransactionCreatedListener::class);
        $listener->handle(new TransactionCreatedEvent(TransactionValue::from($transaction)));

        $order->refresh();

        Event::assertDispatched(
            OrderCompletedEvent::class,
            function (OrderCompletedEvent $event) use ($order) {
                return $event->order->id === $order->id;
            }
        );
        $this->assertEquals(10, $order->outstanding_shop_amount->getMinorAmount()->toInt());
        $this->assertEquals(0, $order->outstanding_customer_amount->getMinorAmount()->toInt());
        $this->assertEquals(OrderStatus::COMPLETED, $order->status);
        $this->assertNotNull($order->completed_at);
    }

    public function testItCompletesOrderWhenCustomerAmountIsLessThanZero(): void
    {
        Event::fake([
            OrderCompletedEvent::class,
        ]);

        $order = Order::withoutEvents(function () {
            return Order::factory()->create(
                [
                    'store_id' => $this->store->id,
                    'total_shop_amount' => 1500,
                    'total_customer_amount' => 1500,
                ]
            );
        });

        Transaction::withoutEvents(function () use ($order) {
            return Transaction::factory()->create(
                [
                    'order_id' => $order->id,
                    'store_id' => $this->store->id,
                    'kind' => TransactionKind::SALE,
                    'customer_amount' => 800,
                    'shop_amount' => 800,
                ]
            );
        });

        $authTransaction = Transaction::withoutEvents(function () use ($order) {
            return Transaction::factory()->create(
                [
                    'order_id' => $order->id,
                    'store_id' => $this->store->id,
                    'kind' => TransactionKind::AUTHORIZATION,
                    'customer_amount' => 800,
                    'shop_amount' => 690,
                    'authorization_expires_at' => Date::now()->addDays(7),
                ]
            );
        });

        $transaction = Transaction::withoutEvents(function () use ($order, $authTransaction) {
            return Transaction::factory()->create(
                [
                    'order_id' => $order->id,
                    'store_id' => $this->store->id,
                    'kind' => TransactionKind::CAPTURE,
                    'customer_amount' => 701,
                    'shop_amount' => 690,
                    'parent_transaction_id' => $authTransaction->id,
                    'parent_transaction_source_id' => $authTransaction->source_id,
                ]
            );
        });

        $listener = resolve(RecalculateOrderTotalsAfterTransactionCreatedListener::class);
        $listener->handle(new TransactionCreatedEvent(TransactionValue::from($transaction)));

        $order->refresh();

        Event::assertDispatched(
            OrderCompletedEvent::class,
            function (OrderCompletedEvent $event) use ($order) {
                return $event->order->id === $order->id;
            }
        );
        $this->assertEquals(10, $order->outstanding_shop_amount->getMinorAmount()->toInt());
        $this->assertEquals(-1, $order->outstanding_customer_amount->getMinorAmount()->toInt());
        $this->assertEquals(OrderStatus::COMPLETED, $order->status);
        $this->assertNotNull($order->completed_at);
    }

    public function testItCompletesOrderWhenShopAmountIsZero(): void
    {
        Event::fake([
            OrderCompletedEvent::class,
        ]);

        $order = Order::withoutEvents(function () {
            return Order::factory()->create(
                [
                    'store_id' => $this->store->id,
                    'total_shop_amount' => 1500,
                    'total_customer_amount' => 1500,
                ]
            );
        });

        Transaction::withoutEvents(function () use ($order) {
            return Transaction::factory()->create(
                [
                    'order_id' => $order->id,
                    'store_id' => $this->store->id,
                    'kind' => TransactionKind::SALE,
                    'customer_amount' => 800,
                    'shop_amount' => 800,
                ]
            );
        });

        $authTransaction = Transaction::withoutEvents(function () use ($order) {
            return Transaction::factory()->create(
                [
                    'order_id' => $order->id,
                    'store_id' => $this->store->id,
                    'kind' => TransactionKind::AUTHORIZATION,
                    'customer_amount' => 700,
                    'shop_amount' => 700,
                    'authorization_expires_at' => Date::now()->addDays(7),
                ]
            );
        });

        $transaction = Transaction::withoutEvents(function () use ($order, $authTransaction) {
            return Transaction::factory()->create(
                [
                    'order_id' => $order->id,
                    'store_id' => $this->store->id,
                    'kind' => TransactionKind::CAPTURE,
                    'customer_amount' => 700,
                    'shop_amount' => 690,
                    'parent_transaction_id' => $authTransaction->id,
                    'parent_transaction_source_id' => $authTransaction->source_id,
                ]
            );
        });

        $listener = resolve(RecalculateOrderTotalsAfterTransactionCreatedListener::class);
        $listener->handle(new TransactionCreatedEvent(TransactionValue::from($transaction)));

        $order->refresh();

        Event::assertDispatched(
            OrderCompletedEvent::class,
            function (OrderCompletedEvent $event) use ($order) {
                return $event->order->id === $order->id;
            }
        );
        $this->assertEquals(10, $order->outstanding_shop_amount->getMinorAmount()->toInt());
        $this->assertEquals(0, $order->outstanding_customer_amount->getMinorAmount()->toInt());
        $this->assertEquals(OrderStatus::COMPLETED, $order->status);
        $this->assertNotNull($order->completed_at);
    }

    public function testItCompletesOrderWhenShopAmountIsLessThanZero(): void
    {
        Event::fake([
            OrderCompletedEvent::class,
        ]);

        $order = Order::withoutEvents(function () {
            return Order::factory()->create(
                [
                    'store_id' => $this->store->id,
                    'total_shop_amount' => 1500,
                    'total_customer_amount' => 1500,
                ]
            );
        });

        Transaction::withoutEvents(function () use ($order) {
            return Transaction::factory()->create(
                [
                    'order_id' => $order->id,
                    'store_id' => $this->store->id,
                    'kind' => TransactionKind::SALE,
                    'customer_amount' => 800,
                    'shop_amount' => 800,
                ]
            );
        });

        $authTransaction = Transaction::withoutEvents(function () use ($order) {
            return Transaction::factory()->create(
                [
                    'order_id' => $order->id,
                    'store_id' => $this->store->id,
                    'kind' => TransactionKind::AUTHORIZATION,
                    'customer_amount' => 800,
                    'shop_amount' => 800,
                    'authorization_expires_at' => Date::now()->addDays(7),
                ]
            );
        });

        $transaction = Transaction::withoutEvents(function () use ($order, $authTransaction) {
            return Transaction::factory()->create(
                [
                    'order_id' => $order->id,
                    'store_id' => $this->store->id,
                    'kind' => TransactionKind::CAPTURE,
                    'customer_amount' => 690,
                    'shop_amount' => 701,
                    'parent_transaction_id' => $authTransaction->id,
                    'parent_transaction_source_id' => $authTransaction->source_id,
                ]
            );
        });

        $listener = resolve(RecalculateOrderTotalsAfterTransactionCreatedListener::class);
        $listener->handle(new TransactionCreatedEvent(TransactionValue::from($transaction)));

        $order->refresh();

        Event::assertDispatched(
            OrderCompletedEvent::class,
            function (OrderCompletedEvent $event) use ($order) {
                return $event->order->id === $order->id;
            }
        );
        $this->assertEquals(-1, $order->outstanding_shop_amount->getMinorAmount()->toInt());
        $this->assertEquals(10, $order->outstanding_customer_amount->getMinorAmount()->toInt());
        $this->assertEquals(OrderStatus::COMPLETED, $order->status);
        $this->assertNotNull($order->completed_at);
    }
}
