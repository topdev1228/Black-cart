<?php
declare(strict_types=1);

namespace App\Domain\Orders\Listeners;

use App\Domain\Orders\Services\LineItemService;
use App\Domain\Orders\Services\OrderService;
use App\Domain\Orders\Values\TrialGroupStartedEvent;

class TrialGroupStartedListener
{
    /**
     * Create the event listener.
     */
    public function __construct(protected OrderService $orderService, protected LineItemService $lineItemService)
    {
    }

    /**
     * Handle the event.
     */
    public function handle(TrialGroupStartedEvent $event): void
    {
        $this->orderService->startTrialByTrialGroupId($event->groupKey);
    }
}
