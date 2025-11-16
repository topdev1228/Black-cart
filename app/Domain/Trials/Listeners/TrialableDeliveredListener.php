<?php
declare(strict_types=1);

namespace App\Domain\Trials\Listeners;

use App\Domain\Trials\Services\TrialService;
use App\Domain\Trials\Values\TrialableDeliveredEvent;

class TrialableDeliveredListener
{
    /**
     * Create the event listener.
     */
    public function __construct(protected TrialService $trialService)
    {
    }

    /**
     * Handle the event.
     */
    public function handle(TrialableDeliveredEvent $event): void
    {
        $trialable = $this->trialService->getBySource($event->trialable->sourceId, $event->trialable->sourceKey);

        $this->trialService->updateCondition($trialable, 'delivery');
    }
}
