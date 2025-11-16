<?php
declare(strict_types=1);

namespace Tests\Feature\Domain\Orders\Console\Commands;

use App\Domain\Orders\Enums\LineItemStatus;
use App\Domain\Orders\Enums\OrderStatus;
use App\Domain\Orders\Enums\TransactionKind;
use App\Domain\Orders\Models\LineItem;
use App\Domain\Orders\Models\Order;
use App\Domain\Orders\Models\Transaction;
use App\Domain\Stores\Models\Store;
use Tests\TestCase;

class RecalculateOutstandingAmountForAllOpenOrdersTest extends TestCase
{
    public function testItRecalculatesAllOrdersToOpen(): void
    {
        $stores = Store::withoutEvents(function () {
            return Store::factory()->count(3)->create();
        });

        $orders = collect();
        foreach ($stores as $store) {
            $orders = $orders->merge(Order::withoutEvents(function () use ($store) {
                return Order::factory()->count(3)->create([
                    'store_id' => $store->id,
                    'outstanding_customer_amount' => 0,
                    'outstanding_shop_amount' => 0,
                    'original_outstanding_customer_amount' => 35700,
                    'original_outstanding_shop_amount' => 35700,
                    'total_customer_amount' => 35700,
                    'total_shop_amount' => 35700,
                    'status' => OrderStatus::COMPLETED,
                ]);
            }));
        }

        foreach ($orders as $order) {
            Transaction::withoutEvents(function () use ($order) {
                return Transaction::factory()->create([
                    'order_id' => $order->id,
                    'store_id' => $order->store_id,
                    'customer_amount' => 10000,
                    'shop_amount' => 10000,
                    'kind' => TransactionKind::SALE,
                ]);
            });
        }

        $this->artisan('orders:recalculate-outstanding-amount-for-all-open-orders');

        foreach ($orders as $order) {
            $order->refresh();
            $this->assertEquals(25700, $order->outstanding_customer_amount->getMinorAmount()->toInt());
            $this->assertEquals(25700, $order->outstanding_shop_amount->getMinorAmount()->toInt());
            $this->assertEquals(OrderStatus::OPEN, $order->status);
        }
    }

    public function testItRecalculatesAllOrdersToInTrial(): void
    {
        $stores = Store::withoutEvents(function () {
            return Store::factory()->count(3)->create();
        });

        $orders = collect();
        foreach ($stores as $store) {
            $orders = $orders->merge(Order::withoutEvents(function () use ($store) {
                return Order::factory()->count(3)->create([
                    'store_id' => $store->id,
                    'outstanding_customer_amount' => 0,
                    'outstanding_shop_amount' => 0,
                    'original_outstanding_customer_amount' => 35700,
                    'original_outstanding_shop_amount' => 35700,
                    'total_customer_amount' => 35700,
                    'total_shop_amount' => 35700,
                    'status' => OrderStatus::COMPLETED,
                    'trial_expires_at' => now()->addDay(),
                ]);
            }));
        }

        foreach ($orders as $order) {
            Transaction::withoutEvents(function () use ($order) {
                return Transaction::factory()->create([
                    'order_id' => $order->id,
                    'store_id' => $order->store_id,
                    'customer_amount' => 10000,
                    'shop_amount' => 10000,
                    'kind' => TransactionKind::SALE,
                ]);
            });
        }

        $this->artisan('orders:recalculate-outstanding-amount-for-all-open-orders');

        foreach ($orders as $order) {
            $order->refresh();
            $this->assertEquals(25700, $order->outstanding_customer_amount->getMinorAmount()->toInt());
            $this->assertEquals(25700, $order->outstanding_shop_amount->getMinorAmount()->toInt());
            $this->assertEquals(OrderStatus::IN_TRIAL, $order->status);
        }
    }

    public function testItRecalculatesAllOrdersToInTrialNullTrialExpiryAt(): void
    {
        $stores = Store::withoutEvents(function () {
            return Store::factory()->count(3)->create();
        });

        $orders = collect();
        foreach ($stores as $store) {
            $orders = $orders->merge(Order::withoutEvents(function () use ($store) {
                return Order::factory()->count(3)->create([
                    'store_id' => $store->id,
                    'outstanding_customer_amount' => 0,
                    'outstanding_shop_amount' => 0,
                    'original_outstanding_customer_amount' => 35700,
                    'original_outstanding_shop_amount' => 35700,
                    'total_customer_amount' => 35700,
                    'total_shop_amount' => 35700,
                    'status' => OrderStatus::COMPLETED,
                    'trial_expires_at' => null,
                ]);
            }));
        }

        foreach ($orders as $order) {
            Transaction::withoutEvents(function () use ($order) {
                return Transaction::factory()->create([
                    'order_id' => $order->id,
                    'store_id' => $order->store_id,
                    'customer_amount' => 10000,
                    'shop_amount' => 10000,
                    'kind' => TransactionKind::SALE,
                ]);
            });

            LineItem::withoutEvents(function () use ($order) {
                LineItem::factory()->create([
                    'order_id' => $order->id,
                    'status' => LineItemStatus::ARCHIVED,
                ]);

                return LineItem::factory()->count(2)->create([
                    'order_id' => $order->id,
                    'status' => LineItemStatus::IN_TRIAL,
                ]);
            });
        }

        $this->artisan('orders:recalculate-outstanding-amount-for-all-open-orders');

        foreach ($orders as $order) {
            $order->refresh();
            $this->assertEquals(25700, $order->outstanding_customer_amount->getMinorAmount()->toInt());
            $this->assertEquals(25700, $order->outstanding_shop_amount->getMinorAmount()->toInt());
            $this->assertEquals(OrderStatus::IN_TRIAL, $order->status);
        }
    }

    public function testItRecalculatesAllOrdersNoChange(): void
    {
        $stores = Store::withoutEvents(function () {
            return Store::factory()->count(3)->create();
        });

        $orders = collect();
        foreach ($stores as $store) {
            $orders = $orders->merge(Order::withoutEvents(function () use ($store) {
                return Order::factory()->count(3)->create([
                    'store_id' => $store->id,
                    'outstanding_customer_amount' => 0,
                    'outstanding_shop_amount' => 0,
                    'original_outstanding_customer_amount' => 0,
                    'original_outstanding_shop_amount' => 0,
                    'total_customer_amount' => 10000,
                    'total_shop_amount' => 10000,
                    'status' => OrderStatus::COMPLETED,
                    'trial_expires_at' => now()->subDay(),
                ]);
            }));
        }

        foreach ($orders as $order) {
            Transaction::withoutEvents(function () use ($order) {
                return Transaction::factory()->create([
                    'order_id' => $order->id,
                    'store_id' => $order->store_id,
                    'customer_amount' => 10000,
                    'shop_amount' => 10000,
                    'kind' => TransactionKind::SALE,
                ]);
            });
        }

        $this->artisan('orders:recalculate-outstanding-amount-for-all-open-orders');

        foreach ($orders as $order) {
            $order->refresh();
            $this->assertEquals(0, $order->outstanding_customer_amount->getMinorAmount()->toInt());
            $this->assertEquals(0, $order->outstanding_shop_amount->getMinorAmount()->toInt());
            $this->assertEquals(OrderStatus::COMPLETED, $order->status);
        }
    }
}
