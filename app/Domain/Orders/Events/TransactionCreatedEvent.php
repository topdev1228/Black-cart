<?php
declare(strict_types=1);

namespace App\Domain\Orders\Events;

use App\Domain\Orders\Models\Transaction;
use App\Domain\Orders\Values\Transaction as TransactionValue;
use App\Domain\Shared\Traits\Broadcastable;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * @method static dispatch(Transaction $transaction)
 */
class TransactionCreatedEvent implements ShouldBroadcastNow
{
    use Dispatchable;
    use Broadcastable;
    use SerializesModels;

    public TransactionValue $transaction;

    /**
     * Create a new event instance.
     */
    public function __construct(Transaction $transaction)
    {
        $this->transaction = TransactionValue::from($transaction);
    }
}
