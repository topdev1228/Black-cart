<?php
declare(strict_types=1);

namespace App\Domain\Billings\Listeners;

use App\Domain\Billings\Services\ChargeService;
use App\Domain\Shared\Facades\AppMetrics;

class BillMerchantListener
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
    public function handle(): void
    {
        AppMetrics::trace('billing.bill_merchant_listener', function () {
            $this->chargeService->billCharges();
        });
    }
}
