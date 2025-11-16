<?php
declare(strict_types=1);

namespace App\Domain\Billings\Listeners;

use App\Domain\Billings\Services\UsageConfigService;
use App\Domain\Billings\Values\SubscriptionActivatedEvent;
use App\Domain\Billings\Values\UsageConfig;

class CreateBillingConfigAfterSubscriptionActivatedListener
{
    /**
     * Create the event listener.
     */
    public function __construct(protected UsageConfigService $usageConfigService)
    {
    }

    public function handle(SubscriptionActivatedEvent $event): ?UsageConfig
    {
        return $this->usageConfigService->createForSubscription($event->subscription);
    }
}
