<?php
declare(strict_types=1);

namespace App\Domain\Payments\Listeners;

use App\Domain\Payments\Services\TransactionService;
use App\Domain\Payments\Values\Transaction as TransactionValue;
use App\Domain\Payments\Values\TransactionCreatedEvent as TransactionCreatedEventValue;

class SavePaymentTransactionAfterTransactionCreatedListener
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
    public function handle(TransactionCreatedEventValue $event): ?TransactionValue
    {
        return $this->transactionService->createCaptureTransaction($event->transaction);
    }
}
