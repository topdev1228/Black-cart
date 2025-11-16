<?php
declare(strict_types=1);

namespace Tests\Unit\Domain\Payments\Repositories;

use App;
use App\Domain\Payments\Enums\TransactionKind;
use App\Domain\Payments\Enums\TransactionStatus;
use App\Domain\Payments\Models\Transaction;
use App\Domain\Payments\Repositories\TransactionRepository;
use App\Domain\Payments\Values\Transaction as TransactionValue;
use App\Domain\Stores\Models\Store;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Tests\TestCase;

class TransactionRepositoryTest extends TestCase
{
    protected Store $store;
    protected TransactionRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->store = Store::factory()->create();
        App::context(store: $this->store);
        $this->repository = resolve(TransactionRepository::class);
    }

    public function testItSavesNewTransaction(): void
    {
        $val = TransactionValue::builder()->create();
        $record = $this->repository->save($val);
        $this->assertDatabaseCount('payments_transactions', 1);
        $this->assertEquals($record->id, Transaction::first()->id);
    }

    public function testItSavesExistingTransaction(): void
    {
        $transaction = Transaction::withoutEvents(function () {
            return Transaction::factory()->create([
                'store_id' => $this->store->id,
            ]);
        });
        $this->assertDatabaseCount('payments_transactions', 1);

        $val = TransactionValue::from($transaction);
        $val->status = TransactionStatus::UNKNOWN;

        $record = $this->repository->save($val);
        $this->assertDatabaseCount('payments_transactions', 1);
        $this->assertEquals($transaction->id, $record->id);

        $transaction->refresh();
        $this->assertEquals($transaction->status, $record->status);
    }

    public function testItGetsBySourceId(): void
    {
        $transaction = Transaction::withoutEvents(function () {
            return Transaction::factory()->create([
                'store_id' => $this->store->id,
            ]);
        });

        $record = $this->repository->getBySourceId($transaction->source_id);
        $this->assertEquals($transaction->id, $record->id);
    }

    public function testItGetsLatestTransactionByOrderIdAndKind(): void
    {
        $saleTransaction = Transaction::withoutEvents(function () {
            Transaction::factory()->count(7)->state(new Sequence(
                ['kind' => TransactionKind::AUTHORIZATION],
                ['kind' => TransactionKind::CAPTURE],
                ['kind' => TransactionKind::SUGGESTED_REFUND],
                ['kind' => TransactionKind::CHANGE],
                ['kind' => TransactionKind::EMV_AUTHORIZATION],
                ['kind' => TransactionKind::VOID],
                ['kind' => TransactionKind::SUGGESTED_REFUND],
            ))->create([
                'store_id' => $this->store->id,
                'order_id' => 'test_order_id',
            ]);

            return Transaction::factory()->create([
                'store_id' => $this->store->id,
                'order_id' => 'test_order_id',
                'kind' => TransactionKind::SALE,
            ]);
        });

        $record = $this->repository->getLatestTransaction('test_order_id', TransactionKind::SALE);
        $this->assertEquals($saleTransaction->id, $record->id);
    }

    public function testItGetsLatestTransactionByOrderId(): void
    {
        $latestTransaction = Transaction::withoutEvents(function () {
            Transaction::factory()->count(7)->state(new Sequence(
                ['kind' => TransactionKind::AUTHORIZATION],
                ['kind' => TransactionKind::CAPTURE],
                ['kind' => TransactionKind::SUGGESTED_REFUND],
                ['kind' => TransactionKind::CHANGE],
                ['kind' => TransactionKind::EMV_AUTHORIZATION],
                ['kind' => TransactionKind::VOID],
                ['kind' => TransactionKind::SUGGESTED_REFUND],
            ))->create([
                'store_id' => $this->store->id,
                'order_id' => 'test_order_id',
                'created_at' => '2024-03-17 00:00:00',
            ]);

            return Transaction::factory()->create([
                'store_id' => $this->store->id,
                'order_id' => 'test_order_id',
                'kind' => TransactionKind::SALE,
                'created_at' => '2024-03-18 00:00:00',
            ]);
        });

        $record = $this->repository->getLatestTransaction('test_order_id');
        $this->assertEquals($latestTransaction->id, $record->id);
    }
}
