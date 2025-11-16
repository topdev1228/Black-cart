<?php
declare(strict_types=1);

namespace App\Domain\Orders\Listeners;

use App\Domain\Orders\Services\OrderService;
use App\Domain\Orders\Values\ReturnCreatedEvent;

class RecalculateOrderTotalsAfterReturnCreatedListener
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
    public function handle(ReturnCreatedEvent $event): void
    {
        // Returns are no longer a part of the order totals calculations.  This is a no-op right now.
    }
}
