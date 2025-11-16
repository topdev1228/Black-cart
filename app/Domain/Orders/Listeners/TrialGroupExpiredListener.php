<?php
declare(strict_types=1);

namespace App\Domain\Orders\Listeners;

use App\Domain\Orders\Events\PaymentRequiredEvent;
use App\Domain\Orders\Services\OrderService;
use App\Domain\Orders\Values\TrialGroupExpiredEvent;

class TrialGroupExpiredListener
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
    public function handle(TrialGroupExpiredEvent $event): void
    {
        $order = $this->orderService->getByTrialGroupId($event->groupKey);
        PaymentRequiredEvent::dispatch($order->id, $order->sourceId, $event->groupKey, $order->outstandingCustomerAmount);
    }
}
