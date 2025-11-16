<?php
declare(strict_types=1);

namespace App\Domain\Orders\Listeners;

use App\Domain\Orders\Jobs\SendAssumedDeliveryMerchantEmailJob;
use App\Domain\Orders\Services\OrderService;
use App\Domain\Orders\Values\OrderCreatedEvent as OrderCreatedEventValue;

class SendAssumedDeliveryMerchantEmailAfterOrderCreatedListener
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
        SendAssumedDeliveryMerchantEmailJob::dispatch($event->order)->delay(now()->addDays(8));
    }
}
