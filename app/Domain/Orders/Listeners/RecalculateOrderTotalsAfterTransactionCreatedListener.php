<?php
declare(strict_types=1);

namespace App\Domain\Orders\Listeners;

use App\Domain\Orders\Services\OrderService;
use App\Domain\Orders\Values\TransactionCreatedEvent as TransactionCreatedEventValue;

class RecalculateOrderTotalsAfterTransactionCreatedListener
{
    /**
     * Create the event listener.
     */
    public function __construct(protected OrderService $orderService)
    {
    }

    /**
     * Handle the event.
     */
    public function handle(TransactionCreatedEventValue $event): void
    {
        $this->orderService->recalculateOrderTotals($event->transaction->orderId);
    }
}
