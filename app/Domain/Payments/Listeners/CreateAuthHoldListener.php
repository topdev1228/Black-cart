<?php
declare(strict_types=1);

namespace App\Domain\Payments\Listeners;

use App\Domain\Payments\Jobs\CreateInitialAuthHoldJob;
use App\Domain\Payments\Services\PaymentService;
use App\Domain\Payments\Values\OrderCreatedEvent;

class CreateAuthHoldListener
{
    public function __construct(protected PaymentService $paymentService)
    {
    }

    /**
     * @see \App\Domain\Orders\Events\OrderCreatedEvent
     */
    public function handle(OrderCreatedEvent $event): void
    {
        CreateInitialAuthHoldJob::dispatch($event->order);
    }
}
