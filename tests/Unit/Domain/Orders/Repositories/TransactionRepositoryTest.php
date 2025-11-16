<?php
declare(strict_types=1);

namespace Tests\Unit\Domain\Orders\Repositories;

use App;
use App\Domain\Orders\Enums\TransactionKind;
use App\Domain\Orders\Enums\TransactionStatus;
use App\Domain\Orders\Events\TransactionCreatedEvent;
use App\Domain\Orders\Models\Order;
use App\Domain\Orders\Models\Transaction;
use App\Domain\Orders\Repositories\TransactionRepository;
use App\Domain\Orders\Values\Transaction as TransactionValue;
use App\Domain\Stores\Models\Store;
use Carbon\CarbonImmutable;
use Event;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Tests\TestCase;

class TransactionRepositoryTest extends TestCase
{
    protected Store $currentStore;
    protected TransactionRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->currentStore = Store::factory()->create();
        App::context(store: $this->currentStore);
        $this->repository = resolve(TransactionRepository::class);
    }

    public function testItGetsTransactionBySourceId(): void
    {
        $order = Order::withoutEvents(function () {
            return Order::factory()->create([
                'store_id' => $this->currentStore->id,
            ]);
        });
        $transaction = Transaction::withoutEvents(function () use ($order) {
            return Transaction::factory()->for($order)->create([
                'store_id' => $this->currentStore->id,
            ]);
        });

        $actualTransaction = $this->repository->getBySourceId($transaction->source_id);

        $this->assertEquals($transaction->id, $actualTransaction->id);
    }

    public function testFirstOrCreatesWithExistingTransaction(): void
    {
        Event::fake([
            TransactionCreatedEvent::class,
        ]);

        $order = Order::withoutEvents(function () {
            return Order::factory()->create([
                'store_id' => $this->currentStore->id,
            ]);
        });
        $transaction = Transaction::withoutEvents(function () use ($order) {
            return Transaction::factory()->for($order)->create([
                'store_id' => $this->currentStore->id,
            ]);
        });

        $this->assertDatabaseCount('orders_transactions', 1);

        $existingTransaction = $this->repository->firstOrCreate(TransactionValue::from($transaction));

        $this->assertEquals($transaction->id, $existingTransaction->id);
        $this->assertDatabaseCount('orders_transactions', 1);

        Event::assertNotDispatched(TransactionCreatedEvent::class);
    }

    public function testFirstOrCreatesWithNewTransaction(): void
    {
        Event::fake([
            TransactionCreatedEvent::class,
        ]);

        $order = Order::withoutEvents(function () {
            return Order::factory()->create([
                'store_id' => $this->currentStore->id,
            ]);
        });
        $transactionValue = TransactionValue::builder()->create([
            'store_id' => $this->currentStore->id,
            'order_id' => $order->id,
            'order_name' => $order->name,
            'source_order_id' => $order->source_id,
        ]);

        $this->assertDatabaseCount('orders_transactions', 0);

        $actualTransaction = $this->repository->firstOrCreate($transactionValue);

        $this->assertDatabaseCount('orders_transactions', 1);

        $this->assertDatabaseHas('orders_transactions', [
            'id' => $actualTransaction->id,
            'source_id' => $actualTransaction->sourceId,
            'order_id' => $order->id,
            'order_name' => $order->name,
            'source_order_id' => $order->source_id,
            'store_id' => $actualTransaction->storeId,
            'kind' => $actualTransaction->kind->value,
            'gateway' => $actualTransaction->gateway,
            'payment_id' => $actualTransaction->paymentId,
            'status' => $actualTransaction->status->value,
            'transaction_source_name' => $actualTransaction->transactionSourceName,
            'shop_currency' => $actualTransaction->shopCurrency,
            'customer_currency' => $actualTransaction->customerCurrency,
            'shop_amount' => $actualTransaction->shopAmount->getMinorAmount()->toInt(),
            'customer_amount' => $actualTransaction->customerAmount->getMinorAmount()->toInt(),
            'unsettled_shop_amount' => $actualTransaction->unsettledShopAmount->getMinorAmount()->toInt(),
            'unsettled_customer_amount' => $actualTransaction->unsettledCustomerAmount->getMinorAmount()->toInt(),
        ]);

        Event::assertDispatched(
            TransactionCreatedEvent::class,
            function (TransactionCreatedEvent $event) use ($actualTransaction) {
                $this->assertEquals($actualTransaction->id, $event->transaction->id);

                return true;
            }
        );
    }

    public function testItGetLastestTransaction(): void
    {
        $store = Store::factory()->create();
        App::context(store: $store);

        Transaction::withoutEvents(function () use ($store) {
            return Transaction::factory(['order_id' => 'test-order-id', 'store_id' => $store->id])->count(5)->state(new Sequence(
                ['id' => '1', 'created_at' => CarbonImmutable::now()->subDays(2)->toDateTimeString()],
                ['id' => '2', 'created_at' => CarbonImmutable::now()->subDay()->toDateTimeString()],
                ['id' => '3', 'created_at' => CarbonImmutable::now()->toDateTimeString()],
                ['id' => '4', 'created_at' => CarbonImmutable::now()->addDays(2)->toDateTimeString()],
                ['id' => '5', 'created_at' => CarbonImmutable::now()->addDay()->toDateTimeString()],
            ))->create();
        });

        $transaction = $this->repository->getLatestTransaction('test-order-id');

        $this->assertEquals('4', $transaction->id);
    }

    public function testItGetsLatestTransactionWithKind(): void
    {
        $store = Store::factory()->create();
        App::context(store: $store);

        Transaction::withoutEvents(function () use ($store) {
            return Transaction::factory(['order_id' => 'test-order-id', 'store_id' => $store->id])->count(5)->state(new Sequence(
                ['id' => '1', 'created_at' => CarbonImmutable::now()->subDays(2)->toDateTimeString(), 'kind' => TransactionKind::SALE],
                ['id' => '2', 'created_at' => CarbonImmutable::now()->subDay()->toDateTimeString(), 'kind' => TransactionKind::AUTHORIZATION],
                ['id' => '3', 'created_at' => CarbonImmutable::now()->toDateTimeString(), 'kind' => TransactionKind::REFUND],
                ['id' => '4', 'created_at' => CarbonImmutable::now()->addDays(2)->toDateTimeString(), 'kind' => TransactionKind::CAPTURE],
                ['id' => '5', 'created_at' => CarbonImmutable::now()->addDay()->toDateTimeString(), 'kind' => TransactionKind::REFUND],
            ))->create();
        });

        $transaction = $this->repository->getLatestTransaction('test-order-id', TransactionKind::CAPTURE);
        $this->assertEquals('4', $transaction->id);

        $transaction = $this->repository->getLatestTransaction('test-order-id', TransactionKind::REFUND);
        $this->assertEquals('5', $transaction->id);

        $transaction = $this->repository->getLatestTransaction('test-order-id', TransactionKind::SALE);
        $this->assertEquals('1', $transaction->id);

        $transaction = $this->repository->getLatestTransaction('test-order-id', [TransactionKind::SALE, TransactionKind::CAPTURE]);
        $this->assertEquals('4', $transaction->id);
    }

    public function testItGetsByOrderId(): void
    {
        $store = Store::factory()->create();
        App::context(store: $store);

        Transaction::withoutEvents(function () use ($store) {
            return Transaction::factory(['order_id' => 'test-order-id', 'store_id' => $store->id])->count(5)->state(new Sequence(
                ['id' => '1', 'created_at' => CarbonImmutable::now()->subDays(2)->toDateTimeString(), 'kind' => TransactionKind::SALE],
                ['id' => '2', 'created_at' => CarbonImmutable::now()->subDay()->toDateTimeString(), 'kind' => TransactionKind::AUTHORIZATION],
                ['id' => '3', 'created_at' => CarbonImmutable::now()->toDateTimeString(), 'kind' => TransactionKind::REFUND],
                ['id' => '4', 'created_at' => CarbonImmutable::now()->addDays(2)->toDateTimeString(), 'kind' => TransactionKind::CAPTURE],
                ['id' => '5', 'created_at' => CarbonImmutable::now()->addDay()->toDateTimeString(), 'kind' => TransactionKind::REFUND],
            ))->create();
        });

        $transactions = $this->repository->getByOrderId('test-order-id');
        $this->assertCount(5, $transactions);
    }

    public function testItGetsByOrderIdWithKind(): void
    {
        $store = Store::factory()->create();
        App::context(store: $store);

        Transaction::withoutEvents(function () use ($store) {
            return Transaction::factory(['order_id' => 'test-order-id', 'store_id' => $store->id])->count(5)->state(new Sequence(
                ['id' => '1', 'created_at' => CarbonImmutable::now()->subDays(2)->toDateTimeString(), 'kind' => TransactionKind::SALE],
                ['id' => '2', 'created_at' => CarbonImmutable::now()->subDay()->toDateTimeString(), 'kind' => TransactionKind::AUTHORIZATION],
                ['id' => '3', 'created_at' => CarbonImmutable::now()->toDateTimeString(), 'kind' => TransactionKind::REFUND],
                ['id' => '4', 'created_at' => CarbonImmutable::now()->addDays(2)->toDateTimeString(), 'kind' => TransactionKind::CAPTURE],
                ['id' => '5', 'created_at' => CarbonImmutable::now()->addDay()->toDateTimeString(), 'kind' => TransactionKind::REFUND],
            ))->create();
        });

        $transactions = $this->repository->getByOrderId('test-order-id', TransactionKind::SALE);
        $this->assertCount(1, $transactions);

        $transactions = $this->repository->getByOrderId('test-order-id', TransactionKind::AUTHORIZATION);
        $this->assertCount(1, $transactions);

        $transactions = $this->repository->getByOrderId('test-order-id', TransactionKind::REFUND);
        $this->assertCount(2, $transactions);

        $transactions = $this->repository->getByOrderId('test-order-id', [TransactionKind::SALE, TransactionKind::CAPTURE]);
        $this->assertCount(2, $transactions);
    }

    public function testItGetsByDatetimeRangeAndKinds(): void
    {
        $store = Store::factory()->create();
        App::context(store: $store);

        $transactions = Transaction::withoutEvents(function () use ($store) {
            return Transaction::factory(['order_id' => 'test-order-id', 'store_id' => $store->id])->count(5)->state(new Sequence(
                ['id' => '1', 'processed_at' => CarbonImmutable::now()->subDays(2)->toDateTimeString(), 'kind' => TransactionKind::SALE],
                ['id' => '2', 'processed_at' => CarbonImmutable::now()->subDay()->toDateTimeString(), 'kind' => TransactionKind::AUTHORIZATION],
                ['id' => '3', 'processed_at' => CarbonImmutable::now()->toDateTimeString(), 'kind' => TransactionKind::REFUND],
                ['id' => '4', 'processed_at' => CarbonImmutable::now()->addDays(2)->toDateTimeString(), 'kind' => TransactionKind::CAPTURE],
                ['id' => '5', 'processed_at' => CarbonImmutable::now()->addDay()->toDateTimeString(), 'kind' => TransactionKind::SALE],
                ['id' => '6', 'processed_at' => CarbonImmutable::now()->addDay()->toDateTimeString(), 'kind' => TransactionKind::SALE, 'status' => TransactionStatus::FAILURE],
            ))->create();
        });

        $expectedTransactions = [
            $transactions[2],
            $transactions[4],
            $transactions[3],
        ];

        $actualTransactions = $this->repository->getByProcessedAtDatetimeRangeAndKinds(
            CarbonImmutable::now()->subSecond(),
            CarbonImmutable::now()->addDays(2)->addSecond(),
            [TransactionKind::SALE, TransactionKind::CAPTURE, TransactionKind::REFUND]
        );

        $this->assertEquals(count($expectedTransactions), count($actualTransactions));
        foreach ($expectedTransactions as $i => $expectedTransaction) {
            $this->assertEquals($expectedTransaction->id, $actualTransactions[$i]->id);
        }
    }

    public function testItGetsByDatetimeRangeAndEmptyKinds(): void
    {
        $store = Store::factory()->create();
        App::context(store: $store);

        $transactions = Transaction::withoutEvents(function () use ($store) {
            return Transaction::factory(['order_id' => 'test-order-id', 'store_id' => $store->id])->count(5)->state(new Sequence(
                ['id' => '1', 'processed_at' => CarbonImmutable::now()->subDays(2)->toDateTimeString(), 'kind' => TransactionKind::SALE],
                ['id' => '2', 'processed_at' => CarbonImmutable::now()->subDay()->toDateTimeString(), 'kind' => TransactionKind::AUTHORIZATION],
                ['id' => '3', 'processed_at' => CarbonImmutable::now()->toDateTimeString(), 'kind' => TransactionKind::REFUND],
                ['id' => '4', 'processed_at' => CarbonImmutable::now()->toDateTimeString(), 'kind' => TransactionKind::CAPTURE, 'status' => TransactionStatus::FAILURE],
                ['id' => '5', 'processed_at' => CarbonImmutable::now()->addDay()->toDateTimeString(), 'kind' => TransactionKind::SALE],
            ))->create();
        });

        $expectedTransactions = [
            $transactions[0],
            $transactions[1],
            $transactions[2],
        ];

        $actualTransactions = $this->repository->getByProcessedAtDatetimeRangeAndKinds(
            CarbonImmutable::now()->subDays(2),
            CarbonImmutable::now()->addSecond()
        );

        $this->assertEquals(count($expectedTransactions), count($actualTransactions));
        foreach ($expectedTransactions as $i => $expectedTransaction) {
            $this->assertEquals($expectedTransaction->id, $actualTransactions[$i]->id);
        }
    }

    public function testItGetsByDatetimeRangeAndSingleKind(): void
    {
        $store = Store::factory()->create();
        App::context(store: $store);

        $transactions = Transaction::withoutEvents(function () use ($store) {
            return Transaction::factory(['order_id' => 'test-order-id', 'store_id' => $store->id])->count(5)->state(new Sequence(
                ['id' => '1', 'processed_at' => CarbonImmutable::now()->subDays(2)->toDateTimeString(), 'kind' => TransactionKind::SALE],
                ['id' => '2', 'processed_at' => CarbonImmutable::now()->subDay()->toDateTimeString(), 'kind' => TransactionKind::AUTHORIZATION],
                ['id' => '3', 'processed_at' => CarbonImmutable::now()->toDateTimeString(), 'kind' => TransactionKind::REFUND],
                ['id' => '4', 'processed_at' => CarbonImmutable::now()->addDays(2)->toDateTimeString(), 'kind' => TransactionKind::CAPTURE],
                ['id' => '5', 'processed_at' => CarbonImmutable::now()->addDay()->toDateTimeString(), 'kind' => TransactionKind::SALE],
                ['id' => '6', 'processed_at' => CarbonImmutable::now()->addDay()->toDateTimeString(), 'kind' => TransactionKind::SALE, 'status' => TransactionStatus::FAILURE],
            ))->create();
        });

        $expectedTransaction = $transactions[4];

        $actualTransactions = $this->repository->getByProcessedAtDatetimeRangeAndKinds(
            CarbonImmutable::now()->subSecond(),
            CarbonImmutable::now()->addDays(2)->addSecond(),
            TransactionKind::SALE,
        );

        $this->assertEquals(1, count($actualTransactions));
        $this->assertEquals($expectedTransaction->id, $actualTransactions[0]->id);
    }

    public function testItDoesNotGetTransactionsOutsideOfDatetimeRange(): void
    {
        $store = Store::factory()->create();
        App::context(store: $store);

        Transaction::withoutEvents(function () use ($store) {
            return Transaction::factory(['order_id' => 'test-order-id', 'store_id' => $store->id])->count(5)->state(new Sequence(
                ['id' => '1', 'processed_at' => CarbonImmutable::now()->subDays(5)->toDateTimeString(), 'kind' => TransactionKind::SALE],
                ['id' => '2', 'processed_at' => CarbonImmutable::now()->subDays(4)->toDateTimeString(), 'kind' => TransactionKind::AUTHORIZATION],
                ['id' => '3', 'processed_at' => CarbonImmutable::now()->subDays(3)->toDateTimeString(), 'kind' => TransactionKind::REFUND],
                ['id' => '4', 'processed_at' => CarbonImmutable::now()->subDays(2)->toDateTimeString(), 'kind' => TransactionKind::CAPTURE],
                ['id' => '5', 'processed_at' => CarbonImmutable::now()->subDay()->toDateTimeString(), 'kind' => TransactionKind::SALE],
                ['id' => '6', 'processed_at' => CarbonImmutable::now()->subDay()->toDateTimeString(), 'kind' => TransactionKind::SALE, 'status' => TransactionStatus::FAILURE],
            ))->create();
        });

        $actualTransactions = $this->repository->getByProcessedAtDatetimeRangeAndKinds(
            CarbonImmutable::now()->subSecond(),
            CarbonImmutable::now()->addDays(2)->addSecond(),
            [TransactionKind::SALE, TransactionKind::CAPTURE, TransactionKind::REFUND]
        );

        $this->assertEmpty($actualTransactions);
    }
}
