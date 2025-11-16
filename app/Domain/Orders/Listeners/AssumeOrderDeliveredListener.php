<?php
declare(strict_types=1);

namespace App\Domain\Orders\Listeners;

use App\Domain\Orders\Jobs\AssumeOrderDeliveredJob;
use App\Domain\Orders\Services\OrderService;
use App\Domain\Orders\Values\OrderCreatedEvent as OrderCreatedEventValue;

class AssumeOrderDeliveredListener
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
    public function handle(OrderCreatedEventValue $event): void
    {
        AssumeOrderDeliveredJob::dispatch($event->order)->delay(now()->addDays(10));
    }
}
