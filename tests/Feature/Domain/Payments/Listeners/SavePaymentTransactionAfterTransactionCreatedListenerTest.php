<?php
declare(strict_types=1);

namespace Feature\Domain\Payments\Listeners;

use App;
use App\Domain\Payments\Listeners\SavePaymentTransactionAfterTransactionCreatedListener;
use App\Domain\Payments\Models\Transaction;
use App\Domain\Payments\Values\Transaction as TransactionValue;
use App\Domain\Payments\Values\TransactionCreatedEvent as TransactionCreatedEventValue;
use App\Domain\Stores\Models\Store;
use Tests\TestCase;

class SavePaymentTransactionAfterTransactionCreatedListenerTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->currentStore = Store::withoutEvents(function () {
            return Store::factory()->create();
        });
        App::context(store: $this->currentStore);
    }

    public function testItDoesNotCreateTransactionOnNonCaptureTransaction(): void
    {
        $transactionValue = TransactionValue::builder()->create([
            // Authorization transaction by default
            'store_id' => $this->currentStore->id,
        ]);

        $event = new TransactionCreatedEventValue($transactionValue);
        $listener = resolve(SavePaymentTransactionAfterTransactionCreatedListener::class);
        $newTransaction = $listener->handle($event);
        $this->assertNull($newTransaction);
    }

    public function testItCreatesCaptureTransactionWithoutParentTransaction(): void
    {
        $transactionValue = TransactionValue::builder()->capture()->create([
            'store_id' => $this->currentStore->id,
        ]);

        $this->assertDatabaseCount('payments_transactions', 0);

        $event = new TransactionCreatedEventValue($transactionValue);
        $listener = resolve(SavePaymentTransactionAfterTransactionCreatedListener::class);
        $newTransaction = $listener->handle($event);

        $this->assertNotEmpty($newTransaction->id);
        $this->assertDatabaseCount('payments_transactions', 1);
        $this->assertDatabaseHas('payments_transactions', [
            'source_id' => $transactionValue->sourceId,
            'parent_transaction_id' => null,
            'parent_transaction_source_id' => $transactionValue->parentTransactionSourceId,
        ]);
    }

    public function testItCreatesCaptureTransaction(): void
    {
        $parentTransaction = Transaction::withoutEvents(function () {
            return Transaction::factory()->create([
                'store_id' => $this->currentStore->id,
            ]);
        });

        $transactionValue = TransactionValue::builder()->capture()->create([
            'store_id' => $this->currentStore->id,
            'parent_transaction_source_id' => $parentTransaction->source_id,
        ]);

        $this->assertDatabaseCount('payments_transactions', 1);

        $event = new TransactionCreatedEventValue($transactionValue);
        $listener = resolve(SavePaymentTransactionAfterTransactionCreatedListener::class);
        $newTransaction = $listener->handle($event);

        $this->assertNotEmpty($newTransaction->id);
        $this->assertDatabaseCount('payments_transactions', 2);
        $this->assertDatabaseHas('payments_transactions', [
            'source_id' => $transactionValue->sourceId,
            'parent_transaction_id' => $parentTransaction->id,
            'parent_transaction_source_id' => $parentTransaction->source_id,
        ]);
        $this->assertDatabaseHas('payments_transactions', [
            'id' => $parentTransaction->id,
            'captured_transaction_id' => $newTransaction->id,
            'captured_transaction_source_id' => $newTransaction->sourceId,
        ]);
    }
}
