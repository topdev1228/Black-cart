<?php
declare(strict_types=1);

namespace App\Domain\Orders\Listeners;

use App\Domain\Orders\Services\OrderService;
use App\Domain\Orders\Values\LineItemSavedEvent as LineItemSavedEventValue;

class UpdateOrderStatusAfterLineItemSavedListener
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
    public function handle(LineItemSavedEventValue $event): void
    {
        $this->orderService->updateOrderStatusAfterLineItemSaved($event->lineItem->orderId);
    }
}
