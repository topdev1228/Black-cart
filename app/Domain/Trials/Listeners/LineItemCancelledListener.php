<?php
declare(strict_types=1);

namespace App\Domain\Trials\Listeners;

use App\Domain\Trials\Services\TrialService;
use App\Domain\Trials\Values\LineItemCancelledEvent;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class LineItemCancelledListener
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
    public function handle(LineItemCancelledEvent $event): void
    {
        try {
            $trialable = $this->trialService->getBySource($event->lineItemId);
        } catch (ModelNotFoundException) {
            return;
        }

        $this->trialService->cancelTrial($trialable);
    }
}
