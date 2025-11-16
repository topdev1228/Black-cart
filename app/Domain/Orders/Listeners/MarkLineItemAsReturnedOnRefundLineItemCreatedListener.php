<?php
declare(strict_types=1);

namespace App\Domain\Orders\Listeners;

use App\Domain\Orders\Enums\LineItemDecisionStatus;
use App\Domain\Orders\Services\LineItemService;
use App\Domain\Orders\Values\RefundLineItemCreatedEvent as RefundLineItemCreatedEventValue;

class MarkLineItemAsReturnedOnRefundLineItemCreatedListener
{
    /**
     * Create the event listener.
     */
    public function __construct(protected LineItemService $lineItemService)
    {
    }

    /**
     * Handle the event.
     */
    public function handle(RefundLineItemCreatedEventValue $eventData): void
    {
        $lineItem = $this->lineItemService->getBySourceId($eventData->refundLineItemValue->lineItemId);
        $this->lineItemService->setDecisionStatus($lineItem, LineItemDecisionStatus::RETURNED);
    }
}
