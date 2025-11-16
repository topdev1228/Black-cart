<?php
declare(strict_types=1);

namespace App\Domain\Trials\Listeners;

use App\Domain\Trials\Events\TrialStartedEvent;
use App\Domain\Trials\Jobs\ExpireTrialJob;
use App\Domain\Trials\Services\TrialService;

class TrialStartedListener
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
    public function handle(TrialStartedEvent $event): void
    {
        $expiryTime = $this->trialService->calculateExpiryTime($event->trialable);
        ExpireTrialJob::dispatch($event->trialable)->delay($expiryTime);
    }
}
