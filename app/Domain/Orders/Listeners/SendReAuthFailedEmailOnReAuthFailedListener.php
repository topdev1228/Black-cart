<?php
declare(strict_types=1);

namespace App\Domain\Orders\Listeners;

use App\Domain\Orders\Mail\ReAuthFailed;
use App\Domain\Orders\Services\OrderService;
use App\Domain\Orders\Values\ReAuthFailedEvent;
use Feature;
use Mail;

class SendReAuthFailedEmailOnReAuthFailedListener
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
    public function handle(ReAuthFailedEvent $eventData): void
    {
        if (Feature::enabled('shopify-perm-b-kill-reauth')) {
            return;
        }

        // TODO: make this a store setting to be exposed on Shoipfy Blackcart Admin
        if (Feature::enabled('shopify-perm-b-merchant-reauth-failed-email')) {
            // Merchant is sending this email
            return;
        }

        $order = $this->orderService->getBySourceId($eventData->sourceOrderId);
        Mail::to($order->customerEmail())->send(new ReAuthFailed($order, $eventData->authAmount, $eventData->authExpiry));
    }
}
