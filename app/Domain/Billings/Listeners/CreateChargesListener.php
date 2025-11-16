<?php
declare(strict_types=1);

namespace App\Domain\Billings\Listeners;

use App\Domain\Billings\Events\TbybNetSaleCreatedEvent;
use App\Domain\Billings\Services\ChargeService;
use App\Domain\Shared\Facades\AppMetrics;

class CreateChargesListener
{
    /**
     * Create the event listener.
     */
    public function __construct(protected ChargeService $chargeService)
    {
    }

    /**
     * Handle the event.
     */
    public function handle(TbybNetSaleCreatedEvent $event): void
    {
        AppMetrics::trace('billing.create_charges_listener', function () use ($event) {
            $this->chargeService->createCharges($event->tbybNetSale);
        });
    }
}
