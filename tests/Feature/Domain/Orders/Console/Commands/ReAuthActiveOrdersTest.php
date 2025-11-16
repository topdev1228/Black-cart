<?php
declare(strict_types=1);

namespace Tests\Feature\Domain\Orders\Console\Commands;

use App\Domain\Orders\Enums\OrderStatus;
use App\Domain\Orders\Enums\TransactionKind;
use App\Domain\Orders\Enums\TransactionStatus;
use App\Domain\Orders\Models\Order;
use App\Domain\Orders\Models\Transaction;
use App\Domain\Payments\Services\PaymentService;
use App\Domain\Stores\Models\Store;
use Carbon\CarbonImmutable;
use Exception;
use Mockery\MockInterface;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\Fixtures\Domains\Orders\Traits\ShopifyUpdatePaymentTermsResponsesTestData;
use Tests\TestCase;

class ReAuthActiveOrdersTest extends TestCase
{
    use ShopifyUpdatePaymentTermsResponsesTestData;

    #[DataProvider('activeOrderStatusesProvider')]
    public function testItReAuthorizesActiveOrdersWithNoActiveAuthorization(OrderStatus $status): void
    {
        $store = Store::withoutEvents(function () {
            return Store::factory()->create();
        });

        $order = Order::withoutEvents(function () use ($store, $status) {
            return Order::factory()->create([
                'store_id' => $store->id,
                'outstanding_customer_amount' => 1000,
                'outstanding_shop_amount' => 1000,
                'original_outstanding_customer_amount' => 1000,
                'original_outstanding_shop_amount' => 1000,
                'total_customer_amount' => 2000,
                'total_shop_amount' => 2000,
                'status' => $status,
            ]);
        });

        Transaction::withoutEvents(function () use ($order) {
            Transaction::factory()->create([
                'order_id' => $order->id,
                'store_id' => $order->store_id,
                'customer_amount' => 1000,
                'shop_amount' => 1000,
                'kind' => TransactionKind::SALE,
                'status' => TransactionStatus::SUCCESS,
                'authorization_expires_at' => null,
            ]);

            return Transaction::factory()->create([
                'order_id' => $order->id,
                'store_id' => $order->store_id,
                'customer_amount' => 1000,
                'shop_amount' => 1000,
                'kind' => TransactionKind::AUTHORIZATION,
                'status' => TransactionStatus::SUCCESS,
                'authorization_expires_at' => CarbonImmutable::now()->subDay(), // in the past
            ]);
        });

        $resultTransaction = Transaction::withoutEvents(function () use ($order) {
            return Transaction::factory()->create([
                'order_id' => $order->id,
                'store_id' => $order->store_id,
                'customer_amount' => 1000,
                'shop_amount' => 1000,
                'kind' => TransactionKind::AUTHORIZATION,
                'status' => TransactionStatus::SUCCESS,
                'authorization_expires_at' => CarbonImmutable::now()->addDay(),
            ]);
        });

        $this->mock(PaymentService::class, function (MockInterface $mock) use ($resultTransaction) {
            $mock->shouldReceive('createAuthHold')
                ->andReturn($resultTransaction);
        });

        $this->artisan('orders:re-auth-active-orders');

        $order->refresh();

        $authExpiresInTheFuture = false;
        $t = null;
        foreach ($order->transactions as $transaction) {
            if ($transaction->kind !== TransactionKind::AUTHORIZATION) {
                continue;
            }

            if ($transaction->authorization_expires_at->isFuture()) {
                $authExpiresInTheFuture = true;
                $t = $transaction;
                break;
            }
        }

        $this->assertTrue($authExpiresInTheFuture);
        $this->assertNotNull($t);
        $this->assertTrue($t->authorization_expires_at->isFuture());
    }

    #[DataProvider('activeOrderStatusesProvider')]
    public function testItDoesNotReAuthorizesActiveOrdersOnException(OrderStatus $status): void
    {
        $store = Store::withoutEvents(function () {
            return Store::factory()->create();
        });

        $order = Order::withoutEvents(function () use ($store, $status) {
            return Order::factory()->create([
                'store_id' => $store->id,
                'outstanding_customer_amount' => 0,
                'outstanding_shop_amount' => 0,
                'original_outstanding_customer_amount' => 1000,
                'original_outstanding_shop_amount' => 1000,
                'total_customer_amount' => 2000,
                'total_shop_amount' => 2000,
                'status' => $status,
            ]);
        });

        Transaction::withoutEvents(function () use ($order) {
            Transaction::factory()->create([
                'order_id' => $order->id,
                'store_id' => $order->store_id,
                'customer_amount' => 1000,
                'shop_amount' => 1000,
                'kind' => TransactionKind::SALE,
                'status' => TransactionStatus::SUCCESS,
                'authorization_expires_at' => null,
            ]);

            return Transaction::factory()->create([
                'order_id' => $order->id,
                'store_id' => $order->store_id,
                'customer_amount' => 1000,
                'shop_amount' => 1000,
                'kind' => TransactionKind::AUTHORIZATION,
                'status' => TransactionStatus::SUCCESS,
                'authorization_expires_at' => CarbonImmutable::now()->subDay(), // in the past
            ]);
        });

        $this->partialMock(PaymentService::class, function (MockInterface $mock) {
            $mock->shouldReceive('createAuthHold')
                ->once()
                ->andThrows(new Exception('test'));
        });

        $this->artisan('orders:re-auth-active-orders');
    }

    #[DataProvider('activeOrderStatusesProvider')]
    public function testItDoesNotReAuthorizesActiveOrdersWithActiveAuthorization(OrderStatus $status): void
    {
        $store = Store::withoutEvents(function () {
            return Store::factory()->create();
        });

        $order = Order::withoutEvents(function () use ($store, $status) {
            return Order::factory()->create([
                'store_id' => $store->id,
                'outstanding_customer_amount' => 1000,
                'outstanding_shop_amount' => 1000,
                'original_outstanding_customer_amount' => 1000,
                'original_outstanding_shop_amount' => 1000,
                'total_customer_amount' => 2000,
                'total_shop_amount' => 2000,
                'status' => $status,
            ]);
        });

        Transaction::withoutEvents(function () use ($order) {
            Transaction::factory()->create([
                'order_id' => $order->id,
                'store_id' => $order->store_id,
                'customer_amount' => 1000,
                'shop_amount' => 1000,
                'kind' => TransactionKind::SALE,
                'status' => TransactionStatus::SUCCESS,
                'authorization_expires_at' => null,
            ]);

            return Transaction::factory()->create([
                'order_id' => $order->id,
                'store_id' => $order->store_id,
                'customer_amount' => 1000,
                'shop_amount' => 1000,
                'kind' => TransactionKind::AUTHORIZATION,
                'status' => TransactionStatus::SUCCESS,
                'authorization_expires_at' => CarbonImmutable::now()->addDay(), // in the future
            ]);
        });

        $this->mock(PaymentService::class, function (MockInterface $mock) {
            $mock->shouldReceive('createAuthHold')
                ->never();
        });

        $this->artisan('orders:re-auth-active-orders');
    }

    public static function activeOrderStatusesProvider(): array
    {
        return [
            [OrderStatus::OPEN],
            [OrderStatus::IN_TRIAL],
        ];
    }

    #[DataProvider('inactiveOrderStatusesProvider')]
    public function testItDoesNotReAuthorizeInactiveOrders(OrderStatus $status): void
    {
        $store = Store::withoutEvents(function () {
            return Store::factory()->create();
        });

        $order = Order::withoutEvents(function () use ($store, $status) {
            return Order::factory()->create([
                'store_id' => $store->id,
                'outstanding_customer_amount' => 1000,
                'outstanding_shop_amount' => 1000,
                'original_outstanding_customer_amount' => 1000,
                'original_outstanding_shop_amount' => 1000,
                'total_customer_amount' => 2000,
                'total_shop_amount' => 2000,
                'status' => $status,
            ]);
        });

        Transaction::withoutEvents(function () use ($order) {
            Transaction::factory()->create([
                'order_id' => $order->id,
                'store_id' => $order->store_id,
                'customer_amount' => 1000,
                'shop_amount' => 1000,
                'kind' => TransactionKind::SALE,
                'status' => TransactionStatus::SUCCESS,
                'authorization_expires_at' => null,
            ]);

            return Transaction::factory()->create([
                'order_id' => $order->id,
                'store_id' => $order->store_id,
                'customer_amount' => 1000,
                'shop_amount' => 1000,
                'kind' => TransactionKind::AUTHORIZATION,
                'status' => TransactionStatus::SUCCESS,
                'authorization_expires_at' => CarbonImmutable::now()->subDay(), // in the past
            ]);
        });

        $this->mock(PaymentService::class, function (MockInterface $mock) {
            $mock->shouldReceive('createAuthHold')
                ->never();
        });

        $this->artisan('orders:re-auth-active-orders');
    }

    public static function inactiveOrderStatusesProvider(): array
    {
        return [
            [OrderStatus::CANCELLED],
            [OrderStatus::COMPLETED],
            [OrderStatus::ARCHIVED],
        ];
    }
}
