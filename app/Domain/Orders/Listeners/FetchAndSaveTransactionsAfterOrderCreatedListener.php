<?php
declare(strict_types=1);

namespace App\Domain\Orders\Listeners;

use App\Domain\Orders\Services\TransactionService;
use App\Domain\Orders\Values\Collections\TransactionCollection;
use App\Domain\Orders\Values\OrderCreatedEvent as OrderCreatedEventValue;

class FetchAndSaveTransactionsAfterOrderCreatedListener
{
    /**
     * Create the event listener.
     */
    public function __construct(protected TransactionService $transactionService)
    {
    }

    /**
     * Handle the event.
     */
    public function handle(OrderCreatedEventValue $event): TransactionCollection
    {
        return $this->transactionService->fetchAndSaveTransactionsForOrder($event->order);
    }
}
