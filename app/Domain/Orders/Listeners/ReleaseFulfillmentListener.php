<?php
declare(strict_types=1);

namespace App\Domain\Orders\Listeners;

use App\Domain\Orders\Services\OrderService;
use App\Domain\Orders\Values\CheckoutAuthorizationSuccessEvent;

/**
 * @see \App\Domain\Payments\Events\CheckoutAuthorizationSuccessEvent
 */
class ReleaseFulfillmentListener
{
    /**
     * Create the event listener.
     */
    public function __construct(protected OrderService $orderService)
    {
    }

    public function handle(CheckoutAuthorizationSuccessEvent $event)
    {
        $this->orderService->releaseFulfillment($event->orderId);
    }
}
