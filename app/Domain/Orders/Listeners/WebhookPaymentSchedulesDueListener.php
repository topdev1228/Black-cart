<?php
declare(strict_types=1);

namespace App\Domain\Orders\Listeners;

use App\Domain\Orders\Services\OrderService;
use App\Domain\Orders\Values\WebhookPaymentSchedulesDue;

class WebhookPaymentSchedulesDueListener
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
    public function handle(WebhookPaymentSchedulesDue $shopifyPaymentSchedulesDue): void
    {
        $this->orderService->expireTrialFromShopifyPaymentSchedule($shopifyPaymentSchedulesDue);
    }
}
