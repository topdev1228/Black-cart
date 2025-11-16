<?php
declare(strict_types=1);

namespace App\Domain\Billings\Listeners;

use App\Domain\Billings\Services\SubscriptionService;
use App\Domain\Billings\Values\StoreDeletedEvent;

class CancelActiveSubscriptionsOnStoreDeletedListener
{
    /**
     * Create the event listener.
     */
    public function __construct(protected SubscriptionService $subscriptionService)
    {
    }

    /**
     * Handle the event.
     */
    public function handle(StoreDeletedEvent $event): void
    {
        $this->subscriptionService->cancelActiveSubscriptions();
    }
}
